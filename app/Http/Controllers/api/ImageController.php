<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Nette\Schema\Helpers;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = Image::with('category')->paginate(12);
        return $images;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            // 'image' => 'required',
            'title' => 'required',
            // 'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $image = new Image();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $image->image = $path;
        }
        if ($request->hasFile('before')) {
            $path = $request->file('before')->store('before', 'public');
            $image->before = $path;
        }
        $image->title = $request->title;
        $image->description = $request->description;
        $image->alt = $request->alt;
        $image->category_id = $request->category_id;

        // Generate a slug by concatenating the title and a random number
        $image->slug = Str::slug($request->title);

        $image->save();

        return response()->json(['message' => 'added successfuly'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $image = Image::where('slug', $slug)->firstOrFail();
        return $image;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $image = Image::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            // 'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'validation error'], 422);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $image->image = $path;
        }

        if ($request->hasFile('before')) {
            $path = $request->file('before')->store('before', 'public');
            $image->before = $path;
        }

        $image->title = $request->title;
        $image->description = $request->title;
        $image->alt = $request->alt;
        $image->category_id = $request->category_id;

        $image->save();

        return response()->json(['message' => 'updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Image::findOrFail($id)->delete();
        return response()->json(['message' => 'deleted successfuly']);
    }

    public function getImagesByCategory($category_id)
    {
        $images = Image::where('category_id', $category_id)->get();
        return $images;
    }
}
