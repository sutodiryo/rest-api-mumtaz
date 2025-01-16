<?php

namespace App\Http\Controllers\Api;

use App\Services\PostService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $post = new PostService();

        return $post->lists();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug'     => 'required',
            'title'     => 'required',
            'content'   => 'nullable',
        ]);

        $post = new PostService();

        return $post->createPost($validator);
    }


    public function tags(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_name'     => 'required',
        ]);

        $post = new PostService();

        return $post->addTags($request->id, $validator);
    }

    public function show(Request $request) {

        $post = new PostService();

        return $post->showPost($request->post);
    }
    public function edit() {}

    public function delete() {}
}
