<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalibrationRequest;
use App\Models\Calibration;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class CalibrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCalibrationRequest $request)
    {
        foreach($request->all() as $calibration)
        {
            $calibrationDetail = Calibration::create([
                'station_transaction_id' => $calibration['station_transaction_id'],
                'pump' => $calibration['pump'],
                'quantity' => $calibration['quantity'],
                'pump_no' => $calibration['pump_no'],
                'voucher_no' => $calibration['voucher_no'],
            ]);
        }

        return redirect()->route('station-transactions.index')->with('success', 'Transaction was successfully updated.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Calibration  $calibration
     * @return \Illuminate\Http\Response
     */
    public function show(Calibration $calibration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Calibration  $calibration
     * @return \Illuminate\Http\Response
     */
    public function edit(Calibration $calibration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Calibration  $calibration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Calibration $calibration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Calibration  $calibration
     * @return \Illuminate\Http\Response
     */
    public function destroy(Calibration $calibration)
    {
        $calibration->delete();

        return Redirect::back()->with('success', 'Calibration row deleted.');
    }
}
