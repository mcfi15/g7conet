<?php

namespace Modules\Ecommerce\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Modules\Ecommerce\Entities\Digital\ProductFile;
use Modules\Ecommerce\Entities\Digital\ProductUpdate;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Ecommerce\Entities\Product;
use Modules\Ecommerce\Entities\ProductGallery;
use Modules\Ecommerce\Entities\ProductReview;
use Modules\Ecommerce\Entities\ProductTranslation;
use Modules\Language\App\Models\Language;
use Modules\Ecommerce\Entities\Order;
use Modules\Ecommerce\Entities\OrderDetail;
use Modules\Ecommerce\Entities\Cart;
use App\Models\Wishlist;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();

        return view('ecommerce::admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::with('translate')->where('status', 'enable')->get();
        $brands = Brand::with('translate')->where('status', 'enable')->get();

        return view('ecommerce::admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request, $id = null)
    {
       $rules = [
           'name' => 'required|string|max:255',
           'slug' => 'required|string|unique:products,slug,' . $id . '|max:255',
           'price' => 'required|numeric|min:0',
           'offer_price' => 'nullable|numeric|min:0|lt:price|max:100',
           'description' => 'required|string',
           'category_id' => 'required|exists:categories,id',
           'brand_id' => 'nullable|exists:brands,id',
           'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
           'seo_title' => 'nullable|string|max:255',
           'seo_description' => 'nullable|string|max:500',
           'product_type' => 'required|string|in:physical,script,ebook',
           'license_type' => 'nullable|string|in:none,regular,extended,both',
           'regular_price' => 'nullable|numeric|min:0',
           'extended_price' => 'nullable|numeric|min:0',
           'demo_url' => 'nullable|url|max:500',
           'download_limit' => 'nullable|integer|min:0',
           'update_support_months' => 'nullable|integer|min:0|max:120',
           'file_access' => 'nullable|string|in:order,email',
           'product_file' => 'nullable|file|max:512000',
           'file_version' => 'nullable|string|max:50',
           'file_changelog' => 'nullable|string|max:2000',
       ];

if ($request->product_type === 'script') {
            $rules['product_file'] = 'nullable|file|max:512000';
        } elseif ($request->product_type === 'ebook') {
            $rules['product_file'] = 'nullable|file|mimes:pdf,zip,epub|max:512000';
        } elseif ($request->product_type === 'physical') {
            $rules['product_file'] = 'nullable|file|max:512000';
            $rules['offer_price'] = 'nullable|numeric|min:0|max:100';
        }

        $request->validate($rules);



        // Check if we are creating a new product or updating an existing one
        $product = $id ? Product::findOrFail($id) : new Product();
        $product->slug = $request->slug;
        $product->price = $request->price;
        $product->tags = $request->tags;
        $product->offer_price = $request->filled('offer_price') ? $request->offer_price : null;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->filled('brand_id') ? $request->brand_id : null;
        $product->status = Status::ENABLE;

        // Product type and digital fields
        $product->product_type = $request->product_type ?? 'physical';
        if ($product->product_type === 'script') {
            $product->license_type = $request->license_type ?? 'none';
            $product->regular_price = $request->regular_price ?: null;
            $product->extended_price = $request->extended_price ?: null;
            $product->demo_url = $request->demo_url ?: null;
            $product->download_limit = $request->download_limit ?: null;
            $product->update_support_months = $request->update_support_months ?: null;
            $product->file_access = $request->file_access ?? 'order';
        } elseif ($product->product_type === 'ebook') {
            $product->license_type = 'none';
            $product->regular_price = null;
            $product->extended_price = null;
            $product->demo_url = null;
            $product->download_limit = $request->download_limit ?: null;
            $product->update_support_months = null;
            $product->file_access = 'order';
        } else {
            $product->license_type = 'none';
            $product->regular_price = null;
            $product->extended_price = null;
            $product->demo_url = null;
            $product->download_limit = null;
            $product->update_support_months = null;
            $product->file_access = 'order';
        }

        // Handle image upload and watermarking
        if ($request->hasFile('thumbnail_image')) {
            // Store the old image path if this is an update
            $old_image = $id ? $product->thumbnail_image : null;

            // Generate new image name
            $image_name = 'listing'.date('-Y-m-d-h-i-s-').rand(999,9999).'.webp';
            $image_name = 'uploads/custom-images/'.$image_name;

            // Process and save the new image
            $manager = new ImageManager(['driver' => 'gd']);
            $image = $manager->make($request->thumbnail_image);

            // Save the new image
            $image->encode('webp', 80)->save(public_path().'/'.$image_name);
            $product->thumbnail_image = $image_name;

            // Delete old image if it exists
            if ($old_image && File::exists(public_path().'/'.$old_image)) {
                File::delete(public_path().'/'.$old_image);
            }
        }

        $product->save();

        // Handle inline file upload for digital products
        \Log::debug('Store - product_type: ' . $product->product_type . ' | hasFile: ' . ($request->hasFile('product_file') ? 'yes' : 'no') . ' | file: ' . ($request->file('product_file') ? $request->file('product_file')->getClientOriginalName() : 'none') . ' | product id: ' . $product->id);
        if (in_array($product->product_type, ['script', 'ebook']) && $request->hasFile('product_file')) {
            $file = $request->file('product_file');
            $version = $request->file_version ?? '1.0.0';

            $product->files()->update(['is_current' => false]);

            $path = 'digital-products/' . $product->id . '/v' . $version;
            $storedPath = \Illuminate\Support\Facades\Storage::disk('local')->putFileAs($path, $file, $file->getClientOriginalName());

            $pf = \Modules\Ecommerce\Entities\Digital\ProductFile::create([
                'product_id' => $product->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $storedPath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'file_hash' => hash_file('sha256', $file->getRealPath()),
                'version' => $version,
                'is_current' => true,
            ]);

            if ($request->filled('file_changelog')) {
                \Modules\Ecommerce\Entities\Digital\ProductUpdate::create([
                    'product_id' => $product->id,
                    'file_id' => $pf->id,
                    'version' => $version,
                    'changelog' => $request->file_changelog,
                ]);
            }
        }

        // Handle product translations
        $languages = Language::all();
        foreach ($languages as $language) {
            $listing_translate = ProductTranslation::firstOrNew([
                'lang_code' => $language->lang_code,
                'product_id' => $product->id,
            ]);

            $listing_translate->name = $request->name;
            $listing_translate->description = $request->description;
            $listing_translate->short_description = $request->short_description;
            $listing_translate->seo_title = $request->seo_title ?? $request->name;
            $listing_translate->seo_description = $request->seo_description ?? $request->name;
            $listing_translate->save();
        }

        $notification = trans('translate.' . ($id ? 'Updated Successfully' : 'Created Successfully'));
        $notification = array('message' => $notification, 'alert-type' => 'success');

        return redirect()->route('admin.product.index')->with($notification);
    }

    public function edit(Request $request)
    {
        $id = $request->product_id;
        $lang_code = $request->lang_code;

        $categories = Category::with('translate')->where('status', 'enable')->get();
        $brands = Brand::with('translate')->where('status', 'enable')->get();
        $product = Product::findOrFail($id);

        $listing_translate = ProductTranslation::where(['product_id' => $id, 'lang_code' =>  $lang_code])->first();

        return view('ecommerce::admin.products.edit', compact('brands','categories', 'product', 'listing_translate'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'product_type' => 'required|string|in:physical,script,ebook',
        ];

        if ($request->product_type === 'script') {
            $rules['product_file'] = 'nullable|file|max:512000';
        } elseif ($request->product_type === 'ebook') {
            $rules['product_file'] = 'nullable|file|mimes:pdf,zip,epub|max:512000';
        } else {
            $rules['product_file'] = 'nullable|file|max:512000';
        }

        $request->validate($rules);

        $listing = $product;

        if($request->lang_code == admin_lang()){

                $product->slug = $request->slug;
                $product->price = $request->price;
                $product->offer_price = $request->filled('offer_price') ? $request->offer_price : null;
                $product->category_id = $request->category_id;
                $product->brand_id = $request->brand_id;
                $product->tags = $request->tags;
                $product->status = Status::ENABLE;

                // Product type and digital fields
                $product->product_type = $request->product_type ?? 'physical';
                if ($product->product_type === 'script') {
                    $product->license_type = $request->license_type ?? 'none';
                    $product->regular_price = $request->regular_price ?: null;
                    $product->extended_price = $request->extended_price ?: null;
                    $product->demo_url = $request->demo_url ?: null;
                    $product->download_limit = $request->download_limit ?: null;
                    $product->update_support_months = $request->update_support_months ?: null;
                    $product->file_access = $request->file_access ?? 'order';
                } elseif ($product->product_type === 'ebook') {
                    $product->license_type = 'none';
                    $product->regular_price = null;
                    $product->extended_price = null;
                    $product->demo_url = null;
                    $product->download_limit = $request->download_limit ?: null;
                    $product->update_support_months = null;
                    $product->file_access = 'order';
                } else {
                    $product->license_type = 'none';
                    $product->regular_price = null;
                    $product->extended_price = null;
                    $product->demo_url = null;
                    $product->download_limit = null;
                    $product->update_support_months = null;
                    $product->file_access = 'order';
                }

                // Handle inline file upload for digital products
                \Log::debug('Update - product_type: ' . $product->product_type . ' | hasFile: ' . ($request->hasFile('product_file') ? 'yes' : 'no') . ' | file: ' . ($request->file('product_file') ? $request->file('product_file')->getClientOriginalName() : 'none') . ' | all files: ' . json_encode(array_keys($request->allFiles())) . ' | _FILES keys: ' . json_encode(array_keys($_FILES)) . ' | _FILES[product_file]: ' . json_encode($_FILES['product_file'] ?? 'NOT SET'));
                if (in_array($product->product_type, ['script', 'ebook']) && $request->hasFile('product_file')) {
                    $file = $request->file('product_file');
                    $version = $request->file_version ?? '1.0.0';

                    $product->files()->update(['is_current' => false]);

                    $path = 'digital-products/' . $product->id . '/v' . $version;
                    $storedPath = Storage::disk('local')->putFileAs($path, $file, $file->getClientOriginalName());

                    $pf = ProductFile::create([
                        'product_id' => $product->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $storedPath,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'file_hash' => hash_file('sha256', $file->getRealPath()),
                        'version' => $version,
                        'is_current' => true,
                    ]);

                    if ($request->filled('file_changelog')) {
                        ProductUpdate::create([
                            'product_id' => $product->id,
                            'file_id' => $pf->id,
                            'version' => $version,
                            'changelog' => $request->file_changelog,
                        ]);
                    }
                }

                // Handle image upload and watermarking
                if ($request->hasFile('thumbnail_image')) {
                    // Store the old image path if this is an update
                    $old_image = $id ? $product->thumbnail_image : null;

                    // Generate new image name
                    $image_name = 'listing'.date('-Y-m-d-h-i-s-').rand(999,9999).'.webp';
                    $image_name = 'uploads/custom-images/'.$image_name;

                    // Process and save the new image
                    $manager = new ImageManager(['driver' => 'gd']);
                    $image = $manager->make($request->thumbnail_image);

                    // Save the new image
                    $image->encode('webp', 80)->save(public_path().'/'.$image_name);
                    $product->thumbnail_image = $image_name;

                    // Delete old image if it exists
                    if ($old_image && File::exists(public_path().'/'.$old_image)) {
                        File::delete(public_path().'/'.$old_image);
                    }
                }

                $product->save();
        }

        $listing_translate = ProductTranslation::findOrFail($request->translate_id);
        $listing_translate->name = $request->name;
        $listing_translate->description = $request->description;
        $listing_translate->short_description = $request->short_description;
        $listing_translate->seo_title = $request->seo_title;
        $listing_translate->seo_description = $request->seo_description;
        $listing_translate->save();

        $notification = trans('translate.' . ($id ? 'Updated Successfully' : 'Created Successfully'));
        $notification = array('message' => $notification, 'alert-type' => 'success');
        return redirect()->back()->with($notification);
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $old_image = $product->thumbnail_image;

        if($old_image){
            if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
        }

        ProductTranslation::where('product_id',$id)->delete();
        ProductReview::where('product_id',$id)->delete();
        Cart::where('product_id',$id)->delete();
        Wishlist::where('product_id',$id)->delete();

        $galleries = ProductGallery::where('product_id', $id)->get();
        foreach($galleries as $gallery){
            $old_image = $gallery->image;

            if($old_image){
                if(File::exists(public_path().'/'.$old_image))unlink(public_path().'/'.$old_image);
            }

            $gallery->delete();
        }

        $order_detils = OrderDetail::where('product_id', $id)->get();
        foreach($order_detils as $order_detils){
            Order::where('id',$order_detils->order_id)->delete();
            $order_detils->delete();
        }

        $product->delete();

        $notification=  trans('translate.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.product.index')->with($notification);
    }

    public function status($id)
    {
        $product = Product::findOrFail($id);
        if($product->status == Status::ENABLE){
            $product->status = Status::DISABLE;
            $product->save();
            $message = trans('translate.Status Changed Successfully');
        }else{
            $product->status = Status::ENABLE;
            $product->save();
            $message = trans('translate.Status Changed Successfully');
        }
        return response()->json($message);
    }

    public function gallery($id)
    {
        $product = Product::findOrFail($id);
        $galleries = ProductGallery::where('product_id', $id)->get();

        return view('ecommerce::admin.products.gallery', compact('product', 'galleries'));
    }

    public function uploadGallery(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        foreach ($request->file as $index => $image) {
            $gallery_image = new ProductGallery();

            if($image) {

                $image_name = 'listing'.date('-Y-m-d-h-i-s-').rand(999,9999).'.webp';
                $image_name = 'uploads/custom-images/'.$image_name;
                $manager = new ImageManager(['driver' => 'gd']);
                $image = $manager->make($image);

                $image->encode('webp', 80)->save(public_path().'/'.$image_name);

                $gallery_image->image = $image_name;

            }

            $gallery_image->product_id = $id;
            $gallery_image->save();
        }

        if ($gallery_image) {
            return response()->json([
                'message' => trans('translate.Images uploaded successfully'),
                'url' => route('admin.product.gallery', $id),
            ]);
        } else {
            return response()->json([
                'message' => trans('translate.Images uploaded Failed'),
                'url' => route('admin.product.gallery', $id),
            ]);
        }
    }

    public function deleteGallery($id){

        $gallery = ProductGallery::findOrFail($id);
        $gallery->delete();

        $notification=  trans('translate.Delete Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);

    }

    public function assign_language($lang_code){
        $product_translates = ProductTranslation::where('lang_code', admin_lang())->get();
        foreach($product_translates as $product_translate){
            $product_translate_new = new ProductTranslation();
            $product_translate_new->lang_code = $lang_code;
            $product_translate_new->product_id = $product_translate->product_id;
            $product_translate_new->name = $product_translate->name;
            $product_translate_new->description = $product_translate->description;
            $product_translate_new->seo_title = $product_translate->seo_title;
            $product_translate_new->seo_description = $product_translate->seo_description;
            $product_translate_new->save();
        }
    }

    public function review_list()
    {
        $reviews = ProductReview::latest()->get();

        return view('ecommerce::admin.products.reviews', ['reviews' => $reviews]);
    }

    public function setup_language($lang_code){
        $blog_translates = ProductTranslation::where('lang_code' , admin_lang())->get();

        foreach($blog_translates as $translate){
            $new_trans = new ProductTranslation();
            $new_trans->lang_code = $lang_code;
            $new_trans->product_id = $translate->product_id;
            $new_trans->name = $translate->name;
            $new_trans->description = $translate->description;
            $new_trans->seo_title = $translate->seo_title;
            $new_trans->seo_description = $translate->seo_description;
            $new_trans->save();

        }
    }

}
