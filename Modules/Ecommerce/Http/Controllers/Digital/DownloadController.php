<?php

namespace Modules\Ecommerce\Http\Controllers\Digital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Ecommerce\Entities\OrderDetail;
use Modules\Ecommerce\Entities\Digital\Download;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:10,1')->only(['download', 'downloadByToken']);
    }

    public function index()
    {
        $user = auth()->guard('web')->user();
        if (!$user) {
            return redirect()->route('user.login');
        }

        $orders = \Modules\Ecommerce\Entities\Order::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('type', 'digital')->orWhere('type', 'mixed');
            })
            ->where('payment_status', \App\Constants\Status::APPROVED)
            ->with(['order_detail' => function ($q) {
                $q->whereHas('singleProduct', function ($p) {
                    $p->digital();
                })->with(['singleProduct.front_translate', 'singleProduct.currentFile', 'license']);
            }])
            ->latest()
            ->paginate(10); 

        return view('ecommerce::frontend.downloads.index', compact('orders'));
    }

    public function download(Request $request, $orderDetailId)
    {
        $user = auth()->guard('web')->user();
        if (!$user) {
            return redirect()->route('user.login');
        }

        $detail = OrderDetail::with(['singleProduct', 'order'])
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('payment_status', \App\Constants\Status::APPROVED);
            })
            ->findOrFail($orderDetailId);

        $product = $detail->singleProduct;
        if (!$product || !$product->is_digital) {
            abort(404);
        }

        $file = $product->currentFile;
        if (!$file) {
            return redirect()->back()->with([
                'messege' => 'No file available for this product yet.',
                'alert-type' => 'error'
            ]);
        }

        $rateKey = 'download:' . $user->id . ':' . $file->id;
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return redirect()->back()->with([
                'messege' => "Too many download attempts. Try again in {$seconds} seconds.",
                'alert-type' => 'error'
            ]);
        }
        RateLimiter::hit($rateKey, 60);

        $downloadCount = Download::where('order_detail_id', $detail->id)->count();
        if ($product->download_limit && $downloadCount >= $product->download_limit) {
            return redirect()->back()->with([
                'messege' => 'Download limit reached for this purchase.',
                'alert-type' => 'error'
            ]);
        }

        $filePath = $file->file_path;
        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            return redirect()->back()->with([
                'messege' => 'File not found on server.',
                'alert-type' => 'error'
            ]);
        }

        $realPath = Storage::disk('local')->path($filePath);
        $realPath = realpath($realPath);
        $allowedDir = realpath(storage_path('app/digital-products'));
        if (!$realPath || !$allowedDir || strpos($realPath, $allowedDir) !== 0) {
            abort(403, 'Invalid file path.');
        }

        Download::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $detail->order_id,
            'order_detail_id' => $detail->id,
            'file_id' => $file->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'downloaded_at' => now(),
        ]);

        $file->increment('download_count');

        return Storage::disk('local')->download($filePath, $file->file_name);
    }

    public function downloadByToken(Request $request, $token)
    {
        $detail = OrderDetail::with(['singleProduct', 'order'])
            ->where('download_token', $token)
            ->first();

        if (!$detail) {
            abort(404, 'Invalid or expired download link.');
        }

        if ($detail->order->payment_status != \App\Constants\Status::APPROVED) {
            abort(403, 'Payment not approved for this order.');
        }

        $rateKey = 'token-download:' . $token;
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return response("Too many attempts. Try again in {$seconds} seconds.", 429);
        }
        RateLimiter::hit($rateKey, 60);

        $product = $detail->singleProduct;
        if (!$product || !$product->is_digital) {
            abort(404);
        }

        $file = $product->currentFile;
        if (!$file) {
            abort(404, 'No file available.');
        }

        $downloadCount = Download::where('order_detail_id', $detail->id)->count();
        if ($product->download_limit && $downloadCount >= $product->download_limit) {
            return response('Download limit reached.', 403);
        }

        $filePath = $file->file_path;
        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found.');
        }

        Download::create([
            'product_id' => $product->id,
            'order_id' => $detail->order_id,
            'order_detail_id' => $detail->id,
            'file_id' => $file->id,
            'download_token' => $token,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'downloaded_at' => now(),
        ]);

        $file->increment('download_count');

        return Storage::disk('local')->download($filePath, $file->file_name);
    }
}
