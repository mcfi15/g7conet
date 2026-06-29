<?php

namespace Modules\Ecommerce\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Ecommerce\Entities\Order;
use Modules\Ecommerce\Entities\OrderDetail;
use Modules\Ecommerce\Entities\Digital\License;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    public function index()
    {

        $orders = Order::with('order_detail.singleProduct.translate')->latest()->get();
        return view('ecommerce::admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('ecommerce::admin.orders.view', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $oldPaymentStatus = $order->payment_status;
        $order->order_status = $request->input('order_status');
        $order->payment_status = $request->input('payment_status');
        $order->save();

        if ($oldPaymentStatus != Status::APPROVED && $order->payment_status == Status::APPROVED) {
            $this->generateDigitalAssets($order);
        }

        $notification=  trans('translate.Status updated successfully');
        $notification=array('message'=>$notification,'alert-type'=>'success');

        return back()->with($notification);
    }

    public function paymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $oldPaymentStatus = $order->payment_status;
        $order->payment_status = $request->input('payment_status');
        $order->save();

        if ($oldPaymentStatus != Status::APPROVED && $order->payment_status == Status::APPROVED) {
            $this->generateDigitalAssets($order);
        }

        $notification=  trans('translate.Payment status updated successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');

        return back()->with($notification);
    }

    protected function generateDigitalAssets(Order $order)
    {
        if (!in_array($order->type, ['digital', 'mixed'])) {
            return;
        }

        $details = OrderDetail::with('singleProduct')->where('order_id', $order->id)->get();

        foreach ($details as $detail) {
            $product = $detail->singleProduct;
            if (!$product || !$product->is_digital) {
                continue;
            }

            if (!$detail->download_token) {
                $detail->download_token = bin2hex(random_bytes(32));
                $detail->save();
            }

            if ($product->license_type !== 'none' && !$detail->license_id) {
                $license = License::create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'product_id' => $product->id,
                    'license_key' => License::generateUniqueKey(),
                    'license_type' => $product->license_type === 'both' ? 'regular' : $product->license_type,
                    'activation_limit' => $product->license_type === 'extended' ? 5 : 1,
                    'expires_at' => $product->update_support_months
                        ? now()->addMonths($product->update_support_months)
                        : null,
                ]);

                $detail->license_id = $license->id;
                $detail->save();
            }

            try {
                $this->sendDigitalPurchaseEmail($order, $detail, $product);
            } catch (\Exception $e) {
                Log::error('Purchase email failed for order ' . $order->id . ': ' . $e->getMessage());
            }
        }
    }

    protected function sendDigitalPurchaseEmail($order, $detail, $product)
    {
        $user = $order->user;
        if (!$user || !$user->email) {
            return;
        }

        $downloadUrl = route('user.downloads.token', $detail->download_token);
        $licenseKey = $detail->license?->license_key;

        $data = [
            'user_name' => $user->name,
            'order_id' => $order->order_id,
            'product_name' => $product->name ?? 'Digital Product',
            'download_url' => $downloadUrl,
            'license_key' => $licenseKey,
            'support_expiry' => $product->update_support_months
                ? $order->created_at->addMonths($product->update_support_months)->format('F d, Y')
                : null,
        ];

        \Mail::send('emails.digital-purchase', $data, function ($message) use ($user, $product) {
            $message->to($user->email, $user->name)
                ->subject(__('translate.Your Download is Ready') . ' - ' . ($product->name ?? __('translate.Digital Product')));
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

}
