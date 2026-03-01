<?php

namespace App\Http\Controllers;

use App\Models\order;
use App\Models\order_product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use App\Models\product;
use App\Models\address;
use  Exception;
use GuzzleHttp\Middleware;

class OrderController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $userRole = request()->user()->role;

        $orders = Order::query()
    ->with(['user', 'products', 'products.images']);


        if (request()->has('status')) {
            $orders->where('status', request()->input('status'));
        }
        if (request()->has('user_id')) {
            $orders->where('user_id', request()->input('user_id'));
        }


        if (request()->has('order_by_date')) {
            $orders->orderBy('ordered_at', request()->input('order_by_date') === 'asc' ? 'asc' : 'desc');
        }
        if ($search = request('search')) {
            $orders->where(function ($query) use ($search) {

                $query->WhereHas('user', function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%");
                });

                $query->orWhereHas('products', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($userRole === 'customer') {
            $orders->where('user_id', request()->user()->id);
        }


        $orders = $orders->paginate(15);

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $userId = $request->user()->id;


            DB::beginTransaction();

            $validated = $request->validate([
                'address_id' => 'required|integer|exists:addresses,id',
            ]);

            // Verify address belongs to user
            $address = address::where('id', $validated['address_id'])
                ->where('user_id', $userId)
                ->firstOrFail();


            $cart = Redis::get("cart:$userId");
            if (!$cart) {
                return response()->json(['message' => 'Cart is empty'], 400);
            }

            $cartItems = json_decode($cart, true);

            $totalPrice = 0;
            $products = [];
            foreach ($cartItems as $productId => $quantity) {
                $product = product::find($productId);
                if (!$product || $product->stock < $quantity) {
                    DB::rollBack();
                    return response()->json(['message' => 'Product not available'], 400);
                }
                $products[$productId] = $product;
                $totalPrice += $product->price * $quantity;
            }


            $order = order::create([
                'user_id' => $userId,
                'address_id' => $address->id,
                'status' => 'pending',
                'total_amount' => $totalPrice,
                'number_of_items' => array_sum($cartItems),
                'ordered_at' => now(),
            ]);


            foreach ($cartItems as $productId => $quantity) {
                $product = $products[$productId];

                order_product::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_at_order' => $product->price
                ]);

                $product->decrement('stock', $quantity);

            }


            $order->update(['total_amount' => $totalPrice]);
            Redis::del("cart:$userId");
            DB::commit();
            return response()->json(['message' => 'Order placed successfully', 'order_id' => $order->id], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(order $order)
    {
        $userRole = request()->user()->role;

        if ($userRole === 'customer' && $order->user_id !== request()->user()->id) {
            return response()->json(['message' => 'Forbidden - You do not have permission'], 403);
        }

        $order->load('products', 'address','products.images');
        return response()->json($order);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, order $order)
    {
        //
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,delivered,cancelled',
        ]);

        $order->update([
            'status' => $validated['status'],
            $validated['status'] . '_at' => now()
        ]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ], 200);
    }
}
