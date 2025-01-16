<?php

namespace App\Http\Controllers\Api;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController
{
    public function index()
    {
        $product = new ProductService();

        return $product->lists();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'price'     => 'required',
            'unit'   => 'required',
            'quantity'   => 'required',
        ]);

        $product = new ProductService();

        return $product->createProduct($validator);
    }

    public function tags(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_name'     => 'required',
        ]);

        $product = new ProductService();

        return $product->addTags($request->id, $validator);
    }

    public function images(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images'     => 'required',
        ]);

        $product = new ProductService();

        return $product->addImages($request->id, $validator);
    }

    public function show(Request $request)
    {
        $product = new ProductService();

        return $product->showProduct($request->product);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'price'     => 'required',
            'unit'   => 'required',
            'quantity'   => 'required',
        ]);

        $product = new ProductService();

        return $product->updateProduct($request->product, $validator);
    }

    public function destroy(Request $request)
    {
        $product = new ProductService();

        return $product->deleteProduct($request->product);
    }
}

