<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMonthlyBatangasTransactionRequest;
use App\Http\Requests\UpdateMonthlyBatangasTransactionRequest;
use App\Models\MonthlyBatangasTransaction;
use App\Models\BatangasTransaction;
use App\Models\BatangasTransactionDetail;
// use App\Models\Purchase;
use App\Models\ToBatangasLoadDetail;
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
use Inertia\Inertia;
use PDF;

class MonthlyBatangasTransactionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(MonthlyBatangasTransaction::class, 'monthly_batangas_transaction');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $monthlyBatangasTransactions = MonthlyBatangasTransaction::filter(Request::only('search', 'trashed'))
            ->orderBy('id', 'desc')
            ->paginate();

        return Inertia::render('MonthlyBatangasTransactions/Index', [
            'filters' => Request::all('search', 'trashed'),
            'monthly_batangas_transactions' => $monthlyBatangasTransactions,
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

        return Inertia::render('MonthlyBatangasTransactions/Create', [
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
    public function store(StoreMonthlyBatangasTransactionRequest $request)
    {
        // check trip no. if has duplicates
        $tripNos = [];
        foreach ($request->transactions as $transaction) {
            array_push($tripNos, $transaction['trip_no']);
        }

        if(count($tripNos) !== count(array_unique($tripNos))) {
            return Redirect::back()->withErrors('Trip no. has duplicates');
        }

        // else

        $dateMonthArray = explode(', ', $request->date);
        $month = $dateMonthArray[0];
        $year = $dateMonthArray[1];

        $monthlyBatangasTransactionId = MonthlyBatangasTransaction::create([
            'year' => $year,
            'month' => $month,
        ])->id;

        foreach($request->transactions as $transaction)
        {
            $batangasTransaction = BatangasTransaction::create([
                'monthly_batangas_transaction_id' => $monthlyBatangasTransactionId,
                'trip_no' => $transaction['trip_no'],
                'tanker_id' => $transaction['tanker_id'],
                'driver_id' => $transaction['driver_id'],
                'helper_id' => $transaction['helper_id'],
            ]);
        }

        return redirect()->route('monthly-batangas-transactions.index')->with('success', 'Monthly Batangas Transaction was successfully added.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(MonthlyBatangasTransaction $monthlyBatangasTransaction)
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


        // BatangasTransaction
        $query = $monthlyBatangasTransaction->load([
            'batangasTransactions.monthlyBatangasTransaction',
            'batangasTransactions.tanker:id,plate_no',
            'batangasTransactions.driver:id,name',
            'batangasTransactions.batangasTransactionDetails.product:id,name',
            'batangasTransactions.batangasTransactionDetails.client:id,name',
            'batangasTransactions.toBatangasLoads.purchase:id,purchase_no',
            'batangasTransactions.toBatangasLoads.toBatangasLoadDetails.product:id,name',
        ]);

        $transactions = collect($query->batangasTransactions)->toArray();

        if (request()->wantsJson()) {
            $transactionsArray = [...$transactions];

            // include existing transactions next month to selected transaction month
            $nextMonthlyTransactionId = $monthlyBatangasTransaction->where('id', '>', $monthlyBatangasTransaction->id)->min('id');
            $nextMonthlyTransaction = $monthlyBatangasTransaction->find($nextMonthlyTransactionId);

            if ($nextMonthlyTransactionId) {
                $nextTransactionsQuery = $nextMonthlyTransaction->load([
                    'batangasTransactions.monthlyBatangasTransaction',
                    'batangasTransactions.tanker:id,plate_no',
                    'batangasTransactions.driver:id,name',
                    'batangasTransactions.helper:id,name',
                ]);

                $nextTransactions = collect($nextTransactionsQuery->batangasTransactions)->toArray();

                array_push($transactionsArray, ...$nextTransactions);
            }

            return [
               'batangasTransactions' => $transactionsArray,
            ];
        }


        return Inertia::render('MonthlyBatangasTransactions/Edit', [
            'monthly_batangas_transaction' => [
                'id' => $monthlyBatangasTransaction->id,
                'year' => $monthlyBatangasTransaction->year,
                'month' => $monthlyBatangasTransaction->month,
                'transactions' => $transactions,
            ],
            'tankers' => $tankers,
            'drivers' => $drivers,
            'helpers' => $helpers,
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
    public function update(UpdateMonthlyBatangasTransactionRequest $request, MonthlyBatangasTransaction $monthlyBatangasTransaction)
    {
        // check trip no. if has duplicates
        $tripNos = [];
        foreach ($request->transactions as $transaction) {
            array_push($tripNos, $transaction['trip_no']);
        }

        if(count($tripNos) !== count(array_unique($tripNos))) {
            return Redirect::back()->withErrors('Trip no. has duplicates');
        }

        // else

        $dateMonthArray = explode(', ', $request->date);
        $month = $dateMonthArray[0];
        $year = $dateMonthArray[1];

        // Batangas Transaction
        $monthlyBatangasTransaction->update([
            'year' => $year,
            'month' => $month,
        ]);

        foreach($request->transactions as $transaction)
        {
            $batangasTransaction = $monthlyBatangasTransaction->batangasTransactions()->findOrNew($transaction['id']);

            $batangasTransaction->trip_no = $transaction['trip_no'];
            $batangasTransaction->tanker_id = $transaction['tanker']['id'];
            $batangasTransaction->driver_id = $transaction['driver']['id'];
            $batangasTransaction->helper_id = $transaction['helper_id'];
            $batangasTransaction->driver_salary = $transaction['driver_salary'];
            $batangasTransaction->helper_salary = $transaction['helper_salary'];
            $batangasTransaction->commission = $transaction['commission'];

            $batangasTransaction->save();

            foreach($transaction['batangas_transaction_details'] as $detail)
            {
                $transactionDetail = $batangasTransaction->batangasTransactionDetails()->findOrNew($detail['id']);

                $transactionDetail->date = $detail['date'];
                $transactionDetail->client_id = $detail['client_id'];
                $transactionDetail->remarks = $detail['remarks'];
                $transactionDetail->product_id = $detail['product_id'];
                $transactionDetail->quantity = $detail['quantity'];
                $transactionDetail->unit_price = $detail['unit_price'];

                $transactionDetail->save();
            }
        }

        $this->deleteBatangasTransaction($request->removed_transactions);
        $this->deleteBatangasTransactionDetail($request->removed_transaction_details);

        return Redirect::back()->with('success', 'Monthly Batangas Transaction updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MonthlyBatangasTransaction $monthlyBatangasTransaction)
    {
        $monthlyBatangasTransaction->delete();

        return redirect()->route('monthly-batangas-transactions.index')->with('success', 'Monthly Batangas Transaction deleted.');
    }

    public function deleteBatangasTransaction($transactionIds)
    {
        BatangasTransaction::whereIn('id', $transactionIds)->delete();
    }

    public function deleteBatangasTransactionDetail($transactionDetailIds)
    {
        BatangasTransactionDetail::whereIn('id', $transactionDetailIds)->delete();
    }

    // DOMPDF - print
    public function print(MonthlyBatangasTransaction $monthlyBatangasTransaction)
    {
        $query = $monthlyBatangasTransaction->load([
            'batangasTransactions.tanker:id,plate_no',
            'batangasTransactions.driver:id,name',
            'batangasTransactions.helper:id,name',
            'batangasTransactions.batangasTransactionDetails.product:id,name',
            'batangasTransactions.batangasTransactionDetails.client:id,name',
            'batangasTransactions.toBatangasLoads.purchase:id,purchase_no',
            'batangasTransactions.toBatangasLoads.toBatangasLoadDetails.product:id,name',
        ]);

        $monthlyTransactions = collect($query)->toArray();
        $driverTrips = collect($query->batangasTransactions)->groupBy('driver.name');
        $helperTrips = collect($query->batangasTransactions)->groupBy('helper.name');


        $pdf = PDF::loadView('print-batangas-transactions', compact('monthlyTransactions', 'driverTrips', 'helperTrips'));
        $pdf->setPaper(array(0, 0, 612.00, 792.0));

        $fileName = "Batangas - ".$monthlyBatangasTransaction->month." ".$monthlyBatangasTransaction->year.".pdf";
        return $pdf->stream($fileName);

    }

}
