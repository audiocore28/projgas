<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTankerLoadRequest;
use App\Models\TankerLoad;
use App\Models\TankerLoadDetail;
use App\Models\Purchase;
use App\Models\Product;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class TankerLoadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tankerLoad = TankerLoad::filter(Request::only('search', 'trashed'))
            // , 'range'
            // ->select('id', 'date', 'purchase_id', 'tanker_id', 'driver_id', 'helper_id')
            ->orderBy('id', 'desc')
            ->paginate()
            ->transform(function ($tankerLoad) {
                return [
                    'id' => $tankerLoad->id,
                    'date' => $tankerLoad->date,
                    'trip_no' => $tankerLoad->trip_no,
                    // 'remarks' => $tankerLoad->remarks,
                    'purchase' => $tankerLoad->purchase ? $tankerLoad->purchase->only('purchase_no') : null,
                    'tanker' => $tankerLoad->tanker ? $tankerLoad->tanker->only('plate_no') : null,
                    'driver' => $tankerLoad->driver ? $tankerLoad->driver->only('name') : null,
                    'helper' => $tankerLoad->helper ? $tankerLoad->helper->only('name') : null,
                    'products' => $tankerLoad->tankerLoadDetails->each(function ($detail) {
                            return [ 'name' => $detail->product->name, ];
                        }),
                ];
            });

        return Inertia::render('TankerLoads/Index', [
            'filters' => Request::all('search', 'trashed'),
            // , 'range'
            'tanker_loads' => $tankerLoad,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $purchases = Purchase::when(request('term'), function($query, $term) {
            $query->where('purchase_no', 'like', "%$term%");
        })->get();

        // $purchases->map(function ($purchase) {
        //         return [
        //             'id' => $purchase->id,
        //             'date' => $purchase->date,
        //             'purchase_no' => $purchase->purchase_no,
        //             'supplier' => $purchase->supplier ? $purchase->supplier->only('name') : null,
        //             'purchase_details' => $purchase->purchaseDetails->each(function ($detail) {
        //                     return [
        //                         'product' => $detail->product ? $detail->product->only('name') : null,
        //                         'quantity' => $detail->quantity,
        //                         'unit_price' => $detail->unit_price,
        //                         'remarks' => $detail->remarks,
        //                     ];
        //                 }),
        //             ];
        //         });

        $products = Product::orderBy('name', 'asc')->get();

        return Inertia::render('TankerLoads/Create', [
            'purchases' => $purchases,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTankerLoadRequest $request)
    {
        $tankerLoadId = TankerLoad::create([
            'trip_no' => $request->trip_no,
            'remarks' => $request->remarks,
            'purchase_id' => $request->purchase_id,
        ])->id;


        foreach($request->details as $detail)
        {
            $tankerLoadDetail = TankerLoadDetail::create([
                'tanker_load_id' => $tankerLoadId,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
            ]);
        }

        return redirect()->route('tanker-loads.index')->with('success', 'Load was successfully added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TankerLoad $tankerLoad)
    {
        $purchases = Purchase::when(request('term'), function($query, $term) {
            $query->where('purchase_no', 'like', "%$term%");
        })->get();
        $products = Product::orderBy('name', 'asc')->get();

        return Inertia::render('TankerLoads/Edit', [
            'tanker_load' => [
                'id' => $tankerLoad->id,
                'date' => $tankerLoad->date,
                'trip_no' => $tankerLoad->trip_no,
                'remarks' => $tankerLoad->remarks,
                'purchase_id' => $tankerLoad->purchase_id,
                'details' => $tankerLoad->tankerLoadDetails,
            ],
            'purchases' => $purchases,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTankerLoadRequest $request, TankerLoad $tankerLoad)
    {
        // TankerLoad
        $tankerLoad->update([
            'date' => $request->date,
            'trip_no' => $request->trip_no,
            'remarks' => $request->remarks,
            'purchase_id' => $request->purchase_id,
        ]);

        // TankerLoadDetail
        $existingDetails = collect($request->details)->filter(function($value){
            return $value['id'] !== null;
        });

        foreach($existingDetails as $detail)
        {
            $tankerLoad->tankerLoadDetails()->find($detail['id'])->update([
                // 'load_id' => $detail['load_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
            ]);
        }

        return Redirect::back()->with('success', 'Load updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TankerLoad $tankerLoad)
    {
        $tankerLoad->delete();

        return Redirect::back()->with('success', 'Load deleted.');
    }

}
