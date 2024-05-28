<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            return response()->json(['errors' => $validator->errors()], 403);
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

    public function updateImage(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // Validate image
        ]);

        // Retrieve the category by ID
        $id = $request->id;
        $category = Category::findOrFail($id);

        // Check if the request contains a new image file
        if ($request->hasFile('image')) {
            // Check if the category already has an image
            if ($category->image) {
                $imagePath = $category->image;

                // Add a debug log to check the image path
                \Log::info('Attempting to delete image at path: ' . $imagePath);

                // Check if the file exists before attempting to delete it
                if (Storage::disk('public')->exists($imagePath)) {
                    $deleted = Storage::disk('public')->delete($imagePath);

                    // Add a debug message to confirm if the deletion was successful
                    if (!$deleted) {
                        return response()->json(['message' => 'Failed to delete old image'], 500);
                    } else {
                        \Log::info('Image deleted successfully.');
                    }
                } else {
                    \Log::warning('File does not exist at path: ' . $imagePath);
                }
            }

            // Store the new image
            $path = $request->file('image')->store('category-photo', 'public');
            $category->image = $path;

            // Save the updated category
            $category->save();

            // Add a debug message to confirm the new image path
            \Log::info('New image stored at path: ' . $path);
        }

        // Return a success response
        return response()->json(['message' => 'Image updated successfully'], 200);
    }

    // public function deleteImage(Request $request)
    // {
    //     // Validate the incoming request
    //     $validated = $request->validate([
    //         'id' => 'required|exists:categories,id', // Ensure the ID exists in the categories table
    //     ]);

    //     // Retrieve the category by ID
    //     $id = $validated['id'];
    //     $category = Category::findOrFail($id);

    //     // Check if the category has an image and delete it
    //     if ($category->image) {
    //         Storage::disk('public')->delete($category->image);
    //         $category->image = null; // Set image field to null after deletion
    //         $category->save();
    //     } else {
    //         return response()->json(['message' => 'No image to delete'], 404);
    //     }

    //     // Return a success response
    //     return response()->json(['message' => 'Image deleted successfully'], 200);
    // }
}
