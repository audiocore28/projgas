<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMonthlyMindoroTransactionRequest;
use App\Models\MonthlyMindoroTransaction;
use App\Models\MindoroTransaction;
use App\Models\MindoroTransactionDetail;
// use App\Models\Purchase;
// use App\Models\TankerLoad;
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
use Illuminate\Support\Str;
use Carbon\Carbon;
use Inertia\Inertia;
use PDF;

class MonthlyMindoroTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $monthlyMindoroTransactions = MonthlyMindoroTransaction::filter(Request::only('search', 'trashed'))
            ->orderBy('id', 'desc')
            ->paginate();

        return Inertia::render('MonthlyMindoroTransactions/Index', [
            'filters' => Request::all('search', 'trashed'),
            'monthly_mindoro_transactions' => $monthlyMindoroTransactions,
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

        return Inertia::render('MonthlyMindoroTransactions/Create', [
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
    public function store(StoreMonthlyMindoroTransactionRequest $request)
    {
        $monthlyMindoroTransactionId = MonthlyMindoroTransaction::create([
            'year' => Carbon::parse($request->date)->year,
            'month' => Carbon::parse($request->date)->format('F'),
        ])->id;

        foreach($request->transactions as $transaction)
        {
            $mindoroTransaction = MindoroTransaction::create([
                'monthly_mindoro_transaction_id' => $monthlyMindoroTransactionId,
                'trip_no' => $transaction['trip_no'],
                'tanker_id' => $transaction['tanker_id'],
                'driver_id' => $transaction['driver_id'],
                'helper_id' => $transaction['helper_id'],
            ]);
        }

        return redirect()->route('monthly-mindoro-transactions.index')->with('success', 'Monthly Mindoro Transaction was successfully added.');
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
    public function edit(MonthlyMindoroTransaction $monthlyMindoroTransaction)
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

        // Clients
        $clients = Client::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->orderBy('name', 'asc')->get()->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => Str::limit($client->name, 22, '...'),
            ];
        });

        // Products
        $products = Product::orderBy('name', 'asc')
            ->get()
            ->map
            ->only('id', 'name');


        // MindoroTransaction
        $transactions = $monthlyMindoroTransaction->mindoroTransactions
                ->map(function ($transaction) {
                    return [
                        'year' => $transaction->monthlyMindoroTransaction->year,
                        'month' => $transaction->monthlyMindoroTransaction->month,
                        'id' => $transaction->id,
                        'trip_no' => $transaction->trip_no,
                        'tanker' => $transaction->tanker ? $transaction->tanker->only('id', 'plate_no') : null,
                        'driver' => $transaction->driver ? $transaction->driver->only('id', 'name') : null,
                        'helper' => $transaction->helper ? $transaction->helper->only('id', 'name') : null,
                        'expense' => $transaction->expense,
                        // 'selected_purchases' => $selectedPurchases,
                        'details' => $transaction->mindoroTransactionDetails
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
                        }),
                        'tanker_loads' => $transaction->tankerLoads->each(function ($load) {
                            return [
                                'mindoro_transaction_id' => $load->mindoro_transaction_id,
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
                })
                ->toArray();


        if (request()->wantsJson()) {
            // include existing transactions next month to selected transaction month
            $nextTransactionId = $monthlyMindoroTransaction->where('id', '>', $monthlyMindoroTransaction->id)->min('id');

            if ($nextTransactionId) {
                $nextTransactions = $monthlyMindoroTransaction->find($nextTransactionId)->mindoroTransactions
                        ->map(function ($transaction) {
                            return [
                                'year' => $transaction->monthlyMindoroTransaction->year,
                                'month' => $transaction->monthlyMindoroTransaction->month,
                                'id' => $transaction->id,
                                'trip_no' => $transaction->trip_no,
                                'tanker' => $transaction->tanker ? $transaction->tanker->only('id', 'plate_no') : null,
                                'driver' => $transaction->driver ? $transaction->driver->only('id', 'name') : null,
                                'helper' => $transaction->helper ? $transaction->helper->only('id', 'name') : null,
                                'driver_salary' => $transaction->driver_salary,
                                'helper_salary' => $transaction->helper_salary,
                                // 'selected_purchases' => $selectedPurchases,
                            ];
                        })
                        ->toArray();

                array_push($transactions, ...$nextTransactions);
            }


            return [
               // 'loadDetails' => $lD,
               // 'MindoroDetails' => $bD,
               'mindoroTransactions' => $transactions,
            ];
        }

        return Inertia::render('MonthlyMindoroTransactions/Edit', [
            'monthly_mindoro_transaction' => [
                'id' => $monthlyMindoroTransaction->id,
                'year' => $monthlyMindoroTransaction->year,
                'month' => $monthlyMindoroTransaction->month,
                'transactions' => $transactions,
            ],

            'tankers' => $tankers,
            'drivers' => $drivers,
            'helpers' => $helpers,
            // 'purchases' => $purchases,
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
    public function update(StoreMonthlyMindoroTransactionRequest $request, MonthlyMindoroTransaction $monthlyMindoroTransaction)
    {
        // Mindoro Transaction
        $monthlyMindoroTransaction->update([
            'year' => Carbon::parse($request->date)->year,
            'month' => Carbon::parse($request->date)->format('F'),
        ]);

        foreach($request->transactions as $transaction)
        {
            $mindoroTransaction = $monthlyMindoroTransaction->mindoroTransactions()->findOrNew($transaction['id']);

            $mindoroTransaction->trip_no = $transaction['trip_no'];
            $mindoroTransaction->tanker_id = $transaction['tanker']['id'];
            $mindoroTransaction->driver_id = $transaction['driver']['id'];
            $mindoroTransaction->helper_id = $transaction['helper']['id'];
            $mindoroTransaction->expense = $transaction['expense'];

            $mindoroTransaction->save();

            foreach($transaction['details'] as $detail)
            {
                $transactionDetail = $mindoroTransaction->mindoroTransactionDetails()->findOrNew($detail['id']);

                $transactionDetail->date = $detail['date'];
                $transactionDetail->dr_no = $detail['dr_no'];
                $transactionDetail->client_id = $detail['client_id'];
                $transactionDetail->product_id = $detail['product_id'];
                $transactionDetail->quantity = $detail['quantity'];
                $transactionDetail->unit_price = $detail['unit_price'];

                $transactionDetail->save();
            }

            foreach ($transaction['tanker_loads'] as $load)
            {
                if ($load['id'] !== null) {
                    foreach ($load['tanker_load_details'] as $detail)
                    {
                        $loadDetail = TankerLoadDetail::findOrNew($detail['id']);
                        $loadDetail->unit_price = $detail['unit_price'];
                        $loadDetail->save();
                    }
                }
            }
        }

        $this->deleteMindoroTransaction($request->removed_transactions);
        $this->deleteMindoroTransactionDetail($request->removed_transaction_details);

        return Redirect::back()->with('success', 'Monthly Mindoro Transaction updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MonthlyMindoroTransaction $monthlyMindoroTransaction)
    {
        $monthlyMindoroTransaction->delete();

        return redirect()->route('monthly-mindoro-transactions.index')->with('success', 'Monthly Mindoro Transaction deleted.');
    }

    public function deleteMindoroTransaction($transactionIds)
    {
        MindoroTransaction::whereIn('id', $transactionIds)->delete();
    }

    public function deleteMindoroTransactionDetail($transactionDetailIds)
    {
        MindoroTransactionDetail::whereIn('id', $transactionDetailIds)->delete();
    }

    // DOMPDF - print
    public function print(MonthlyMindoroTransaction $monthlyMindoroTransaction)
    {
        // MindoroTransaction
        $transactions = $monthlyMindoroTransaction->mindoroTransactions
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'trip_no' => $transaction->trip_no,
                        'tanker' => $transaction->tanker ? $transaction->tanker->only('id', 'plate_no') : null,
                        'driver' => $transaction->driver ? $transaction->driver->only('id', 'name') : null,
                        'helper' => $transaction->helper ? $transaction->helper->only('id', 'name') : null,
                        'expense' => $transaction->expense,
                        // 'selected_purchases' => $selectedPurchases,
                        'details' => $transaction->mindoroTransactionDetails
                                ->map(function ($detail) {
                                    return [
                                       'id' => $detail->id,
                                       'date' => $detail->date,
                                       'dr_no' => $detail->dr_no,
                                       'quantity' => $detail->quantity,
                                       'unit_price' => $detail->unit_price,
                                       'mindoro_transaction_id' => $detail->mindoro_transaction_id,
                                       'product' => $detail->product ? $detail->product->only('id', 'name') : null,
                                       'client' => $detail->client ? $detail->client->only('id', 'name') : null,
                                       'selected_client' => $detail->client_id,
                                    ];
                        }),
                        'tanker_loads' => $transaction->tankerLoads
                                ->map(function ($load) {
                                    return [
                                        'mindoro_transaction_id' => $load->mindoro_transaction_id,
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
                })
                ->toArray();

        $queryTrips = $monthlyMindoroTransaction->mindoroTransactions
                    ->map(function ($transaction) {
                        return [
                            'id' => $transaction->id,
                            'trip_no' => $transaction->trip_no,
                            'tanker' => $transaction->tanker ? $transaction->tanker->only('id', 'plate_no') : null,
                            'driver' => $transaction->driver ? $transaction->driver->only('id', 'name') : null,
                            'helper' => $transaction->helper ? $transaction->helper->only('id', 'name') : null,
                        ];
                    });

        $driverTrips = $queryTrips->groupBy('driver.name');
        $helperTrips = $queryTrips->groupBy('helper.name');

        $pdf = PDF::loadView('print-mindoro-transactions', compact('monthlyMindoroTransaction', 'transactions', 'driverTrips', 'helperTrips'));
        $pdf->setPaper(array(0, 0, 612.00, 792.0));

        $fileName = "Mindoro - ".$monthlyMindoroTransaction->month." ".$monthlyMindoroTransaction->year.".pdf";
        return $pdf->stream($fileName);

    }

}
