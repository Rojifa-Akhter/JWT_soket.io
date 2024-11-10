<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\product;
use App\Models\Product as ModelsProduct;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('user_id',Auth::id())->get();
        return response()->json($products);
    }

    // for user
    public function create(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|string',
            'status' => 'required|string|in:approved,reject,pending',
        ]);

        // dd($request->all());
        $product = Product::create([
            'user_id' => Auth::user()->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        return response()->json(['message' => 'Product created successfully'], 201);
    }
    public function approve($id)
    {
        $product = Product::where('id',$id)->first(); 
        // dd($product);
        $product->status = 'approved';
        $product->save();

        return response()->json(['message' => 'Product approved successfully.']);
    }
    public function reject($id)
    {
        $product = Product::where('id',$id)->first(); 
        // dd($product);
        $product->status = 'reject';
        $product->save();

        return response()->json(['message' => 'Product reject successfully.']);
    }
    public function view()
    {
        $products = product::all();
        return response()->json($products);
    }

    public function project()
    {
        $project = Project::all();
        return response()->json($project);
    }
    public function event()
    {
        $event = Event::all();
        return response()->json($event);
    }
}
