<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Predis\Command\Redis\HEXISTS;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{

function viewCart(Request $request){
    $userId = $request->user()->id;
    $cartKey = "cart:$userId";

    
    $cartItems = Redis::get($cartKey);

    $productDetails = [];

    foreach (json_decode($cartItems, true) ?? [] as $productId => $quantity) {
        $product = product::find($productId);
        if ($product) {
            $productDetails[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity
            ];
        }
    }

    return response()->json([
        'product_details' => $productDetails
    ], 200);
}

 function addToCart(Request $request){
        
    try{

    $validated = $request->validate([
    'product_id' => 'required|integer|exists:products,id',
    'quantity' => 'sometimes|integer|min:1'
    ]);

    $userId = $request->user()->id;
    $productId = $validated['product_id'];
    $quantity = $validated['quantity'] ?? 1;
    $cartKey = "cart:$userId";

    
    $cart = Redis::get($cartKey);
    $cartItems = $cart ? json_decode($cart, true) : [];



   $product = product::find($productId);

    $existingQuantity = $cartItems[$productId] ?? 0;

    if ($product->stock < ($existingQuantity + $quantity)) {
    return response()->json(['message' => 'Requested quantity not available'], 400);
    }

    
    if (isset($cartItems[$productId])) {
        $cartItems[$productId] += $quantity;
    } else {
        $cartItems[$productId] = $quantity;
    }

    Redis::setex($cartKey, 60 * 60 * 24 * 14, json_encode($cartItems));

    return response()->json(['message' => 'Product added to cart successfully'], 200);

} catch (\Exception $e) {

    return response()->json(['message' => $e->getMessage()], 400);
}
    }


    function removeItem(Request $request, $productId){
    $userId = $request->user()->id;
    $cartKey = "cart:$userId";

    $cart = Redis::get($cartKey);
    if (!$cart) {
        return response()->json(['message' => 'Cart is empty'], 400);
    }

    $cartItems = json_decode($cart, true);

    if (!isset($cartItems[$productId])) {
        return response()->json(['message' => 'Product not in cart'], 400);
    }

    unset($cartItems[$productId]);

    Redis::setex($cartKey, 60 * 60 * 24 * 14, json_encode($cartItems));

    return response()->json(['message' => 'Product removed successfully'], 200);
}

function decreseAmount(Request $request, $productId){


    $userId = $request->user()->id;
    $cartKey = "cart:$userId";

    $cart = Redis::get($cartKey);
    if (!$cart) {
        return response()->json(['message' => 'Cart is empty'], 400);
    }

    $cartItems = json_decode($cart, true);

    if (!isset($cartItems[$productId])) {
        return response()->json(['message' => 'Product not in cart'], 400);
    }

    if ($cartItems[$productId] > 1) {
        $cartItems[$productId] -= 1;
    } else {
        unset($cartItems[$productId]);
    }

    Redis::setex($cartKey, 60 * 60 * 24 * 14, json_encode($cartItems));

    return response()->json(['message' => 'Product quantity decreased successfully'], 200);
}


function increaseAmount(Request $request, $productId){
    $userId = $request->user()->id;
    $cartKey = "cart:$userId";

    $cart = Redis::get($cartKey);
    if (!$cart) {
        return response()->json(['message' => 'Cart is empty'], 400);
    }

    $cartItems = json_decode($cart, true);

    if (!isset($cartItems[$productId])) {
        return response()->json(['message' => 'Product not in cart'], 400);
    }

    $product = product::find($productId);
    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    if ($product->stock < ($cartItems[$productId] + 1)) {
        return response()->json(['message' => 'Requested quantity not available'], 400);
    }

    $cartItems[$productId] += 1;

    Redis::setex($cartKey, 60 * 60 * 24 * 14, json_encode($cartItems));

    return response()->json(['message' => 'Product quantity increased successfully'], 200);

}

function clearCart(Request $request){
    $userId = $request->user()->id;
    $cartKey = "cart:$userId";

    $cart = Redis::get($cartKey); // Check if cart exists
    if (!$cart) {
        return response()->json(['message' => 'Cart is already empty'], 400);
    }

    Redis::del($cartKey);

    return response()->json(['message' => 'Cart cleared successfully'], 200);
}
}