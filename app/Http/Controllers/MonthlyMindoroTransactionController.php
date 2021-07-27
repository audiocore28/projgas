<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMonthlyMindoroTransactionRequest;
use App\Http\Requests\UpdateMonthlyMindoroTransactionRequest;
use App\Models\MonthlyMindoroTransaction;
use App\Models\MindoroTransaction;
use App\Models\MindoroTransactionDetail;
// use App\Models\Purchase;
use App\Models\ToMindoroLoadDetail;
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

class MonthlyMindoroTransactionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(MonthlyMindoroTransaction::class, 'monthly_mindoro_transaction');
    }
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

        $monthlyMindoroTransactionId = MonthlyMindoroTransaction::create([
            'year' => $year,
            'month' => $month,
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
        $query = $monthlyMindoroTransaction->load([
            'mindoroTransactions.monthlyMindoroTransaction',
            'mindoroTransactions.tanker:id,plate_no',
            'mindoroTransactions.driver:id,name',
            'mindoroTransactions.helper:id,name',
            'mindoroTransactions.mindoroTransactionDetails.product:id,name',
            'mindoroTransactions.mindoroTransactionDetails.client:id,name',
            'mindoroTransactions.toMindoroLoads.purchase:id,purchase_no',
            'mindoroTransactions.toMindoroLoads.toMindoroLoadDetails.product:id,name',
        ]);

        $transactions = collect($query->mindoroTransactions)->toArray();

        if (request()->wantsJson()) {
            $transactionsArray = [...$transactions];

            // include existing transactions next month to selected transaction month
            $nextMonthlyTransactionId = $monthlyMindoroTransaction->where('id', '>', $monthlyMindoroTransaction->id)->min('id');
            $nextMonthlyTransaction = $monthlyMindoroTransaction->find($nextMonthlyTransactionId);

            if ($nextMonthlyTransactionId) {
                $nextTransactionsQuery = $nextMonthlyTransaction->load([
                    'mindoroTransactions.monthlyMindoroTransaction',
                    'mindoroTransactions.tanker:id,plate_no',
                    'mindoroTransactions.driver:id,name',
                    'mindoroTransactions.helper:id,name',
                ]);

                $nextTransactions = collect($nextTransactionsQuery->mindoroTransactions)->toArray();

                array_push($transactionsArray, ...$nextTransactions);
            }

            return [
               'mindoroTransactions' => $transactionsArray,
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
    public function update(UpdateMonthlyMindoroTransactionRequest $request, MonthlyMindoroTransaction $monthlyMindoroTransaction)
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

        // Mindoro Transaction
        $monthlyMindoroTransaction->update([
            'year' => $year,
            'month' => $month,
        ]);

        foreach($request->transactions as $transaction)
        {
            $mindoroTransaction = $monthlyMindoroTransaction->mindoroTransactions()->findOrNew($transaction['id']);

            $mindoroTransaction->trip_no = $transaction['trip_no'];
            $mindoroTransaction->tanker_id = $transaction['tanker']['id'];
            $mindoroTransaction->driver_id = $transaction['driver']['id'];
            $mindoroTransaction->helper_id = $transaction['helper']['id'];
            $mindoroTransaction->expense = $transaction['expense'];
            $mindoroTransaction->driver_salary = $transaction['driver_salary'];
            $mindoroTransaction->helper_salary = $transaction['helper_salary'];

            $mindoroTransaction->save();

            foreach($transaction['mindoro_transaction_details'] as $detail)
            {
                $transactionDetail = $mindoroTransaction->mindoroTransactionDetails()->findOrNew($detail['id']);

                $transactionDetail->date = $detail['date'];
                $transactionDetail->dr_no = $detail['dr_no'];
                $transactionDetail->client_id = $detail['client_id'];
                $transactionDetail->remarks = $detail['remarks'];
                $transactionDetail->product_id = $detail['product_id'];
                $transactionDetail->quantity = $detail['quantity'];
                $transactionDetail->unit_price = $detail['unit_price'];

                $transactionDetail->save();
            }

            foreach ($transaction['to_mindoro_loads'] as $load)
            {
                if ($load['id'] !== null) {
                    foreach ($load['to_mindoro_load_details'] as $detail)
                    {
                        $loadDetail = ToMindoroLoadDetail::findOrNew($detail['id']);
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
        $query = $monthlyMindoroTransaction->load([
            'mindoroTransactions.tanker:id,plate_no',
            'mindoroTransactions.driver:id,name',
            'mindoroTransactions.helper:id,name',
            'mindoroTransactions.mindoroTransactionDetails.product:id,name',
            'mindoroTransactions.mindoroTransactionDetails.client:id,name',
            'mindoroTransactions.toMindoroLoads.purchase:id,purchase_no',
            'mindoroTransactions.toMindoroLoads.toMindoroLoadDetails.product:id,name',
        ]);

        $monthlyTransactions = collect($query)->toArray();
        $driverTrips = collect($query->mindoroTransactions)->groupBy('driver.name');
        $helperTrips = collect($query->mindoroTransactions)->groupBy('helper.name');


        $pdf = PDF::loadView('print-mindoro-transactions', compact('monthlyTransactions', 'driverTrips', 'helperTrips'));
        $pdf->setPaper(array(0, 0, 612.00, 792.0));

        $fileName = "Mindoro - ".$monthlyMindoroTransaction->month." ".$monthlyMindoroTransaction->year.".pdf";
        return $pdf->stream($fileName);

    }

}
