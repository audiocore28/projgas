<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMindoroTransactionRequest;
use App\Models\MindoroTransaction;
use App\Models\MindoroTransactionDetail;
use App\Models\Purchase;
use App\Models\TankerLoad;
use App\Models\Client;
use App\Models\Product;
// use App\Models\Tanker;
// use App\Models\Driver;
// use App\Models\Helper;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class MindoroTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mindoroTransactions = MindoroTransaction::filter(Request::only('search', 'trashed'))
            ->orderBy('id', 'desc')
            ->paginate()
            ->transform(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'trip_no' => $transaction->trip_no,
                    'purchases' => $transaction->purchases ? $transaction->purchases->only('purchase_no') : null,
                    'clients' => $transaction->mindoroTransactionDetails->each(function ($detail) {
                            return [ 'name' => $detail->client->name ];
                        }),
                ];
            });

        return Inertia::render('MindoroTransactions/Index', [
            'filters' => Request::all('search', 'trashed'),
            'mindoro_transactions' => $mindoroTransactions,
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

        $purchases->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'date' => $purchase->date,
                    'purchase_no' => $purchase->purchase_no,
                    'supplier' => $purchase->supplier ? $purchase->supplier->only('name') : null,
                    'loads' => $purchase->tankerLoads->each(function ($load) {
                            return [
                                'date' => $load->date,
                                'trip_no' => $load->trip_no,
                                'remarks' => $load->remarks,
                                'purchase' => $load->purchase->purchase_no,
                                'tanker' => $load->tanker->plate_no,
                                'driver' => $load->driver->name,
                                'helper' => $load->helper->name,
                                'details' => $load->tankerLoadDetails->each(function ($detail) {
                                    return [
                                        'quantity' => $detail->quantity,
                                        'product' => $detail->product->name,
                                        'unit_price' => $detail->unit_price,
                                    ];
                                }),
                            ];
                        }),
                    ];
                });

        $clients = Client::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->get();

        $products = Product::orderBy('name', 'asc')->get();

        return Inertia::render('MindoroTransactions/Create', [
            'clients' => $clients,
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
    public function store(StoreMindoroTransactionRequest $request)
    {
        $mindoroTransactionId = MindoroTransaction::create([
            'trip_no' => $request->trip_no,
        ])->id;

        $mindoroTransaction = MindoroTransaction::find($mindoroTransactionId);
        $mindoroTransaction->purchases()->sync($request->purchases);

        foreach($request->details as $detail)
        {
            $mindoroTransactionDetail = MindoroTransactionDetail::create([
                'mindoro_transaction_id' => $mindoroTransactionId,
                'date' => $detail['date'],
                'dr_no' => $detail['dr_no'],
                'client_id' => $detail['client_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
            ]);
        }

        return redirect()->route('mindoro-transactions.index')->with('success', 'Mindoro Transaction was successfully added.');
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
    public function edit(MindoroTransaction $mindoroTransaction)
    {
        // Dropdowns ------------------

        // Purchases
        $purchases = Purchase::when(request('term'), function($query, $term) {
            $query->where('purchase_no', 'like', "%$term%");
        })->get();

        $purchases->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'date' => $purchase->date,
                    'purchase_no' => $purchase->purchase_no,
                    'supplier' => $purchase->supplier ? $purchase->supplier->only('name') : null,
                    'tanker_loads' => $purchase->tankerLoads->each(function ($load) {
                            return [
                                'date' => $load->date,
                                'trip_no' => $load->trip_no,
                                'remarks' => $load->remarks,
                                'purchase' => $load->purchase->purchase_no,
                                'tanker' => $load->tanker->plate_no,
                                'driver' => $load->driver->name,
                                'helper' => $load->helper->name,
                                'tanker_load_details' => $load->tankerLoadDetails->each(function ($detail) {
                                    return [
                                        'quantity' => $detail->quantity,
                                        'product' => $detail->product->name,
                                        'unit_price' => $detail->unit_price,
                                    ];
                                }),
                            ];
                        }),
                    ];
                });

        // Clients
        $clients = Client::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->get();

        // Products
        $products = Product::orderBy('name', 'asc')->get();


        // Mindoro Transaction Queries ------------------

        // Selected Purchases
        $selectedPurchases = $mindoroTransaction->purchases->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'date' => $purchase->date,
                    'purchase_no' => $purchase->purchase_no,
                    'supplier' => $purchase->supplier ? $purchase->supplier->only('name') : null,
                    'tanker_loads' => $purchase->tankerLoads->each(function ($load) {
                            return [
                                'date' => $load->date,
                                'trip_no' => $load->trip_no,
                                'remarks' => $load->remarks,
                                'purchase' => $load->purchase->purchase_no,
                                'tanker' => $load->tanker->plate_no,
                                'driver' => $load->driver->name,
                                'helper' => $load->helper->name,
                                'tanker_load_details' => $load->tankerLoadDetails->each(function ($detail) {
                                    return [
                                        'quantity' => $detail->quantity,
                                        'product' => $detail->product->name,
                                        'unit_price' => $detail->unit_price,
                                    ];
                                }),
                            ];
                        }),
                    ];
                });

        // Details
        $details = $mindoroTransaction->mindoroTransactionDetails
                ->map(function ($detail) {
                    return [
                       'id' => $detail->id,
                       'date' => $detail->date,
                       'dr_no' => $detail->dr_no,
                       'quantity' => $detail->quantity,
                       'unit_price' => $detail->unit_price,
                       'mindoro_transaction_id' => $detail->mindoro_transaction_id,
                       'product_id' => $detail->product_id,
                       'client_id' => $detail->client_id,
                       'selected_client' => $detail->client_id,
                    ];
                })
                ->toArray();



        return Inertia::render('MindoroTransactions/Edit', [
            'mindoro_transaction' => [
                'id' => $mindoroTransaction->id,
                'trip_no' => $mindoroTransaction->trip_no,
                'selected_purchases' => $selectedPurchases,
                'details' => $details,
            ],
            'purchases' => $purchases,
            'clients' => $clients,
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
    public function update(StoreMindoroTransactionRequest $request, MindoroTransaction $mindoroTransaction)
    {
        // Mindoro Transaction
        $mindoroTransaction->update([
            'trip_no' => $request->trip_no,
        ]);

        $mindoroTransaction->purchases()->sync($request->purchases);

        // Details
        $existingDetails = collect($request->details)->filter(function($value){
            return $value['id'] !== null;
        });

        foreach($existingDetails as $detail)
        {
            $mindoroTransaction->mindoroTransactionDetails()->find($detail['id'])->update([
                // 'mindoroTransaction_id' => $detail['mindoroTransaction_id'],
                'date' => $detail['date'],
                'dr_no' => $detail['dr_no'],
                'client_id' => $detail['client_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
            ]);
        }

        return Redirect::back()->with('success', 'Mindoro Transaction updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MindoroTransaction $mindoroTransaction)
    {
        $mindoroTransaction->delete();

        return redirect()->route('mindoro-transactions.index')->with('success', 'Mindoro Transaction deleted.');
    }

}
