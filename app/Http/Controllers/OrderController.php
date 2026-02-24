<?php

namespace App\Http\Controllers;

use App\Models\order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = order::query();

        if (request()->has('status')) {
            $orders->where('status', request()->input('status'));
        }
        if (request()->has('user_id')) {
            $orders->where('user_id', request()->input('user_id'));
        }
       

        if(request()->has('order_by_date')){
            $orders->orderBy('ordered_at', request()->input('order_by_date') === 'asc' ? 'asc' : 'desc');
        }
        if(request()->has('search')){
            $orders->whereHas('products', function($query){
                $query->where('name', 'like', '%' . request()->input('search') . '%')
                       ->orWhere('email', 'like', '%' . request()->input('search') . '%');
            });
        }

        $orders = $orders->paginate(15);
        return response()->json($orders);
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
    public function show(order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(order $order)
    {
        //
    }
}
