<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Depot;
use App\Models\Account;
use App\Models\Product;
use App\Models\ToBatangasLoad;
use App\Models\ToBatangasLoadDetail;
use App\Models\ToMindoroLoad;
use App\Models\ToMindoroLoadDetail;
use App\Models\MonthlyMindoroTransaction;
use App\Models\MonthlyBatangasTransaction;
use App\Models\MindoroTransaction;
use App\Models\BatangasTransaction;
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
            'purchases' => $query->with('supplier', 'depot', 'account', 'purchaseDetails.product')->latest('date')->paginate(),
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

        $depots = Depot::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        $accounts = Account::orderBy('name', 'asc')
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
            'depots' => $depots,
            'accounts' => $accounts,
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
            'depot_id' => $request->depot_id,
            'account_id' => $request->account_id,
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
            $batangasLoadId = ToBatangasLoad::create([
                'batangas_transaction_id' => $load['batangas_transaction_id'],
                'remarks' => $load['remarks'],
                'purchase_id' => $purchaseId,
            ])->id;

            foreach ($load['details'] as $detail) {
                $batangasLoadDetail = ToBatangasLoadDetail::create([
                    'to_batangas_load_id' => $batangasLoadId,
                    'product_id' => $detail['product']['id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                ]);
            }
        }

        foreach($request->mindoroLoads as $load)
        {
            $mindoroLoadId = ToMindoroLoad::create([
                'mindoro_transaction_id' => $load['mindoro_transaction_id'],
                'remarks' => $load['remarks'],
                'purchase_id' => $purchaseId,
            ])->id;

            foreach ($load['details'] as $detail) {
                $mindoroLoadDetail = ToMindoroLoadDetail::create([
                    'to_mindoro_load_id' => $mindoroLoadId,
                    'product_id' => $detail['product']['id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                ]);
            }
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase was successfully added.');
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

        $depots = Depot::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        $accounts = Account::orderBy('name', 'asc')
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

        $batangasLoads = $purchase->toBatangasLoads
            ->map(function ($load) {
                return [
                   'id' => $load->id,
                   'purchase_id' => $load->purchase_id,
                   'monthly_batangas_transaction_id' => $load->batangasTransaction ? $load->batangasTransaction->monthlyBatangasTransaction->id : null,
                   'batangas_transaction_id' => BatangasTransaction::where('id', $load->batangas_transaction_id)->exists() ? $load->batangas_transaction_id : null,
                   'remarks' => $load->remarks,
                   'details' => $load->toBatangasLoadDetails->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'to_batangas_load_id' => $detail->to_batangas_load_id,
                            'product' => $detail->product ? $detail->product->only('id', 'name') : null,
                            'quantity' => $detail->quantity,
                            'unit_price' => $detail->unit_price,
                        ];
                   }),
                ];
            })
            ->toArray();

        $mindoroLoads = $purchase->toMindoroLoads
            ->map(function ($load) {
                return [
                   'id' => $load->id,
                   'purchase_id' => $load->purchase_id,
                   'monthly_mindoro_transaction_id' => $load->mindoroTransaction ? $load->mindoroTransaction->monthlyMindoroTransaction->id : null,
                   'mindoro_transaction_id' => MindoroTransaction::where('id', $load->mindoro_transaction_id)->exists() ? $load->mindoro_transaction_id : null,
                   'remarks' => $load->remarks,
                   'details' => $load->toMindoroLoadDetails->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'to_mindoro_load_id' => $detail->to_mindoro_load_id,
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
                'depot_id' => $purchase->depot_id,
                'account_id' => $purchase->account_id,
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
            'depots' => $depots,
            'accounts' => $accounts,
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
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        // Purchase
        $purchase->update([
            'date' => $request->date,
            'purchase_no' => $request->purchase_no,
            'supplier_id' => $request->supplier_id,
            'depot_id' => $request->depot_id,
            'account_id' => $request->account_id,
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

        // ToBatangasLoad
        foreach($request->batangasLoads as $load)
        {
            $batangasLoad = $purchase->toBatangasLoads()->findOrNew($load['id']);

            $batangasLoad->batangas_transaction_id = $load['batangas_transaction_id'];
            $batangasLoad->remarks = $load['remarks'];
            $batangasLoad->purchase_id = $load['purchase_id'];
            $batangasLoad->save();

            // ToBatangasLoadDetail
            foreach ($load['details'] as $detail)
            {
                $loadDetail = $batangasLoad->toBatangasLoadDetails()->findOrNew($detail['id']);

                $loadDetail->quantity = $detail['quantity'];
                $loadDetail->product_id = $detail['product']['id'];
                $loadDetail->unit_price = $detail['unit_price'];
                $loadDetail->save();
            }
        }

        // ToMindoroLoad
        foreach($request->mindoroLoads as $load)
        {
            $mindoroLoad = $purchase->toMindoroLoads()->findOrNew($load['id']);

            $mindoroLoad->mindoro_transaction_id = $load['mindoro_transaction_id'];
            // $mindoroLoad->trip_no = $load['trip_no'];
            $mindoroLoad->remarks = $load['remarks'];
            $mindoroLoad->purchase_id = $load['purchase_id'];
            $mindoroLoad->save();

            // ToMindoroLoadDetail
            foreach ($load['details'] as $detail)
            {
                $loadDetail = $mindoroLoad->toMindoroLoadDetails()->findOrNew($detail['id']);

                $loadDetail->quantity = $detail['quantity'];
                $loadDetail->product_id = $detail['product']['id'];
                $loadDetail->unit_price = $detail['unit_price'];
                $loadDetail->save();
            }
        }

        // PurchaseDetail
        $this->deletePurchaseDetail($request->removed_purchase_details);
        // ToBatangasLoad
        $this->deleteBatangasLoad($request->removed_batangas_loads);
        $this->deleteBatangasLoadDetail($request->removed_batangas_load_details);
        // ToMindoroLoad
        $this->deleteMindoroLoad($request->removed_mindoro_loads);
        $this->deleteMindoroLoadDetail($request->removed_mindoro_load_details);

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

    public function deleteBatangasLoad($loadIds)
    {
        ToBatangasLoad::whereIn('id', $loadIds)->delete();
    }

    public function deleteBatangasLoadDetail($loadDetailIds)
    {
        ToBatangasLoadDetail::whereIn('id', $loadDetailIds)->delete();
    }

    public function deleteMindoroLoad($loadIds)
    {
        ToMindoroLoad::whereIn('id', $loadIds)->delete();
    }

    public function deleteMindoroLoadDetail($loadDetailIds)
    {
        ToMindoroLoadDetail::whereIn('id', $loadDetailIds)->delete();
    }

    public function print(Purchase $purchase)
    {
        $batangasLoads = $purchase->toBatangasLoads
            ->map(function ($load) {
                return [
                   'id' => $load->id,
                   'purchase' => $load->purchase ? $load->purchase->only('id', 'purchase_no') : null,
                   'trip_no' => BatangasTransaction::where('id', $load->batangas_transaction_id)->exists() ? $load->batangasTransaction->trip_no : null,
                   'driver' => BatangasTransaction::where('id', $load->batangas_transaction_id)->exists() ? $load->batangasTransaction->driver->name : null,
                   'remarks' => $load->remarks,
                   'details' => $load->toBatangasLoadDetails->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'to_batangas_load_id' => $detail->to_batangas_load_id,
                            'product' => $detail->product ? $detail->product->only('id', 'name') : null,
                            'quantity' => $detail->quantity,
                            'unit_price' => $detail->unit_price,
                        ];
                   }),
                ];
            })
            ->toArray();

        $mindoroLoads = $purchase->toMindoroLoads
            ->map(function ($load) {
                return [
                   'id' => $load->id,
                   'purchase_id' => $load->purchase_id,
                   'trip_no' => MindoroTransaction::where('id', $load->mindoro_transaction_id)->exists() ? $load->mindoroTransaction->trip_no : null,
                   'driver' => MindoroTransaction::where('id', $load->mindoro_transaction_id)->exists() ? $load->mindoroTransaction->driver->name : null,
                   'remarks' => $load->remarks,
                   'details' => $load->toMindoroLoadDetails->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'to_mindoro_load_id' => $detail->to_mindoro_load_id,
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

        $purchases = $query->orderBy('id', 'desc')->get();

        $pdf = PDF::loadView('print-purchases', compact('purchases'));
        $pdf->setPaper(array(0, 0, 612.00, 792.0));

        $fileName = "Purchases - ". Carbon::parse(request('start'))->format('m/d/Y') . " to " . Carbon::parse(request('end'))->format('m/d/Y') . ".pdf";
        return $pdf->stream($fileName);
    }

}
