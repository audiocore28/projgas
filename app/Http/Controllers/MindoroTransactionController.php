<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMindoroTransactionRequest;
use App\Models\MindoroTransaction;
use App\Models\MindoroTransactionDetail;
use App\Models\Purchase;
use App\Models\TankerLoad;
use App\Models\TankerLoadDetail;
use App\Models\Client;
use App\Models\Product;
use App\Models\Tanker;
use App\Models\Driver;
use App\Models\Helper;
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
                    'date' => $transaction->date,
                    'trip_no' => $transaction->trip_no,
                    'tanker' => $transaction->tanker ? $transaction->tanker->only('plate_no') : null,
                    'driver' => $transaction->driver ? $transaction->driver->only('name') : null,
                    'helper' => $transaction->helper ? $transaction->helper->only('name') : null,
                    'purchases' => $transaction->purchases->each(function ($purchase) {
                            return ['purchase_no' => $purchase->purchase_no, ];
                        }),
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
        $tankers = Tanker::orderBy('plate_no', 'asc')
            ->get()
            ->map
            ->only('id', 'plate_no');

        $drivers = Driver::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        $helpers = Helper::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        $products = Product::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        return Inertia::render('MindoroTransactions/Create', [
            'tankers' => $tankers,
            'drivers' => $drivers,
            'helpers' => $helpers,
            'products' => $products,
            'clients' => function () {
                $clients = Client::when(request('term'), function($query, $term) {
                    $query->where('name', 'like', "%$term%");
                })->orderBy('name', 'asc')->get();

                return $clients;
            },
            'purchases' => function () {
                $purchases = Purchase::when(request('term'), function($query, $term) {
                    $query->where('purchase_no', 'like', "%$term%");
                })->orderBy('id', 'desc')->get();

                $purchases->map(function ($purchase) {
                        return [
                            'id' => $purchase->id,
                            'date' => $purchase->date,
                            'purchase_no' => $purchase->purchase_no,
                            'supplier' => $purchase->supplier ? $purchase->supplier->only('name') : null,
                            'loads' => $purchase->tankerLoads->each(function ($load) {
                                    return [
                                        'trip_no' => $load->trip_no,
                                        'remarks' => $load->remarks,
                                        'purchase' => $load->purchase->purchase_no,
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

                return $purchases;
            },
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
            'date' => $request->date,
            'trip_no' => $request->trip_no,
            'tanker_id' => $request->tanker_id,
            'driver_id' => $request->driver_id,
            'helper_id' => $request->helper_id,
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

        foreach($request->selectedPurchases as $purchase)
        {
           foreach ($purchase['tanker_loads'] as $load)
           {
                foreach ($load['tanker_load_details'] as $detail)
                {
                    $loadDetail = TankerLoadDetail::find($detail['id']);
                    $loadDetail->unit_price = $detail['unit_price'];
                    $loadDetail->save();
                }
           }
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
        })->orderBy('id', 'desc')->get();

        $purchases->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'date' => $purchase->date,
                    'purchase_no' => $purchase->purchase_no,
                    'supplier' => $purchase->supplier ? $purchase->supplier->only('name') : null,
                    'tanker_loads' => $purchase->tankerLoads->each(function ($load) {
                            return [
                                'trip_no' => $load->trip_no,
                                'remarks' => $load->remarks,
                                'purchase' => $load->purchase->purchase_no,
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

        $tankers = Tanker::orderBy('plate_no', 'asc')
            ->get()
            ->map
            ->only('id', 'plate_no');

        $drivers = Driver::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        $helpers = Helper::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');

        // Clients
        $clients = Client::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->orderBy('name', 'asc')->get();

        // Products
        $products = Product::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');


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
                'date' => $mindoroTransaction->date,
                'trip_no' => $mindoroTransaction->trip_no,
                'tanker_id' => $mindoroTransaction->tanker_id,
                'driver_id' => $mindoroTransaction->driver_id,
                'helper_id' => $mindoroTransaction->helper_id,
                'selected_purchases' => $selectedPurchases,
                'details' => $details,
            ],
            'tankers' => $tankers,
            'drivers' => $drivers,
            'helpers' => $helpers,
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
            'date' => $request->date,
            'trip_no' => $request->trip_no,
            'tanker_id' => $request->tanker_id,
            'driver_id' => $request->driver_id,
            'helper_id' => $request->helper_id,
        ]);

        $mindoroTransaction->purchases()->sync($request->purchases);

        // Details
        // $existingDetails = collect($request->details)->filter(function($value){
        //     return $value['id'] !== null;
        // });

        // foreach($existingDetails as $detail)
        // {
        //     $mindoroTransaction->mindoroTransactionDetails()->find($detail['id'])->update([
        //         // 'mindoroTransaction_id' => $detail['mindoroTransaction_id'],
        //         'date' => $detail['date'],
        //         'dr_no' => $detail['dr_no'],
        //         'client_id' => $detail['client_id'],
        //         'product_id' => $detail['product_id'],
        //         'quantity' => $detail['quantity'],
        //         'unit_price' => $detail['unit_price'],
        //     ]);
        // }

        foreach($request->details as $detail)
        {
            $transaction = $mindoroTransaction->mindoroTransactionDetails()->findOrNew($detail['id']);

            $transaction->date = $detail['date'];
            $transaction->dr_no = $detail['dr_no'];
            $transaction->client_id = $detail['client_id'];
            $transaction->product_id = $detail['product_id'];
            $transaction->quantity = $detail['quantity'];
            $transaction->unit_price = $detail['unit_price'];

            $transaction->save();
        }

        foreach($request->selectedPurchases as $purchase)
        {
           foreach ($purchase['tanker_loads'] as $load)
           {
                foreach ($load['tanker_load_details'] as $detail)
                {
                    $loadDetail = TankerLoadDetail::find($detail['id']);
                    $loadDetail->unit_price = $detail['unit_price'];
                    $loadDetail->save();
                }
           }
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
