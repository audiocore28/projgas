<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStationTransactionRequest;
use App\Models\StationTransaction;
use App\Models\Product;
use App\Models\Client;
use App\Models\Station;
use App\Models\Cashier;
use App\Models\PumpAttendant;
use App\Models\OfficeStaff;
use App\Models\PumpReading;
use App\Models\Sale;
use App\Models\CompanyVale;
use App\Models\Calibration;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class StationTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = StationTransaction::filter(request()->only('search', 'trashed'))
                ->orderBy('id', 'desc')
                ->paginate()
                ->transform(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'date' => $transaction->date,
                        'shift' => $transaction->shift,
                        'station' => $transaction->station ? $transaction->station->only('name') : null,
                        'cashier' => $transaction->cashier ? $transaction->cashier->only('name') : null,
                        'pump_attendant' => $transaction->pumpAttendant ? $transaction->pumpAttendant->only('name') : null,
                        'office_staff' => $transaction->officeStaff ? $transaction->officeStaff->only('name') : null,
                    ];
                });

        return Inertia::render('StationTransactions/Index', [
            'filters' => request()->all('search', 'trashed'),
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $products = Product::orderBy('name', 'asc')->get();
        $clients = Client::orderBy('name', 'asc')->get();
        $stations = Station::orderBy('name', 'asc')->get();
        $cashiers = Cashier::orderBy('name', 'asc')->get();
        $pumpAttendants = PumpAttendant::orderBy('name', 'asc')->get();
        $officeStaffs = OfficeStaff::orderBy('name', 'asc')->get();

        return Inertia::render('StationTransactions/Create', [
            'products' => $products,
            'clients' => $clients,
            'stations' => $stations,
            'cashiers' => $cashiers,
            'pump_attendants' => $pumpAttendants,
            'office_staffs' => $officeStaffs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStationTransactionRequest $request)
    {
        $transactionId = StationTransaction::create([
            'date' => $request->date,
            'shift' => $request->shift,
            'station_id' => $request->station_id,
            'cashier_id' => $request->cashier_id,
            'pump_attendant_id' => $request->pump_attendant_id,
            'office_staff_id' => $request->office_staff_id,
        ])->id;


        foreach($request->pump_readings as $reading)
        {
            $readingDetail = PumpReading::create([
                'station_transaction_id' => $transactionId,
                'pump' => $reading['pump'],
                'opening' => $reading['opening'],
                'closing' => $reading['closing'],
                'unit_price' => $reading['unit_price'],
            ]);
        }

        foreach($request->sales as $sale)
        {
            $saleDetail = Sale::create([
                'station_transaction_id' => $transactionId,
                'pump_no' => $sale['pump_no'],
                'dr_no' => $sale['dr_no'],
                'client_id' => $sale['client_id'],
                'product_id' => $sale['product_id'],
                'quantity' => $sale['quantity'],
                'rs_no' => $sale['rs_no'],
            ]);
        }

        foreach($request->company_vales as $vale)
        {
            $valeDetail = CompanyVale::create([
                'station_transaction_id' => $transactionId,
                'pump_no' => $vale['pump_no'],
                'voucher_no' => $vale['voucher_no'],
                'client_id' => $vale['client_id'],
                'product_id' => $vale['product_id'],
                'quantity' => $vale['quantity'],
            ]);
        }

        foreach($request->calibrations as $calibration)
        {
            $calibrationDetail = Calibration::create([
                'station_transaction_id' => $transactionId,
                'pump' => $calibration['pump'],
                'quantity' => $calibration['quantity'],
                'pump_no' => $calibration['pump_no'],
                'voucher_no' => $calibration['voucher_no'],
            ]);
        }

        foreach($request->discounts as $discount)
        {
            $discountDetail = Discount::create([
                'station_transaction_id' => $transactionId,
                'voucher_no' => $discount['voucher_no'],
                'cash' => $discount['cash'],
                'client_id' => $discount['client_id'],
                'quantity' => $discount['quantity'],
                'disc_amount' => $discount['disc_amount'],
            ]);
        }

        return redirect()->route('station-transactions.index')->with('success', 'Transaction was successfully added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StationTransaction  $stationTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(StationTransaction $stationTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StationTransaction  $stationTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(StationTransaction $stationTransaction)
    {
        $products = Product::orderBy('name', 'asc')->get();
        $clients = Client::orderBy('name', 'asc')->get();
        $stations = Station::orderBy('name', 'asc')->get();
        $cashiers = Cashier::orderBy('name', 'asc')->get();
        $pumpAttendants = PumpAttendant::orderBy('name', 'asc')->get();
        $officeStaffs = OfficeStaff::orderBy('name', 'asc')->get();

        return Inertia::render('StationTransactions/Edit', [
            'transaction' => [
                'id' => $stationTransaction->id,
                'date' => $stationTransaction->date,
                'shift' => $stationTransaction->shift,
                'station_id' => $stationTransaction->station_id,
                'cashier_id' => $stationTransaction->cashier_id,
                'pump_attendant_id' => $stationTransaction->pump_attendant_id,
                'office_staff_id' => $stationTransaction->office_staff_id,
                'pump_readings' => $stationTransaction->pumpReadings,
                'sales' => $stationTransaction->sales,
                'company_vales' => $stationTransaction->companyVales,
                'calibrations' => $stationTransaction->calibrations,
                'discounts' => $stationTransaction->discounts,
            ],
            'products' => $products,
            'clients' => $clients,
            'stations' => $stations,
            'cashiers' => $cashiers,
            'pump_attendants' => $pumpAttendants,
            'office_staffs' => $officeStaffs,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StationTransaction  $stationTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(StoreStationTransactionRequest $request, StationTransaction $stationTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StationTransaction  $stationTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(StationTransaction $stationTransaction)
    {
        //
    }
}
