<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Cart_items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function addToCart(Request $request)
    {
        // 1. Validation for Adding an Item
        $request->validate([
            // item_id is the product/menu item ID being added
            'item_id' => 'required|integer|exists:menus,id', // Make sure 'items' table exists
            'qty'     => 'required|integer|min:1',
        ]);

        $itemId = $request->input('item_id');
        $quantity = $request->input('qty');
        $note = $request->input('note');
        $price = $request->input('price');
        
        // Get the authenticated user's ID
        $userId = $request->user()->id ?? 1; 

        // 2. Determine the Restaurant ID
        // CRITICAL: We need the restaurant_id to link the cart properly.
        // Assuming your 'items' table has a 'restaurant_id' foreign key.
        $restaurantId = \DB::table('menus')->where('id', $itemId)->value('restaurant_id');

        if (!$restaurantId) {
             return response()->json(['success' => false, 'message' => 'Invalid item ID.'], 404);
        }

        // 3. Find or Create the Active Cart in the 'carts' table
        // Find an active cart for the user tied to this specific restaurant.
        $cart = Cart::firstOrCreate(
            [
                'user_id' => $userId,
                'restaurant_id' => $restaurantId,
                'is_active' => 1, // Ensure we are dealing with an open, active cart
            ],
            [
                // Set these values only if a new cart is created
                'user_id' => $userId,
                'restaurant_id' => $restaurantId,
                'is_active' => 1,
            ]
        );
        
        // $cart now holds the record from the 'carts' table, whether found or newly created.

        // 4. Insert or Update the Item in the 'cart_items' table (UPSERT)
        // Check if the item already exists in this cart.
        $cartItem = Cart_items::where('cart_id', $cart->id)
            ->where('item_id', $itemId)
            ->first();

        if ($cartItem) {
            // Item exists: Increment the quantity
            $cartItem->qty += $quantity;
            $cartItem->note = $note;
            $cartItem->price = $cartItem->price * 2;
            $cartItem->save();
        } else {
            // Item is new: Create a new CartItem record
            $cartItem = Cart_items::create([
                'cart_id' => $cart->id,
                'item_id' => $itemId,
                'qty' => $quantity,
                'note' => $note,
                'price' => $price
            ]);
        }

        // 5. Return the FULL, Updated Cart Data to React
        // Eager load the cart items so React gets the complete list.
        // $cartData = $cart->load('items'); 

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully!',
            'data'    => '' // $cartData, // React uses this data to update its state
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
    public function show(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $cart = DB::table('carts as ca')
            ->select(
                'ct.id as id',
                'ct.qty',
                'ct.price',
                'me.restaurant_id',
                'me.item_name',
                'me.item_description',
                'me.image'
            )
            ->join('cart_items as ct', 'ct.cart_id', '=', 'ca.id')
            ->join('menus as me', 'me.id', '=', 'ct.item_id')
            ->where('ca.user_id', $user->id)
            ->where('ca.is_active', 1)
            ->get();

        return $cart;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }

    public function cartCount(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $cartCount = (int) \DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where('carts.user_id', $user->id)
            ->where('carts.is_active', 1)
            ->selectRaw('COALESCE(SUM(cart_items.qty),0) as total')
            ->value('total');

        return response()->json(['count' => $cartCount]);
    }

    public function checkout(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $cart = DB::table('carts as ca')
            ->select(
                'ct.id as id',
                'ct.qty',
                'ct.price',
                'ct.item_id',
                'ct.cart_id',
                'me.restaurant_id',
                'me.item_name',
                'me.item_description',
                'me.image',
            )
            ->join('cart_items as ct', 'ct.cart_id', '=', 'ca.id')
            ->join('menus as me', 'me.id', '=', 'ct.item_id')
            ->where('ca.user_id', $user->id)
            ->where('ca.is_active', 1)
            ->get();

        $restaurantId = $cart->first()->restaurant_id;
        $cartId = $cart->first()->cart_id;
        $totalQty = $cart->sum('qty'); 
        $totalPrice = $cart->sum('price'); 

        DB::beginTransaction();

        try {
            // ✅ Insert into orders table
            $orderId = DB::table('orders')->insertGetId([
                'user_id'        => $user->id,
                'restaurant_id'  => $restaurantId,
                'item_qty'       => $totalQty,
                'total_price'    => $totalPrice,
                'cart_id'        => $cartId,
                'status'         => 'pending',
                'paymemt_method' => 'cash',
                'note'           => 'this order is done',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // ✅ Insert order items
            foreach ($cart as $item) {
                DB::table('order_items')->insert([
                    'user_id'    => $user->id,
                    'order_id'   => $orderId,
                    'item_id'    => $item->item_id,
                    'qty'        => $item->qty,
                    'price'      => $item->price,
                    'note'       => 'order done for this',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ✅ Deactivate cart after checkout
            DB::table('carts')
                ->where('user_id', $user->id)
                ->where('is_active', 1)
                ->update(['is_active' => 0]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id'=> $orderId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Checkout failed',
                'error'   => $e->getMessage()
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Items has ordered successfully',
            'data'    => $orderId // $cartData, // React uses this data to update its state
        ]);
    }

}
