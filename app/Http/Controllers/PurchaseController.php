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
use App\Models\MonthlyBatangasTransaction;
use App\Models\Delivery;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;
use Inertia\Inertia;
use PDF;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Purchase::class, 'purchase');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Purchase::query();

        if (request('range')) {
            $dateRange = request('range');
            $start = Carbon::parse($dateRange[0])->startOfDay();
            $end = Carbon::parse($dateRange[1])->endOfDay();

            $query->whereBetween('date', [$start, $end]);
        }

        if (request('search')) {
            $query->where('purchase_no', 'like', '%'.request('search').'%');
                    // ->orWhereHas('supplier', function ($query) {
                    //     $query->where('name', 'like', '%'.request('search').'%');
                    // });
        }

        return Inertia::render('Purchases/Index', [
            'filters' => Request::all('search', 'range', 'trashed'),
            'purchases' => $query->with('supplier', 'purchaseDetails.product')->orderBy('id', 'desc')->paginate(),
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
        $monthlyBatangasTransactions = MonthlyBatangasTransaction::orderBy('id', 'desc')->get();

        return Inertia::render('Purchases/Create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'monthlyMindoroTransactions' => $monthlyMindoroTransactions,
            'monthlyBatangasTransactions' => $monthlyBatangasTransactions,
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
            'monthly_batangas_transaction_id' => $request->monthly_batangas_transaction_id,
        ])->id;


        foreach($request->details as $detail)
        {
            $purchaseDetail = PurchaseDetail::create([
                'purchase_id' => $purchaseId,
                'product_id' => $detail['product']['id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
                'remarks' => $detail['remarks'],
            ]);
        }

        foreach($request->batangasLoads as $load)
        {
            $tankerLoadId = TankerLoad::create([
                'batangas_transaction_id' => $load['batangas_transaction_id'],
                'mindoro_transaction_id' => 0,
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

        foreach($request->mindoroLoads as $load)
        {
            $tankerLoadId = TankerLoad::create([
                'batangas_transaction_id' => 0,
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
        $monthlyBatangasTransactions = MonthlyBatangasTransaction::orderBy('id', 'desc')->get();

        // $loads = $purchase->tankerLoads
        //     ->map(function ($load) {
        //         return [
        //            'id' => $load->id,
        //            'purchase_id' => $load->purchase_id,
        //            'mindoro_transaction_id' => $load->mindoro_transaction_id,
        //            'batangas_transaction_id' => $load->batangas_transaction_id,
        //            'remarks' => $load->remarks,
        //            'details' => $load->tankerLoadDetails->map(function ($detail) {
        //                 return [
        //                     'id' => $detail->id,
        //                     'tanker_load_id' => $detail->tanker_load_id,
        //                     'product' => $detail->product ? $detail->product->only('id', 'name') : null,
        //                     'quantity' => $detail->quantity,
        //                     'unit_price' => $detail->unit_price,
        //                 ];
        //            }),
        //            'batangas_transaction' => $load->purchase->batangasTransactions->map(function ($transaction) {
        //                 return [
        //                     'trip_no' => $transaction->trip_no,
        //                     'driver' => $transaction->driver->only('name'),
        //                 ];
        //            }),
        //            'mindoro_transaction' => $load->purchase->mindoroTransactions->map(function ($transaction) {
        //                 return [
        //                     'trip_no' => $transaction->trip_no,
        //                     'driver' => $transaction->driver->only('name'),
        //                 ];
        //            }),
        //         ];
        //     })
        //     ->toArray();

        $batangasLoads = $purchase->batangasLoads
            ->map(function ($load) {
                return [
                   'id' => $load->id,
                   'purchase_id' => $load->purchase_id,
                   'mindoro_transaction_id' => $load->mindoro_transaction_id,
                   'monthly_batangas_transaction' => $load->batangasTransaction ? $load->batangasTransaction->monthlyBatangasTransaction->only('id') : null,
                   'batangas_transaction_id' => $load->batangas_transaction_id,
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
                ];
            })
            ->toArray();

        $mindoroLoads = $purchase->mindoroLoads
            ->map(function ($load) {
                return [
                   'id' => $load->id,
                   'purchase_id' => $load->purchase_id,
                   'monthly_mindoro_transaction' => $load->mindoroTransaction ? $load->mindoroTransaction->monthlyMindoroTransaction->only('id') : null,
                   'mindoro_transaction_id' => $load->mindoro_transaction_id,
                   'batangas_transaction_id' => $load->batangas_transaction_id,
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
                'monthly_batangas_transaction_id' => $purchase->monthly_batangas_transaction_id,
                'details' => $purchase->purchaseDetails
                        ->map(function ($detail) {
                            return [
                                'id' => $detail->id,
                                'quantity' => $detail->quantity,
                                'unit_price' => $detail->unit_price,
                                'remarks' => $detail->remarks,
                                'purchase_id' => $detail->purchase_id,
                                'product' => $detail->product ? $detail->product->only('id', 'name') : null,
                            ];
                        }),
                'batangasLoads' => $batangasLoads,
                'mindoroLoads' => $mindoroLoads,
            ],
            // 'tanker_loads' => $loads,
            'suppliers' => $suppliers,
            'products' => $products,
            'monthlyMindoroTransactions' => $monthlyMindoroTransactions,
            'monthlyBatangasTransactions' => $monthlyBatangasTransactions,
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
            'monthly_batangas_transaction_id' => $request->monthly_batangas_transaction_id,
        ]);

        // PurchaseDetail
        foreach($request->details as $detail)
        {
            $purchaseDetail = $purchase->purchaseDetails()->findOrNew($detail['id']);

            $purchaseDetail->purchase_id = $detail['purchase_id'];
            $purchaseDetail->product_id = $detail['product']['id'];
            $purchaseDetail->quantity = $detail['quantity'];
            $purchaseDetail->unit_price = $detail['unit_price'];
            $purchaseDetail->remarks = $detail['remarks'];
            $purchaseDetail->save();
        }

        // BatangasLoad
        foreach($request->batangasLoads as $load)
        {
            $batangasLoad = $purchase->batangasLoads()->findOrNew($load['id']);

            $batangasLoad->batangas_transaction_id = $load['batangas_transaction_id'];
            $batangasLoad->mindoro_transaction_id = $load['mindoro_transaction_id'];
            $batangasLoad->remarks = $load['remarks'];
            $batangasLoad->purchase_id = $load['purchase_id'];
            $batangasLoad->save();

            // TankerLoadDetail
            foreach ($load['details'] as $detail)
            {
                $loadDetail = $batangasLoad->tankerLoadDetails()->findOrNew($detail['id']);

                $loadDetail->quantity = $detail['quantity'];
                $loadDetail->product_id = $detail['product']['id'];
                $loadDetail->unit_price = $detail['unit_price'];
                $loadDetail->save();
            }
        }

        // MindoroLoad
        foreach($request->mindoroLoads as $load)
        {
            $mindoroLoad = $purchase->mindoroLoads()->findOrNew($load['id']);

            $mindoroLoad->mindoro_transaction_id = $load['mindoro_transaction_id'];
            $mindoroLoad->batangas_transaction_id = $load['batangas_transaction_id'];
            // $mindoroLoad->trip_no = $load['trip_no'];
            $mindoroLoad->remarks = $load['remarks'];
            $mindoroLoad->purchase_id = $load['purchase_id'];
            $mindoroLoad->save();

            // TankerLoadDetail
            foreach ($load['details'] as $detail)
            {
                $loadDetail = $mindoroLoad->tankerLoadDetails()->findOrNew($detail['id']);

                $loadDetail->quantity = $detail['quantity'];
                $loadDetail->product_id = $detail['product']['id'];
                $loadDetail->unit_price = $detail['unit_price'];
                $loadDetail->save();
            }
        }

        $this->deletePurchaseDetail($request->removed_purchase_details);
        $this->deleteTankerLoad($request->removed_loads);
        $this->deleteTankerLoadDetail($request->removed_load_details);

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

    public function deletePurchaseDetail($purchaseDetailIds)
    {
        PurchaseDetail::whereIn('id', $purchaseDetailIds)->delete();
    }

    public function deleteTankerLoad($loadIds)
    {
        TankerLoad::whereIn('id', $loadIds)->delete();
    }

    public function deleteTankerLoadDetail($loadDetailIds)
    {
        TankerLoadDetail::whereIn('id', $loadDetailIds)->delete();
    }

    public function print(Purchase $purchase)
    {
        $batangasLoads = $purchase->batangasLoads
            ->map(function ($load) {
                return [
                   'id' => $load->id,
                   'purchase' => $load->purchase ? $load->purchase->only('id', 'purchase_no') : null,
                   'trip_no' => $load->batangasTransaction->trip_no,
                   'driver' => $load->batangasTransaction->driver->name,
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
                ];
            })
            ->toArray();

        $mindoroLoads = $purchase->mindoroLoads
            ->map(function ($load) {
                return [
                   'id' => $load->id,
                   'purchase_id' => $load->purchase_id,
                   'trip_no' => $load->mindoroTransaction->trip_no,
                   'driver' => $load->mindoroTransaction->driver->name,
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
                ];
            })
            ->toArray();


        $pdf = PDF::loadView('print-purchase', compact('purchase', 'batangasLoads', 'mindoroLoads'));
        $pdf->setPaper(array(0, 0, 612.00, 792.0));

        $fileName = "Purchase - ".$purchase->purchase_no.".pdf";
        return $pdf->stream($fileName);
    }

    public function prints()
    {
        $query = Purchase::query();

        if (request('start') && request('end')) {
            $start = Carbon::parse(request('start'))->startOfDay();
            $end = Carbon::parse(request('end'))->endOfDay();

            $query->whereBetween('date', [$start, $end]);
        }

        if (request('search')) {
            $query->where('purchase_no', 'like', '%'.request('search').'%');
                    // ->orWhereHas('supplier', function ($query) {
                    //     $query->where('name', 'like', '%'.request('search').'%');
                    // });
        }

        $purchases = $query->get();

        $pdf = PDF::loadView('print-purchases', compact('purchases'));
        $pdf->setPaper(array(0, 0, 612.00, 792.0));

        $fileName = "Purchases - ". Carbon::parse(request('start'))->format('m/d/Y') . " to " . Carbon::parse(request('end'))->format('m/d/Y') . ".pdf";
        return $pdf->stream($fileName);
    }

}
