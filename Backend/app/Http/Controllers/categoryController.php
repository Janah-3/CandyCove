<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\category;
use App\Http\Middleware\checkRole as Middleware;

class categoryController extends Controller
{

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index']),
            new Middleware('checkRole:admin', except: ['index']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $categories = category::paginate(15);

        return response()->json($categories, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = category::create($validated);
        return response()->json($category, 201);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category->update($validated);
        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
