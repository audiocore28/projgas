<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMindoroTransactionRequest;
use App\Models\MindoroTransaction;
use App\Models\MindoroTransactionDetail;
use App\Models\Purchase;
use App\Models\TankerLoad;
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
                    'trip_no' => $transaction->trip_no,
                    'purchase' => $transaction->purchase ? $transaction->purchase->only('purchase_no') : null,
                    'tanker' => $transaction->tanker ? $transaction->tanker->only('plate_no') : null,
                    'driver' => $transaction->driver ? $transaction->driver->only('name') : null,
                    'helper' => $transaction->helper ? $transaction->helper->only('name') : null,
                    'clients' => $transaction->mindoroTransactionDetails->each(function ($detail) {
                            return [ 'name' => $detail->client->name ];
                        }),
                    // 'tanker_load_id' => $transaction->tanker_load_id,
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

        $tankers = Tanker::orderBy('plate_no', 'asc')->get();
        $drivers = Driver::orderBy('name', 'asc')->get();
        $helpers = Helper::orderBy('name', 'asc')->get();

        $clients = Client::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->get();

        $products = Product::orderBy('name', 'asc')->get();

        return Inertia::render('MindoroTransactions/Create', [
            'clients' => $clients,
            'purchases' => $purchases,
            'products' => $products,
            'tankers' => $tankers,
            'drivers' => $drivers,
            'helpers' => $helpers,
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
            'purchase_id' => $request->purchase_id,
            'tanker_id' => $request->tanker_id,
            'driver_id' => $request->driver_id,
            'helper_id' => $request->helper_id,
        ])->id;


        foreach($request->details as $detail)
        {
            $mindoroTransactionDetail = MindoroTransactionDetail::create([
                'mindoro_transaction_id' => $mindoroTransactionId,
                'date' => $detail['date'],
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
        $purchases = Purchase::when(request('term'), function($query, $term) {
            $query->where('purchase_no', 'like', "%$term%");
        })->get();

        $tankers = Tanker::orderBy('plate_no', 'asc')->get();
        $drivers = Driver::orderBy('name', 'asc')->get();
        $helpers = Helper::orderBy('name', 'asc')->get();

        $clients = Client::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->get();

        $products = Product::orderBy('name', 'asc')->get();
        $details = $mindoroTransaction->mindoroTransactionDetails
                ->map(function ($detail) {
                    return [
                       'id' => $detail->id,
                       'date' => $detail->date,
                       'quantity' => $detail->quantity,
                       'unit_price' => $detail->unit_price,
                       'mindoro_transaction_id' => $detail->mindoro_transaction_id,
                       'product_id' => $detail->product_id,
                       'client_id' => $detail->client_id,
                       'selectedClient' => $detail->client_id,
                    ];
                })
                ->toArray();

        return Inertia::render('MindoroTransactions/Edit', [
            'mindoro_transaction' => [
                'id' => $mindoroTransaction->id,
                'trip_no' => $mindoroTransaction->trip_no,
                'tanker_id' => $mindoroTransaction->tanker_id,
                'driver_id' => $mindoroTransaction->driver_id,
                'helper_id' => $mindoroTransaction->helper_id,
                'purchase_id' => $mindoroTransaction->purchase_id,
                'details' => $details,
            ],
            'clients' => $clients,
            'purchases' => $purchases,
            'tankers' => $tankers,
            'drivers' => $drivers,
            'helpers' => $helpers,
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
        // mindoroTransaction
        $mindoroTransaction->update([
            'trip_no' => $request->trip_no,
            'tanker_id' => $request->tanker_id,
            'driver_id' => $request->driver_id,
            'helper_id' => $request->helper_id,
            'purchase_id' => $request->purchase_id,
        ]);

        // PurchaseDetail
        $existingDetails = collect($request->details)->filter(function($value){
            return $value['id'] !== null;
        });

        foreach($existingDetails as $detail)
        {
            $mindoroTransaction->mindoroTransactionDetails()->find($detail['id'])->update([
                // 'mindoroTransaction_id' => $detail['mindoroTransaction_id'],
                'date' => $detail['date'],
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
