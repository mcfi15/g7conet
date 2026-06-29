<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Ecommerce\Entities\Order;
use Modules\Ecommerce\Entities\OrderDetail;
use Modules\Ecommerce\Entities\Digital\License;
use App\Constants\Status;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(){
        $orders = Order::with('order_detail.singleProduct.translate')->latest()->get();

        $title = trans('translate.All Order');

        return view('admin.order_list', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }


    public function active_orders(){


        $orders = Order::with('order_detail.singleProduct.translate')->where(['order_status' => \App\Constants\Status::PROCESSING])->latest()->get();

        $title = trans('translate.Active Order');

        return view('admin.order_list', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }


    public function reject_orders(){

        $orders = Order::with('order_detail.singleProduct.translate')->where(['order_status' => \App\Constants\Status::REJECTED])->latest()->get();

        $title = trans('translate.Rejected Order');

        return view('admin.order_list', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }

    public function delivered_orders(){

        $orders = Order::with('order_detail.singleProduct.translate')->where(['order_status' => \App\Constants\Status::SHIPPED])->latest()->get();

        $title = trans('translate.Delivered Order');

        return view('admin.order_list', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }

    public function complete_orders(){

        $orders = Order::with('order_detail.singleProduct.translate')->where(['order_status' => \App\Constants\Status::COMPLETED])->latest()->get();

        $title = trans('translate.Complete Order');

        return view('admin.order_list', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }

    public function pending_payment_orders(){

        $orders = Order::where('payment_status', 'pending')->latest()->get();

        $title = trans('translate.Pending Payment Order');

        return view('admin.pending_payment_orders', [
            'orders' => $orders,
            'title' => $title,
        ]);
    }

    public function order_show($order_id){

        $order = Order::with('order_detail.singleProduct')->where('order_id', $order_id)->first();
        $seller = User::findOrFail($order->user_id);

        return view('admin.order_show', [
            'order' => $order,
            'seller' => $seller,
        ]);
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


    public function order_delete(Request $request, $id){

        $order = Order::where('id', $id)->first();
        OrderDetail::where('order_id', $order->id)->delete();
        $order->delete();

        $notify_message = trans('translate.Order delete successful');
        $notify_message = array('message' => $notify_message, 'alert-type' => 'success');
        return redirect()->route('admin.orders')->with($notify_message);
    }


}
