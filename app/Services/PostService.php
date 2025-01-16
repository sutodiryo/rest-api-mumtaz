<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Http\Resources\PostResource;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function lists()
    {
        $posts = Post::latest()->paginate(1);

        return new PostResource(true, 'success', $posts);
    }

    public function createPost($request)
    {

        if ($request->fails()) {

            return new PostResource(false, $request->errors(), null);
        } else {

            DB::beginTransaction();

            $post = Post::create([
                'slug'     => $request->validated()['slug'],
                'title'     => $request->validated()['title'],
                'content'   => $request->validated()['content'],
                'created_by_id'   => Auth::id(),
            ]);

            DB::commit();

            return new PostResource(true, 'Post created!', $post);
        }
    }


    public function addTags($post_id, $request)
    {
        if ($request->fails()) {

            return new PostResource(false, $request->errors(), null);
        } else {

            DB::beginTransaction();
            $post = Post::findOrFail($post_id);

            $tags = $request->validated()['tag_name'];

            foreach ($tags as $key => $new_tag) {

                $tag = new Tag(['name' => $new_tag]);

                $post->tags()->save($tag);
            }

            DB::commit();

            return new PostResource(true, 'Tags added to post!', $post);
        }
    }

    public function showPost($post_id)
    {
        if (!$post_id) {

            return new PostResource(false, 'Post not found', null);
        } else {

            $post = Post::with('tags')->where('id', $post_id)->first();

            return new PostResource(true, 'Post find succesfully!', $post);
        }
    }
}
