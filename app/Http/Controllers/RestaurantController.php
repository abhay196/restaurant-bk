<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // Don't forget to import this at the top!
use Cloudinary\Cloudinary;


class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $start = microtime(true);

        // 1. Define a unique key for this data
        // 2. Set time in seconds (e.g., 3600 = 1 hour)
        // 3. The closure runs ONLY if the cache is empty
        $restaurants = Cache::remember('all_restaurants', 3600, function () {
            Log::info("Fetching from Database (Cache Miss)"); 
            return Restaurant::all();
        });

        $end = microtime(true);
        $executionTime = round(($end - $start) * 1000, 2); // Time in milliseconds

        return response()->json([
            'success' => true,
            'message' => 'Restaurants fetched successfully!',
            'time_ms' => $executionTime . 'ms', // This tells you how fast it was
            'data'    => $restaurants,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'         => 'required|string|max:255',
                'address'      => 'required|string',
                'phone'        => 'required|string|max:20',
                'description'  => 'nullable|string',
                'is_available' => 'required|boolean',
                'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
                'type'         => 'required|string|max:100',
            ]);

            $validated['user_id'] = $request->user()->id ?? 1; 
            $validated['slug'] = strtolower($validated['name']);

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
                    'folder' => 'restaurant'
                ]);

                $validated['image'] = $upload['secure_url'];
            }

            $restaurant = Restaurant::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Restaurant created successfully!',
                'data'    => $restaurant,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
            ], 500);
        }
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
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found!',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Restaurant data fetched successfully!',
            'data'    => $restaurant,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found!'], 404);
        }

        // Use $request->all() for validation to avoid issues with boolean casting
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'address'      => 'required|string',
            'phone'        => 'required|string|max:20',
            'description'  => 'nullable|string',
            'is_available' => 'required', // Removed 'boolean' check here to prevent string "1" issues
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
            'type'         => 'required|string|max:100',
        ]);

        if ($request->hasFile('image')) {
            // 3. DIRECT UPLOAD LOGIC FOR EDIT
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => 'dkwsaccn9',
                    'api_key'    => '879561643833876',
                    'api_secret' => 'X0DS2-kPB6xZcru_8qrz_6Oc--4',
                ],
            ]);

            $upload = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath(), [
                'folder' => 'restaurant'
            ]);

            $validated['image'] = $upload['secure_url'];
        } else {
            $validated['image'] = $restaurant->image;
        }

        $restaurant->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Restaurant updated successfully!',
            'data'    => $restaurant,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        //
    }

}
