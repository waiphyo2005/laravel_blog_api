<?php

namespace App\Http\Controllers\Api;

use App\Models\BlogMediaContents;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogMediaContentsController extends Controller
{
    //Display all images
    public function index()
    {
        return response()->json(BlogMediaContents::all(), 200);
    }

    //Store new image
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $user = Auth::user();

        $imageOriginalName = $validatedData['image']->getClientOriginalName();
        $imageUniqueName = uniqid() . '-' . str_replace(' ', '-', $imageOriginalName);
        $validatedData['image']->move(public_path('images/blog-images'), $imageUniqueName);
        $imagePathURL = asset('images/blog-images/' . $imageUniqueName);

        $newBlogImage = BlogMediaContents::create([
            'media_name' => $imageOriginalName,
            'media_path_url' => $imagePathURL,
            'is_used' => false,
            'user_id' => $user->id
        ]);

        return response()->json([
            'message' => 'Image stored successfully',
            'imageName' => $newBlogImage->media_name,
            'imagePath' => $newBlogImage->media_path_url
        ], 201);
    }

    //Delete Image
    public function destroy(BlogMediaContents $blogMediaContent)
    {
        BlogMediaContents::where('id', $blogMediaContent->id)->delete();
    }
}
