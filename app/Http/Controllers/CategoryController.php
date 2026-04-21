<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('restaurant:id,name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Categories successfully',
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => strtolower($request->name),
            'restaurant_id' => $request->restaurant_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category added successfully',
            'category' => $category
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $categories = Category::where('restaurant_id', $id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Categories successfully',
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
