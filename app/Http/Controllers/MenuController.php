<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load both 'category' and 'restaurant'
        // You can specify only the columns you need to keep the payload light
        $menus = Menu::with([
            'category:id,name', 
            'restaurant:id,name'
        ])->get();

        return response()->json([
            'success' => true,
            'message' => 'Menus fetched successfully!',
            'data'    => $menus,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id'    => 'required|integer|exists:restaurants,id',
            'category_id'    => 'required|integer',
            'item_name'        => 'required|string|max:255',
            'item_description' => 'required|string|max:500',
            'price'            => 'nullable|numeric|min:0',
            'is_available'     => 'required|boolean',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
        ]);

        // ✅ Handle image upload
        if ($request->hasFile('image')) {
            // 2. DIRECT UPLOAD LOGIC
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => 'dkwsaccn9',
                    'api_key'    => '879561643833876',
                    'api_secret' => 'X0DS2-kPB6xZcru_8qrz_6Oc--4',
                ],
            ]);

            $upload = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath(), [
                'folder' => 'menu'
            ]);

            $validated['image'] = $upload['secure_url'];
        }

        // ✅ Insert into DB
        $menu_item = Menu::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu item created successfully!',
            'data'    => $menu_item,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $menu_item = menu::find($id);

        if (!$menu_item) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found!',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu item fetched successfully!',
            'data'    => $menu_item,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $menu_item = Menu::find($id);

        if (!$menu_item) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found!',
            ], 404);
        }

        $validated = $request->validate([
            'restaurant_id'    => 'required|integer|exists:restaurants,id',
            'item_name'        => 'required|string|max:255',
            'item_description' => 'required|string|max:500',
            'price'            => 'nullable|numeric|min:0',
            'is_available'     => 'required|boolean',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
        ]);

        // ✅ Handle image upload only if a new file is uploaded
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public'); 
            $validated['image'] = $path;
        } else {
            $validated['image'] = $menu_item->image;
        }
        
        // return response()->json([
        //     'success' => false,
        //     'message' => 'resdsdff!',
        //     'data'    => $validated,
        // ]);
        // ✅ Update data
        $menu_item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu updated successfully!',
            'data'    => $menu_item,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        //
    }

    public function menus($id)
    {
        // Step 1: Find the restaurant and eager load menus with their categories
        // We use dot notation 'menus.category' to reach the nested relationship
        $restaurant = Restaurant::with(['menus.category:id,name'])->find($id);

        // If the RESTAURANT doesn't exist, return 404
        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found',
            ], 404);
        }

        // Step 2 & 3: Return the data
        // $restaurant now contains a 'menus' property, and each menu item 
        // now contains a 'category' object.
        return response()->json([
            'success' => true,
            'message' => 'Menu fetched successfully!',
            'data' => [
                'restaurant' => $restaurant->makeHidden('menus'), // Clean up parent object
                'menus'      => $restaurant->menus,
            ],
        ]);
    }


}
