<?php

namespace App\Http\Controllers;
use App\Models\address;

use Illuminate\Http\Request;

class addressController extends Controller
{
    //

    function index(){
        $addresses = address::with('user')->get();
        return response()->json($addresses);
    }

    function store(Request $request){
        $validated = $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'building_no' => 'required|string|max:255',
            'appartment_no' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $validated['user_id'] = $request->user()->id;

        $address = address::create($validated);

        return response()->json($address, 201);
    }

    function show($id){
        $address = address::with('user')->findOrFail($id);
        return response()->json($address);
    }

    function update(Request $request, $id){
        $address = address::findOrFail($id);

        if ($address->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'building_no' => 'required|string|max:255',
            'appartment_no' => 'sometimes|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $address->update($validated);

        return response()->json($address);
    }


    function destroy(Request $request, $id){
        $address = address::findOrFail($id);

        if ($address->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }

    
     function getUserAddresses(Request $request){
        $userId = $request->user()->id;
        $addresses = address::where('user_id', $userId)->get();
        return response()->json($addresses);
     }


}
