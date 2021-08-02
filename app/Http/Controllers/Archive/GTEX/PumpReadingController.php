<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePumpReadingRequest;
use App\Models\PumpReading;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class PumpReadingController extends Controller
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
    public function store(StorePumpReadingRequest $request)
    {
        foreach($request->all() as $reading)
        {
            $readingDetail = PumpReading::create([
                'station_transaction_id' => $reading['station_transaction_id'],
                'pump' => $reading['pump'],
                'opening' => $reading['opening'],
                'closing' => $reading['closing'],
                'unit_price' => $reading['unit_price'],
            ]);
        }

        return redirect()->route('station-transactions.index')->with('success', 'Transaction was successfully updated.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PumpReading  $pumpReading
     * @return \Illuminate\Http\Response
     */
    public function show(PumpReading $pumpReading)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PumpReading  $pumpReading
     * @return \Illuminate\Http\Response
     */
    public function edit(PumpReading $pumpReading)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PumpReading  $pumpReading
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PumpReading $pumpReading)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PumpReading  $pumpReading
     * @return \Illuminate\Http\Response
     */
    public function destroy(PumpReading $pumpReading)
    {
        $pumpReading->delete();

        return Redirect::back()->with('success', 'Pump Reading row deleted.');
    }
}
