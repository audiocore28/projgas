<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Products/Index', [
            'filters' => Request::all('search', 'trashed'),
            'products' => Product::filter(Request::only('search', 'trashed'))
                ->orderBy('id', 'desc')
                ->paginate()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Products/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product was successfully added.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'deleted_at' => $product->deleted_at,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->all());

        return Redirect::back()->with('success', 'Product updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->purchaseDetails()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, purchase has product records']);
        }

        if ($product->batangasTransactionDetails()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, Batangas Transaction has product records']);
        }

        if ($product->mindoroTransactionDetails()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, Mindoro Transaction has product records']);
        }

        $product->delete();

        return Redirect::back()->with('success', 'Product deleted.');
    }


    public function restore(Product $product)
    {
        $product->restore();

        return Redirect::back()->with('success', 'Product restored.');
    }
}
