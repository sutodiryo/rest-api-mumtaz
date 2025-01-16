<?php

namespace App\Services;

use App\Http\Resources\ProductResource;
use App\Http\Resources\TransactionResource;
use App\Models\Image;
use App\Models\Product;
use App\Models\Tag;
use App\Models\Todo;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Intervention\Image\Facades\Image as ImageProcessing;

class ProductService
{

    public function lists()
    {
        $products = Product::latest()->paginate(1);

        return new ProductResource(true, 'success', $products);
    }

    public function createProduct($request)
    {    
        if ($request->fails()) {

            return new ProductResource(false, $request->errors(), null);
        } else {

            DB::beginTransaction();

            $product = Product::create([
                'name'     => $request->validated()['name'],
                'price'     => $request->validated()['price'],
                'unit'   => $request->validated()['unit'],
                'quantity'   => $request->validated()['quantity'],
                'created_by_id'   => Auth::id(),
            ]);

            DB::commit();

            return new ProductResource(true, 'Product created!', $product);
        }
    }

    public function addTags($product_id, $request)
    {
        if ($request->fails()) {

            return new ProductResource(false, $request->errors(), null);
        } else {

            DB::beginTransaction();
            $product = Product::findOrFail($product_id);

            $tags = $request->validated()['tag_name'];

            foreach ($tags as $key => $new_tag) {

                $tag = new Tag(['name' => $new_tag]);

                $product->tags()->save($tag);
            }

            DB::commit();

            return new ProductResource(true, 'Tags added to product!', $product);
        }
    }

    public function addImages($product_id, $request)
    {

        if ($request->fails()) {

            return new ProductResource(false, $request->errors(), null);
        } else {

            DB::beginTransaction();
            $product = Product::findOrFail($product_id);

            $images = $request->validated()['images'];

            foreach ($images as $key => $image) {

                $filename = $product->name . '-' . ($key + 1) . '.jpg';

                $process_image = $this->processImage($image, $filename);

                if ($process_image) {

                    $created_image = new Image([
                        'filename' => $filename,
                        'full_path' => 'path',
                        'mime_type' => '.jpg',
                        'size' => 0,
                        'unit' => 'unit',
                        'created_by_id' => Auth::id(),
                    ]);

                    $product->images()->save($created_image);
                }
            }

            DB::commit();

            return new ProductResource(true, 'Images added to product!', $product);
        }
    }

    public function showProduct($id)
    {
        $product = Product::with('tags', 'images')->where('id', $id)->first();

        if (!$product) {

            return new ProductResource(false, 'Product not found', null);
        } else {

            return new ProductResource(true, 'Product find succesfully!', $product);
        }
    }

    
    public function updateProduct($product_id, $request)
    {
        if ($request->fails()) {

            return new ProductResource(false, $request->errors(), null);
        } else {

            DB::beginTransaction();

            $product = Product::findOrFail($product_id);

            $product->name = $request->validated()['name'];
            $product->price = $request->validated()['price'];
            $product->unit = $request->validated()['unit'];
            $product->quantity = $request->validated()['quantity'];
            $product->save();

            DB::commit();

            return new ProductResource(true, 'Product updated!', $product);
        }
    }

    public function deleteProduct($product_id)
    {
        $product = Product::findOrFail($product_id);

        if (!$product) {

            return new ProductResource(false, 'Product not found', null);
        } else {

            DB::beginTransaction();
            $product->tags()->delete();
            $product->Images()->delete();
            $product->delete();

            DB::commit();

            return new ProductResource(true, 'Product deleted succesfully!', $product);
        }
    }

    // Additional
    function processImage($base64_img, $filename)
    {
        $imageResize = ImageProcessing::make(base64_decode($base64_img))->resize(1280, null, function ($constraint) {
            $constraint->upsize();
            $constraint->aspectRatio();
        });

        $exif = @exif_read_data('data://image/jpeg;base64,' . substr($base64_img, 0, 30000));

        if (!empty($exif['Orientation'])) {

            switch ($exif['Orientation']) {
                case 3:
                    $imageResize->rotate(180);
                    break;
                case 6:
                    $imageResize->rotate(-90);
                    break;
                case 8:
                    $imageResize->rotate(90);
                    break;
            }
        }

        return Storage::disk('s3')->put($filename, $imageResize);
    }
}
