<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Nette\Schema\Helpers;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return $categories;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        // Create a new category and assign the values from the request
        $category = new Category();
        $category->name = $request->name;
        $category->description = $request->description;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('category-photo', 'public');
            $category->image = $path;
        }

        // Generate a slug by concatenating the title and a random number
        $category->slug = Str::slug($request->name);

        // Save the category
        $category->save();

        return response()->json(['message' => 'added successfuly'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->first();
        return $category;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'validation error'], 422);
        }

        $category->name = $request->name;
        $category->description = $request->description;

        $category->save();

        return response()->json(['message' => 'updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return response()->json(['message' => 'deleted successfuly']);
    }
}
