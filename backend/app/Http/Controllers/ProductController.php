<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductController extends Controller implements HasMiddleware
{

public static function middleware(): array
{
    return [
        new Middleware('auth:sanctum', except: ['index', 'show']),
        new Middleware('checkRole:admin', except: ['index', 'show']),
    ];
}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       try{ $query = product::query();
         
        if (request()->has('category_id')) {
            $query->where('category_id', request()->input('category_id'));
        }

        if (request()->has('max_price')) {
            $query->where('price', '<=', request()->input('max_price'));
        }
        if(request()->has('order_by_price')){
            $query->orderBy('price', request()->input('order_by_price') === 'asc' ? 'asc' : 'desc');
        }

         if (request()->has('min_price')) {
            $query->where('price', '>=', request()->input('min_price'));
        }

         $query->where('is_active', true);

         if (request()->has('search')) {
            $query->where('name', 'like', '%' . request()->input('search') . '%');
        }

        $products = $query->paginate(15);
       
        return response()->json($products);}
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    
     $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'is_active' => 'required|in:0,1',
        'category_id' => 'required|exists:categories,id',
        'images' => 'required|array',
        'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
    ]);

    try {
        DB::beginTransaction();
        
        $product = product::create(collect($validated)->except('images')->toArray());

        foreach ($request->file('images') as $index => $image) {
          $uploadedFileUrl = cloudinary()->uploadApi()->upload($image->getRealPath(), [
    'folder' => 'products'
])['secure_url'];

            ProductImage::create([
                'product_id' => $product->id,
                'url' => $uploadedFileUrl,
                'is_primary' => $index === 0,
            ]);
        }

        DB::commit();
        return response()->json($product, 201);

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
    public function show(product $product)
    {
        return response()->json(['product' => $product , 'images' => $product->images]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'stock' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(product $product)
    {
        $product->delete();

        return response()->json([ 'message' => 'Product deleted successfully'], 204);
    }

    

    static function decrease_stock($id, $quantity){
        $product = product::find($id);
            if (!$product ) {
                return response()->json(['message' => 'Product not found'], 404);
                exit;
            }
        $product->stock -= $quantity;
        $product->save();
    }

    
}

