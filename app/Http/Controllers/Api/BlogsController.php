<?php

namespace App\Http\Controllers\Api;

use App\Models\Blogs;
use App\Http\Requests\UpdateBlogsRequest;
use App\Http\Controllers\Controller;
use App\Models\BlogMediaContents;
use App\Models\User;
use Database\Seeders\BlogMediaContentsSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\BlogResource;
use App\Http\Resources\BlogCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogsController extends Controller
{

    //Display all blogs
    public function index()
    {
        return new BlogCollection(Blogs::all());
    }


    //Create a new blog
    public function store(Request $request)
    {
        //Validate user input
        $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        //Fetch the user creating the blog
        $user = Auth::user();

        //Use regex to find the image URLs in the blog's body markdown
        preg_match_all('/!\[.*?\]\((.*?)\)/', $request->body, $matches);
        $imageURLs = $matches[1];

        // Using a try-catch block with a database transaction to ensure both actions are completed together or rolled back on failure
        try {
            DB::beginTransaction();

            //Store the blog
            $blog = Blogs::create([
                'title' => $request->title,
                'body' => $request->body,
                'user_id' => $user->id
            ]);

            //Updating the status of the images which are actually being used in the blog
            foreach ($imageURLs as $image) {
                BlogMediaContents::where('media_path_url', $image)->update([
                    'is_used' => true,
                    'blog_id' => $blog->id
                ]);
            }

            // Delete images that were removed from the draft and are no longer linked to the blog
            BlogMediaContents::where('is_used', false)->where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Blog created successfully.',
                'blog' => $blog
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create blog: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create blog!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Display a specific blog
    public function show(Blogs $blog)
    {
        return new BlogResource($blog);
    }

    //Edit a specific blog
    public function update(Request $request, Blogs $blog)
    {
        //Validate user input
        $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        //Fetch the user creating the blog
        $user = Auth::user();

        //Use regex to find the image URLs in the blog's body markdown
        preg_match_all('/!\[.*?\]\((.*?)\)/', $request->body, $matches);
        $updatedImageURLs = $matches[1];

        //Get old blog images to compare with the images in the new blog's markdown
        $blogOldImages = BlogMediaContents::where('blog_id', $blog->id)->pluck('media_path_url')->toArray();

        try {
            DB::beginTransaction();

            // Update the blog's title and body
            $blog->update([
                'title' => $request->title,
                'body'  => $request->body
            ]);

            // Mark new images (found in updated markdown but not previously linked) as used and link them to the blog
            foreach ($updatedImageURLs as $updatedImage) {
                if (!in_array($updatedImage, $blogOldImages)) {
                    BlogMediaContents::where('media_path_url', $updatedImage)->update([
                        'is_used' => true,
                        'blog_id' => $blog->id
                    ]);
                }
            }

            // Delete old images that are no longer present in the updated markdown
            foreach ($blogOldImages as $oldImage) {
                if (!in_array($oldImage, $updatedImageURLs)) {
                    BlogMediaContents::where('media_path_url', $oldImage)->delete();
                }
            }

            // Delete unused images that were uploaded during drafting but not associated with the blog
            BlogMediaContents::where('is_used', false)
                ->where('user_id', $user->id)
                ->delete();

            DB::commit();

            return response()->json([
                'message' => 'Blog Updated Successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update blog: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to updates blog!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Delete a specific blog
    public function destroy(Blogs $blog)
    {
        Blogs::where('id', $blog->id)->delete();
    }
}
