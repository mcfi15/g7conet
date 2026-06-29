<?php

namespace Modules\QRCode\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Modules\QRCode\App\Models\QRCode;
use Modules\QRCode\App\Http\Requests\QRCodeRequest;
use Modules\GlobalSetting\App\Models\GlobalSetting;

class QRCodeController extends Controller
{
    public function index()
    {
        $qrCodes = QRCode::latest()->get();
        return view('qrcode::index', compact('qrCodes'));
    }

    public function publicCreate()
    {
        if (GlobalSetting::where('key', 'qr_code_status')->first()?->value != '1') {
            abort(404);
        }

        $dailyLimit = (int) (GlobalSetting::where('key', 'daily_qr_code_limit')->first()?->value ?? 5);

        $limitReached = false;
        if (!auth('web')->check()) {
            $ip = request()->ip();
            $todayCount = QRCode::whereNull('user_id')
                ->whereDate('created_at', today())
                ->where('ip_address', $ip)
                ->count();
            if ($todayCount >= $dailyLimit) {
                $limitReached = true;
            }
        }

        $pageTitle = trans('translate.QR Code Generator');
        return view('qrcode::frontend.create', compact('pageTitle', 'dailyLimit', 'limitReached'));
    }

    public function store(QRCodeRequest $request)
    {
        if (GlobalSetting::where('key', 'qr_code_status')->first()?->value != '1') {
            abort(404);
        }

        $dailyLimit = (int) (GlobalSetting::where('key', 'daily_qr_code_limit')->first()?->value ?? 5);

        if (!auth('web')->check()) {
            $ip = $request->ip();
            $todayCount = QRCode::whereNull('user_id')
                ->whereDate('created_at', today())
                ->where('ip_address', $ip)
                ->count();
            if ($todayCount >= $dailyLimit) {
                $notify_message = trans('translate.You have reached the daily limit. Please register to continue creating QR codes.');
                $notify_message = ['message' => $notify_message, 'alert-type' => 'error'];
                return redirect()->back()->with($notify_message)->withInput();
            }
        }

        $user = auth('web')->user();

        $scheduledDeletion = $user
            ? now()->addMonths(3)
            : now()->addDay();

        $data = [
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'content' => $request->content,
            'foreground_color' => $request->foreground_color ?? '#000000',
            'background_color' => $request->background_color ?? '#FFFFFF',
            'size' => (int) ($request->size ?? 300),
            'format' => $request->format ?? 'png',
            'status' => 'enable',
            'scheduled_deletion_at' => $scheduledDeletion,
        ];

        $designFields = ['dot_style','eye_style','eye_border_color','eye_center_color',
            'gradient_enabled','gradient_start','gradient_end',
            'frame_style','frame_text','frame_color','frame_text_color',
            'frame_margin','frame_font_size'];
        foreach ($designFields as $f) {
            if ($request->has($f)) {
                $data[$f] = $request->$f;
            }
        }

        $qrCode = QRCode::create($data);

        $notify_message = trans('translate.QR Code created successfully');
        $notify_message = ['message' => $notify_message, 'alert-type' => 'success'];
        return redirect()->route('qrcode.show', $qrCode->id)->with($notify_message);
    }

    public function show($id)
    {
        if (GlobalSetting::where('key', 'qr_code_status')->first()?->value != '1') {
            abort(404);
        }
        $qrCode = QRCode::findOrFail($id);
        $pageTitle = trans('translate.QR Code') . ' - ' . $qrCode->content;

        $canEdit = auth('web')->check() && $qrCode->isOwnedBy(auth('web')->user());

        return view('qrcode::frontend.show', compact('qrCode', 'pageTitle', 'canEdit'));
    }

    public function userQRCodes()
    {
        $user = auth('web')->user();
        $qrCodes = QRCode::ownedBy($user->id)->latest()->paginate(20);
        $pageTitle = trans('translate.My QR Codes');
        return view('qrcode::frontend.user_qrcodes', compact('qrCodes', 'pageTitle'));
    }

    public function editOwn($id)
    {
        if (GlobalSetting::where('key', 'qr_code_status')->first()?->value != '1') {
            abort(404);
        }

        if (!auth('web')->check()) {
            return Redirect::route('user.login');
        }

        $qrCode = QRCode::findOrFail($id);

        if (!$qrCode->isOwnedBy(auth('web')->user())) {
            abort(403);
        }

        $pageTitle = trans('translate.Edit QR Code');
        return view('qrcode::frontend.edit', compact('qrCode', 'pageTitle'));
    }

    public function updateOwn(QRCodeRequest $request, $id)
    {
        if (GlobalSetting::where('key', 'qr_code_status')->first()?->value != '1') {
            abort(404);
        }

        if (!auth('web')->check()) {
            return Redirect::route('user.login');
        }

        $qrCode = QRCode::findOrFail($id);

        if (!$qrCode->isOwnedBy(auth('web')->user())) {
            abort(403);
        }

        $data = [
            'content' => $request->content,
            'foreground_color' => $request->foreground_color ?? '#000000',
            'background_color' => $request->background_color ?? '#FFFFFF',
            'size' => (int) ($request->size ?? 300),
            'format' => $request->format ?? 'png',
        ];

        $designFields = ['dot_style','eye_style','eye_border_color','eye_center_color',
            'gradient_enabled','gradient_start','gradient_end',
            'frame_style','frame_text','frame_color','frame_text_color',
            'frame_margin','frame_font_size'];
        foreach ($designFields as $f) {
            if ($request->has($f)) {
                $data[$f] = $request->$f;
            }
        }

        $qrCode->update($data);

        $notify_message = trans('translate.QR Code updated successfully');
        $notify_message = ['message' => $notify_message, 'alert-type' => 'success'];
        return redirect()->route('qrcode.show', $qrCode->id)->with($notify_message);
    }

    public function destroy($id)
    {
        $qrCode = QRCode::findOrFail($id);
        $qrCode->delete();

        $notify_message = trans('translate.QR Code deleted successfully');
        $notify_message = ['message' => $notify_message, 'alert-type' => 'success'];
        return redirect()->route('admin.qrcode.index')->with($notify_message);
    }

    public function toggleStatus($id)
    {
        $qrCode = QRCode::findOrFail($id);
        $qrCode->status = $qrCode->status === 'enable' ? 'disable' : 'enable';
        $qrCode->save();

        return response()->json([
            'status' => $qrCode->status,
            'message' => trans('translate.Status updated successfully')
        ]);
    }

    public function toggleFeature(Request $request)
    {
        $status = $request->status ? 1 : 0;
        GlobalSetting::where('key', 'qr_code_status')->update(['value' => $status]);

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
        GlobalSetting::where('key', 'daily_qr_code_limit')->update(['value' => $request->limit]);

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
