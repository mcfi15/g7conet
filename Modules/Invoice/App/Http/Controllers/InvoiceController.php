<?php

namespace Modules\Invoice\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Modules\Invoice\App\Models\Invoice;
use Modules\Invoice\App\Http\Requests\InvoiceRequest;
use Modules\GlobalSetting\App\Models\GlobalSetting;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::latest()->get();
        return view('invoice::index', compact('invoices'));
    }

    public function publicCreate()
    {
        if (GlobalSetting::where('key', 'invoice_status')->first()?->value != '1') {
            abort(404);
        }

        $dailyLimit = (int) (GlobalSetting::where('key', 'daily_invoice_limit')->first()?->value ?? 5);

        // Check if guest has hit the daily limit (tracked by IP)
        $limitReached = false;
        if (!auth('web')->check()) {
            $ip = request()->ip();
            $todayCount = Invoice::whereNull('user_id')
                ->whereDate('created_at', today())
                ->where('ip_address', $ip)
                ->count();
            if ($todayCount >= $dailyLimit) {
                $limitReached = true;
            }
        }

        $pageTitle = trans('translate.Invoice Generator');

        $currencyModel = app()->bound('Modules\Currency\App\Models\Currency')
            ? app('Modules\Currency\App\Models\Currency')
            : null;
        $currencies = $currencyModel ? $currencyModel::orderBy('currency_name')->get() : collect();

        return view('invoice::frontend.create', compact('pageTitle', 'currencies', 'dailyLimit', 'limitReached'));
    }

    public function create()
    {
        return view('invoice::create');
    }

    public function store(InvoiceRequest $request)
    {
        if (GlobalSetting::where('key', 'invoice_status')->first()?->value != '1') {
            abort(404);
        }

        $dailyLimit = (int) (GlobalSetting::where('key', 'daily_invoice_limit')->first()?->value ?? 5);

        // Check daily limit for guests
        if (!auth('web')->check()) {
            $ip = $request->ip();
            $todayCount = Invoice::whereNull('user_id')
                ->whereDate('created_at', today())
                ->where('ip_address', $ip)
                ->count();
            if ($todayCount >= $dailyLimit) {
                $notify_message = trans('translate.You have reached the daily limit. Please register to continue creating invoices.');
                $notify_message = ['message' => $notify_message, 'alert-type' => 'error'];
                return redirect()->back()->with($notify_message)->withInput();
            }
        }

        $items = collect($request->items)->map(function ($item) {
            return [
                'description' => $item['description'],
                'quantity' => (float) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
                'total' => (float) $item['quantity'] * (float) $item['unit_price'],
            ];
        });

        $subtotal = $items->sum('total');
        $taxPercentage = (float) ($request->tax_percentage ?? 0);
        $taxAmount = $subtotal * ($taxPercentage / 100);
        $discountAmount = (float) ($request->discount_amount ?? 0);
        $total = $subtotal + $taxAmount - $discountAmount;

        $user = auth('web')->user();

        // Set deletion schedule: 3 months for users, 24 hours for guests
        $scheduledDeletion = $user
            ? now()->addMonths(3)
            : now()->addDay();

        $invoice = Invoice::create([
            'user_id' => $user?->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'business_address' => $request->business_address,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_address' => $request->client_address,
            'items' => $items->toArray(),
            'subtotal' => $subtotal,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'note' => $request->note,
            'logo' => $request->logo,
            'currency' => $request->currency,
            'status' => 'enable',
            'scheduled_deletion_at' => $scheduledDeletion,
            'ip_address' => $request->ip(),
        ]);

        $notify_message = trans('translate.Invoice created successfully');
        $notify_message = ['message' => $notify_message, 'alert-type' => 'success'];
        return redirect()->route('invoice.show', $invoice->id)->with($notify_message);
    }

    public function show($id)
    {
        if (GlobalSetting::where('key', 'invoice_status')->first()?->value != '1') {
            abort(404);
        }
        $invoice = Invoice::findOrFail($id);
        $pageTitle = trans('translate.Invoice') . ' - ' . $invoice->invoice_number;

        $canEdit = auth('web')->check() && $invoice->isOwnedBy(auth('web')->user());

        return view('invoice::frontend.show', compact('invoice', 'pageTitle', 'canEdit'));
    }

    public function userInvoices()
    {
        $user = auth('web')->user();
        $invoices = Invoice::ownedBy($user->id)->latest()->paginate(20);
        $pageTitle = trans('translate.My Invoices');
        return view('invoice::frontend.user_invoices', compact('invoices', 'pageTitle'));
    }

    public function editOwn($id)
    {
        if (GlobalSetting::where('key', 'invoice_status')->first()?->value != '1') {
            abort(404);
        }

        if (!auth('web')->check()) {
            return Redirect::route('user.login');
        }

        $invoice = Invoice::findOrFail($id);

        if (!$invoice->isOwnedBy(auth('web')->user())) {
            abort(403);
        }

        $currencyModel = app()->bound('Modules\Currency\App\Models\Currency')
            ? app('Modules\Currency\App\Models\Currency')
            : null;
        $currencies = $currencyModel ? $currencyModel::orderBy('currency_name')->get() : collect();

        $pageTitle = trans('translate.Edit Invoice');
        return view('invoice::frontend.edit', compact('invoice', 'pageTitle', 'currencies'));
    }

    public function updateOwn(InvoiceRequest $request, $id)
    {
        if (GlobalSetting::where('key', 'invoice_status')->first()?->value != '1') {
            abort(404);
        }

        if (!auth('web')->check()) {
            return Redirect::route('user.login');
        }

        $invoice = Invoice::findOrFail($id);

        if (!$invoice->isOwnedBy(auth('web')->user())) {
            abort(403);
        }

        $items = collect($request->items)->map(function ($item) {
            return [
                'description' => $item['description'],
                'quantity' => (float) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
                'total' => (float) $item['quantity'] * (float) $item['unit_price'],
            ];
        });

        $subtotal = $items->sum('total');
        $taxPercentage = (float) ($request->tax_percentage ?? 0);
        $taxAmount = $subtotal * ($taxPercentage / 100);
        $discountAmount = (float) ($request->discount_amount ?? 0);
        $total = $subtotal + $taxAmount - $discountAmount;

        $invoice->update([
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'business_address' => $request->business_address,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_address' => $request->client_address,
            'items' => $items->toArray(),
            'subtotal' => $subtotal,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'note' => $request->note,
            'logo' => $request->logo,
            'currency' => $request->currency,
        ]);

        $notify_message = trans('translate.Invoice updated successfully');
        $notify_message = ['message' => $notify_message, 'alert-type' => 'success'];
        return redirect()->route('invoice.show', $invoice->id)->with($notify_message);
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        $notify_message = trans('translate.Invoice deleted successfully');
        $notify_message = ['message' => $notify_message, 'alert-type' => 'success'];
        return redirect()->route('admin.invoice.index')->with($notify_message);
    }

    public function toggleStatus($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->status = $invoice->status === 'enable' ? 'disable' : 'enable';
        $invoice->save();

        return response()->json([
            'status' => $invoice->status,
            'message' => trans('translate.Status updated successfully')
        ]);
    }

    public function toggleFeature(Request $request)
    {
        $status = $request->status ? 1 : 0;
        GlobalSetting::where('key', 'invoice_status')->update(['value' => $status]);

        Cache::forget('setting');
        $setting_data = GlobalSetting::get();
        $setting = [];
        foreach ($setting_data as $data_item) {
            $setting[$data_item->key] = $data_item->value;
        }
        Cache::put('setting', (object) $setting);

        return response()->json([
            'status' => $status,
            'message' => trans('translate.Status updated successfully')
        ]);
    }

    public function saveDailyLimit(Request $request)
    {
        $request->validate(['limit' => 'required|integer|min:1|max:100']);
        GlobalSetting::where('key', 'daily_invoice_limit')->update(['value' => $request->limit]);

        Cache::forget('setting');
        $setting_data = GlobalSetting::get();
        $setting = [];
        foreach ($setting_data as $data_item) {
            $setting[$data_item->key] = $data_item->value;
        }
        Cache::put('setting', (object) $setting);

        return response()->json([
            'message' => trans('translate.Daily limit updated successfully')
        ]);
    }
}
