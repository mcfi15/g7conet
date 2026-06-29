<?php

namespace Modules\Ecommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Ecommerce\Entities\Product;
use Modules\Ecommerce\Entities\Digital\ProductFile;
use Modules\Ecommerce\Entities\Digital\ProductUpdate;

class ProductFileController extends Controller
{
    public function index($productId)
    {
        $product = Product::with('files')->findOrFail($productId);
        return view('ecommerce::admin.products.files', compact('product'));
    }

    public function upload(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:512000',
            'version' => 'required|string|max:50',
            'changelog' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('file');
        $version = $request->version;

        $product->files()->update(['is_current' => false]);

        $path = 'digital-products/' . $product->id . '/v' . $version;
        $storedPath = Storage::disk('local')->putFileAs($path, $file, $file->getClientOriginalName());

        $productFile = ProductFile::create([
            'product_id' => $product->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'version' => $version,
            'is_current' => true,
        ]);

        if ($request->filled('changelog')) {
            ProductUpdate::create([
                'product_id' => $product->id,
                'file_id' => $productFile->id,
                'version' => $version,
                'changelog' => $request->changelog,
                'created_at' => now(),
            ]);
        }

        $notification = [
            'messege' => trans('translate.File uploaded successfully'),
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.product.files', $product->id)->with($notification);
    }

    public function setCurrent(Request $request, $fileId)
    {
        $file = ProductFile::with('product')->findOrFail($fileId);
        $file->product->files()->update(['is_current' => false]);
        $file->update(['is_current' => true]);

        return response()->json(['success' => true]);
    }

    public function destroy($fileId)
    {
        $file = ProductFile::findOrFail($fileId);
        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }
        $file->delete();

        $notification = [
            'messege' => trans('translate.Delete Successfully'),
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }
}
