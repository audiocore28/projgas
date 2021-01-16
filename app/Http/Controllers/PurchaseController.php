<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\TankerLoad;
use App\Models\Delivery;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchases = Purchase::filter(Request::only('search'))
                ->orderBy('id', 'desc')
                ->paginate(5)
                ->transform(function ($purchase) {
                    return [
                        'id' => $purchase->id,
                        'date' => $purchase->date,
                        'purchase_no' => $purchase->purchase_no,
                        'supplier' => $purchase->supplier ? $purchase->supplier->only('name') : null,
                    ];
                });

        return Inertia::render('Purchases/Index', [
            'filters' => Request::all('search'),
            'purchases' => $purchases,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();

        return Inertia::render('Purchases/Create', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseRequest $request)
    {
        $purchaseId = Purchase::create([
            'date' => $request->date,
            'purchase_no' => $request->purchase_no,
            'supplier_id' => $request->supplier_id,
        ])->id;


        foreach($request->details as $detail)
        {
            $purchaseDetail = PurchaseDetail::create([
                'purchase_id' => $purchaseId,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
                'remarks' => $detail['remarks'],
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase was successfully added.');
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
    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::all();
        $products = Product::all();

        // each, foreach, where, whereHas, get, return, has, filter, with

        $pD = $purchase->purchaseDetails->transform(function ($detail) {
            return [
                'quantity' => $detail->quantity,
                'unit_price' => $detail->unit_price,
                'remarks' => $detail->remarks,
                'purchase_id' => $detail->purchase_id,
                'product' => $detail->product ? $detail->product->only('id', 'name') : null,
                // 'supplier' => $detail->supplier ? $detail->supplier->only('id', 'name') : null,
            ];
        });

        $lD = $purchase->tankerLoads->transform(function ($load) {
            return [
                // 'date' => $load->date,
                // 'status' => $load->status,
                // 'remarks' => $load->remarks,
                // 'purchase_id' => $load->purchase_id,
                // 'tanker_id' => $load->tanker_id,
                'tanker' => $load->tanker ? $load->tanker->only('plate_no') : null,
                'driver' => $load->driver ? $load->driver->only('name') : null,
                'helper' => $load->helper ? $load->helper->only('name') : null,
                // 'loads' => $load->tankerLoadDetails ? $load->tankerLoadDetails : null,
                'loads' => [
                    'load' => $load->tankerLoadDetails->each(function ($detail) {
                        return $detail->product->name;
                    })
                ]
            ];
        });

        $dD = $purchase->deliveries->transform(function ($delivery) {
            return [
                // 'delivery_id' => $delivery->delivery_id,
                // 'product_id' => $delivery->product_id,
                // 'quantity' => $delivery->quantity,
                // 'unit_price' => $delivery->unit_price,
                'tanker' => $delivery->tanker ? $delivery->tanker->only('plate_no') : null,
                'driver' => $delivery->driver ? $delivery->driver->only('name') : null,
                'helper' => $delivery->helper ? $delivery->helper->only('name') : null,
                // 'deliveries' => $delivery->deliveryDetails ? $delivery->deliveryDetails : null,
                'deliveries' => [
                    'delivery' => $delivery->deliveryDetails->each(function ($detail) {
                        return ['p' => $detail->product->name, 'c' => $detail->client->name];
                    })
                ]
            ];
        });

       $keys = collect(['purchases', 'loads', 'deliveries']);
       $figures = $keys->combine([$pD, $lD, $dD]);

        return Inertia::render('Purchases/Edit', [
            'purchase' => [
                'id' => $purchase->id,
                'date' => $purchase->date,
                'purchase_no' => $purchase->purchase_no,
                'supplier_id' => $purchase->supplier_id,
                'details' => $purchase->purchaseDetails,
            ],
            'suppliers' => $suppliers,
            'products' => $products,
            'figures' => $figures,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePurchaseRequest $request, Purchase $purchase)
    {
        // dd($purchase->purchaseDetails);

        // Purchase
        $purchase->update([
            'date' => $request->date,
            'purchase_no' => $request->purchase_no,
            'supplier_id' => $request->supplier_id,
        ]);

        // PurchaseDetail
        $existingDetails = collect($request->details)->filter(function($value){
            return $value['id'] !== null;
        });

        foreach($existingDetails as $detail)
        {
            $purchase->purchaseDetails()->find($detail['id'])->update([
                // 'purchase_id' => $detail['purchase_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
                'remarks' => $detail['remarks'],
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted.');
    }


    public function restore(Purchase $purchase)
    {
        $purchase->restore();

        return redirect()->route('purchases.index')->with('success', 'Purchase restored.');
    }
}
