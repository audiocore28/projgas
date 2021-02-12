<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Http\Requests\StoreStationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\Pump;
use App\Models\Product;
use Inertia\Inertia;

class StationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Stations/Index', [
            'filters' => Request::all('search', 'trashed'),
            'stations' => Station::filter(Request::only('search', 'trashed'))
                ->orderBy('id', 'desc')
                ->paginate()
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

        return Inertia::render('Stations/Create', [
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStationRequest $request)
    {
        $stationId = Station::create([
            'name' => $request->name,
            'address' => $request->address,
            'contact_no' => $request->contact_no,
        ])->id;

        foreach($request->pumps as $pump)
        {
            $pumpDetail = Pump::create([
                'station_id' => $stationId,
                'pump' => $pump['pump'],
                'product_id' => $pump['product_id'],
                'nozzle' => $pump['nozzle'],
            ]);
        }

        return redirect()->route('stations.index')->with('success', 'Station was successfully added.');
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
    public function edit(Station $station)
    {
        $products = Product::orderBy('name', 'asc')->get();

        return Inertia::render('Stations/Edit', [
            'station' => [
                'id' => $station->id,
                'name' => $station->name,
                'address' => $station->address,
                'contact_no' => $station->contact_no,
                'pumps' => $station->pumps,
            ],
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
    public function update(StoreStationRequest $request, Station $station)
    {
        // Station
        $station->update([
            'name' => $request->name,
            'address' => $request->address,
            'contact_no' => $request->contact_no,
        ]);

        // Pumps
        $existingPumps = collect($request->pumps)->filter(function($value){
            return $value['id'] !== null;
        });

        foreach($existingPumps as $pump)
        {
            $station->pumps()->find($pump['id'])->update([
                // 'station_id' => $pump['station_id'],
                'pump' => $pump['pump'],
                'nozzle' => $pump['nozzle'],
                'product_id' => $pump['product_id'],
            ]);
        }

        return Redirect::back()->with('success', 'Station updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Station $station)
    {
        $station->delete();

        return redirect()->route('stations.index')->with('success', 'Station deleted.');
    }

}
