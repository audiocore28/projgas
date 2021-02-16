<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDriverRequest;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Drivers/Index', [
            'filters' => Request::all('search', 'trashed'),
            'drivers' => Driver::filter(Request::only('search', 'trashed'))
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
        return Inertia::render('Drivers/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDriverRequest $request)
    {
        $driver = Driver::create($request->all());

        return redirect()->route('drivers.index')->with('success', 'Driver was successfully added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function show(Driver $driver)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function edit(Driver $driver)
    {
        // $trips = $driver->tankerLoads()
        //     ->selectRaw('year(date) year, monthname(date) month, count(*) loaded')
        //     ->groupBy('year', 'month')
        //     ->orderByRaw('min(date)')
        //     ->get()
        //     ->toArray();

        $lD = $driver->tankerLoads()
            ->latest()
            ->paginate()
            ->transform(function ($load) {
                return [
                    'id' => $load->id,
                    'date' => $load->date,
                    'trip_no' => $load->trip_no,
                    'purchase' => $load->purchase ? $load->purchase->only('purchase_no') : null,
                    'tanker' => $load->tanker ? $load->tanker->only('plate_no') : null,
                    'driver' => $load->driver ? $load->driver->only('name') : null,
                    'helper' => $load->helper ? $load->helper->only('name') : null,
                    'loads' => $load->tankerLoadDetails->each(function ($detail) {
                            return ['p' => $detail->product->name];
                        })
                ];
            });

        $hD = $driver->hauls()
            ->latest()
            ->paginate()
            ->transform(function ($haul) {
                return [
                    'id' => $haul->id,
                    'trip_no' => $haul->trip_no,
                    'purchase' => $haul->purchase ? $haul->purchase->only('purchase_no') : null,
                    'tanker' => $haul->tanker ? $haul->tanker->only('plate_no') : null,
                    'driver' => $haul->driver ? $haul->driver->only('name') : null,
                    'helper' => $haul->helper ? $haul->helper->only('name') : null,
                    'hauls' => $haul->haulDetails->each(function ($detail) {
                            return ['p' => $detail->product->name, 'c' => $detail->client->name];
                        })
                ];
            });

        $dD = $driver->deliveries()
            ->latest()
            ->paginate()
            ->transform(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'trip_no' => $delivery->trip_no,
                    'purchase' => $delivery->purchase ? $delivery->purchase->only('purchase_no') : null,
                    'tanker' => $delivery->tanker ? $delivery->tanker->only('plate_no') : null,
                    'driver' => $delivery->driver ? $delivery->driver->only('name') : null,
                    'helper' => $delivery->helper ? $delivery->helper->only('name') : null,
                    'deliveries' => $delivery->deliveryDetails->each(function ($detail) {
                            return ['p' => $detail->product->name, 'c' => $detail->client->name];
                        })
                ];
            });

       if (request()->wantsJson()) {
         return [
           'loadDetails' => $lD,
           'deliveryDetails' => $dD,
           'haulDetails' => $hD,
         ];
       }

        return Inertia::render('Drivers/Edit', [
           'driver' => [
                'id' => $driver->id,
                'name' => $driver->name,
                'nickname' => $driver->nickname,
                'address' => $driver->address,
                'license_no' => $driver->license_no,
                'contact_no' => $driver->contact_no,
                'deleted_at' => $driver->deleted_at,
           ],
           'loadDetails' => $lD,
           'deliveryDetails' => $dD,
           'haulDetails' => $hD,
           // 'trips' => $trips,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function update(StoreDriverRequest $request, Driver $driver)
    {
        $driver->update($request->all());

        return Redirect::back()->with('success', 'Driver updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driver $driver)
    {
        if ($driver->deliveries()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, delivery has driver records']);
        }

        if ($driver->hauls()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, hauling has driver records']);
        }

        if ($driver->tankerLoads()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, load has driver records']);
        }

        $driver->delete();

        return Redirect::back()->with('success', 'Driver deleted.');
    }


    public function restore(Driver $driver)
    {
        $driver->restore();

        return Redirect::back()->with('success', 'Driver restored.');
    }
}
