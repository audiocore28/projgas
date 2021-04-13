<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\TankerLoad;
use App\Models\TankerLoadDetail;
use App\Models\MonthlyMindoroTransaction;
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
        $purchases = Purchase::filter(Request::only('search', 'trashed'))
                ->orderBy('id', 'desc')
                ->paginate()
                ->transform(function ($purchase) {
                    return [
                        'id' => $purchase->id,
                        'date' => $purchase->date,
                        'purchase_no' => $purchase->purchase_no,
                        'supplier' => $purchase->supplier ? $purchase->supplier->only('name') : null,
                        'products' => $purchase->purchaseDetails->each(function ($detail) {
                                return [ 'name' => $detail->product->name, ];
                            }),
                        ];
                });

        return Inertia::render('Purchases/Index', [
            'filters' => Request::all('search', 'trashed'),
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
        $suppliers = Supplier::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        $products = Product::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        $monthlyMindoroTransactions = MonthlyMindoroTransaction::orderBy('id', 'desc')->get();

        return Inertia::render('Purchases/Create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'monthlyMindoroTransactions' => $monthlyMindoroTransactions,
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
            'monthly_mindoro_transaction_id' => $request->monthly_mindoro_transaction_id,
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

        foreach($request->tankerLoads as $load)
        {
            $tankerLoadId = TankerLoad::create([
                'mindoro_transaction_id' => $load['mindoro_transaction_id'],
                'remarks' => $load['remarks'],
                'purchase_id' => $purchaseId,
            ])->id;

            foreach ($load['details'] as $detail) {
                $tankerLoadDetail = TankerLoadDetail::create([
                    'tanker_load_id' => $tankerLoadId,
                    'product_id' => $detail['product']['id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                ]);
            }
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
        $suppliers = Supplier::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        $products = Product::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        $monthlyMindoroTransactions = MonthlyMindoroTransaction::orderBy('id', 'desc')->get();

        $loads = $purchase->tankerLoads
            ->map(function ($load) {
                return [
                   'id' => $load->id,
                   'purchase_id' => $load->purchase_id,
                   'mindoro_transaction_id' => $load->mindoro_transaction_id,
                   'remarks' => $load->remarks,
                   'details' => $load->tankerLoadDetails->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'tanker_load_id' => $detail->tanker_load_id,
                            'product' => $detail->product ? $detail->product->only('id', 'name') : null,
                            'quantity' => $detail->quantity,
                            'unit_price' => $detail->unit_price,
                        ];
                   }),
                   'batangas_transaction' => $load->purchase->batangasTransactions->map(function ($transaction) {
                        return [
                            'trip_no' => $transaction->trip_no,
                            'driver' => $transaction->driver->only('name'),
                        ];
                   }),
                   'mindoro_transaction' => $load->purchase->mindoroTransactions->map(function ($transaction) {
                        return [
                            'trip_no' => $transaction->trip_no,
                            'driver' => $transaction->driver->only('name'),
                        ];
                   }),
                ];
            })
            ->toArray();

        return Inertia::render('Purchases/Edit', [
            'purchase' => [
                'id' => $purchase->id,
                'date' => $purchase->date,
                'purchase_no' => $purchase->purchase_no,
                'supplier_id' => $purchase->supplier_id,
                'monthly_mindoro_transaction_id' => $purchase->monthly_mindoro_transaction_id,
                'details' => $purchase->purchaseDetails,
            ],
            'tanker_loads' => $loads,
            'suppliers' => $suppliers,
            'products' => $products,
            'monthlyMindoroTransactions' => $monthlyMindoroTransactions,
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
        // Purchase
        $purchase->update([
            'date' => $request->date,
            'purchase_no' => $request->purchase_no,
            'supplier_id' => $request->supplier_id,
            'monthly_mindoro_transaction_id' => $request->monthly_mindoro_transaction_id,
        ]);

        // PurchaseDetail
        foreach($request->details as $detail)
        {
            $purchaseDetail = $purchase->purchaseDetails()->findOrNew($detail['id']);

            $purchaseDetail->purchase_id = $detail['purchase_id'];
            $purchaseDetail->product_id = $detail['product_id'];
            $purchaseDetail->quantity = $detail['quantity'];
            $purchaseDetail->unit_price = $detail['unit_price'];
            $purchaseDetail->remarks = $detail['remarks'];
            $purchaseDetail->save();
        }

        // TankerLoad
        foreach($request->tankerLoads as $load)
        {
            $tankerLoad = $purchase->tankerLoads()->findOrNew($load['id']);

            $tankerLoad->mindoro_transaction_id = $load['mindoro_transaction_id'];
            // $tankerLoad->trip_no = $load['trip_no'];
            $tankerLoad->remarks = $load['remarks'];
            $tankerLoad->purchase_id = $load['purchase_id'];
            $tankerLoad->save();

            // TankerLoadDetail
            foreach ($load['details'] as $detail) {

                $loadDetail = $tankerLoad->tankerLoadDetails()->findOrNew($detail['id']);

                $loadDetail->quantity = $detail['quantity'];
                $loadDetail->product_id = $detail['product']['id'];
                $loadDetail->unit_price = $detail['unit_price'];
                $loadDetail->save();
            }
        }

        return Redirect::back()->with('success', 'Purchase updated.');
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

}
