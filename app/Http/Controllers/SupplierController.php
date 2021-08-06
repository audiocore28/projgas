<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Supplier::class, 'supplier');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Suppliers/Index', [
            'filters' => Request::all('search', 'trashed'),
            'suppliers' => Supplier::filter(Request::only('search', 'trashed'))
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
        return Inertia::render('Suppliers/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSupplierRequest $request)
    {
        $supplier = Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier was successfully added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        $query = $supplier->load([
            'purchases' => function ($query) {
                $query->select(['id', 'supplier_id', 'date', 'purchase_no'])->orderBy('date', 'DESC'); // fields from purchases table
                // supplier_id is needed so eloquent can match the purchase with its parent supplier
            },
            'purchases.purchaseDetails.product:id,name'
        ]);
        // ->get(['id', 'name']) // fields from suppliers table
        // ->toArray();

        $supplier = collect($query)->toArray();

       // if (request()->wantsJson()) {
       //   return [
       //      'purchaseDetails' => $supplier,
       //   ];
       // }

        return Inertia::render('Suppliers/Show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        return Inertia::render('Suppliers/Edit', [
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'office' => $supplier->office,
                'email_address' => $supplier->email_address,
                'contact_person' => $supplier->contact_person,
                'contact_no' => $supplier->contact_no,
                'deleted_at' => $supplier->deleted_at,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->all());

        return Redirect::back()->with('success', 'Supplier updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, purchase has supplier records']);
        }

        $supplier->delete();

        return Redirect::back()->with('success', 'Supplier deleted.');
    }


    public function restore(Supplier $supplier)
    {
        $supplier->restore();

        return Redirect::back()->with('success', 'Supplier restored.');
    }
}
