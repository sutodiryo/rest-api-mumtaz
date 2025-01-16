<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Http\Resources\PostResource;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as ImageProcessing;

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

    public function addImages($post_id, $request)
    {

        if ($request->fails()) {

            return new PostResource(false, $request->errors(), null);
        } else {

            DB::beginTransaction();
            $post = Post::findOrFail($post_id);

            $images = $request->validated()['images'];

            foreach ($images as $key => $image) {

                $filename = $post->slug . '-' . ($key + 1) . '.jpg';

                $process_image = $this->processImage($image, $filename);

                if ($process_image) {

                    $created_image = new Image([
                        'filename' => $filename,
                        'full_path' => 'path',
                        'mime_type' => '.jpg',
                        'size' => 0,
                        'content' => 'content',
                        'created_by_id' => Auth::id(),
                    ]);

                    $post->images()->save($created_image);
                    
                }
            }

            DB::commit();

            return new PostResource(true, 'Images added to post!', $post);
        }
    }

    public function showPost($post_id)
    {
        if (!$post_id) {

            return new PostResource(false, 'Post not found', null);
        } else {

            $post = Post::with('tags', 'images')->where('id', $post_id)->first();

            return new PostResource(true, 'Post find succesfully!', $post);
        }
    }

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
