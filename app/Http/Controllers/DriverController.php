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
        $trips = $driver->mindoroTransactions()
            ->selectRaw('year(date) year, monthname(date) month, count(*) loaded')
            ->groupBy('year', 'month')
            ->orderByRaw('min(date)')
            ->get()
            ->toArray();

        $bD = $driver->batangasTransactions()
            ->latest()
            ->paginate()
            ->transform(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->date,
                    'trip_no' => $transaction->trip_no,
                    'purchases' => $transaction->purchases->each(function ($purchase) {
                            return ['purchase_no' => $purchase->purchase_no, ];
                        }),
                    'tanker' => $transaction->tanker ? $transaction->tanker->only('plate_no') : null,
                    'driver' => $transaction->driver ? $transaction->driver->only('name') : null,
                    'helper' => $transaction->helper ? $transaction->helper->only('name') : null,
                    'details' => $transaction->batangasTransactionDetails->each(function ($detail) {
                            return ['p' => $detail->product->name, 'c' => $detail->client->name];
                        })
                ];
            });

        $mD = $driver->mindoroTransactions()
            ->latest()
            ->paginate()
            ->transform(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->date,
                    'trip_no' => $transaction->trip_no,
                    'purchases' => $transaction->purchases->each(function ($purchase) {
                            return ['purchase_no' => $purchase->purchase_no, ];
                        }),
                    'tanker' => $transaction->tanker ? $transaction->tanker->only('plate_no') : null,
                    'driver' => $transaction->driver ? $transaction->driver->only('name') : null,
                    'helper' => $transaction->helper ? $transaction->helper->only('name') : null,
                    'details' => $transaction->mindoroTransactionDetails->each(function ($detail) {
                            return ['p' => $detail->product->name, 'c' => $detail->client->name];
                        })
                ];
            });


       if (request()->wantsJson()) {
         return [
           // 'loadDetails' => $lD,
           'batangasDetails' => $bD,
           'mindoroDetails' => $mD,
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
           // 'loadDetails' => $lD,
           'batangasDetails' => $bD,
           'mindoroDetails' => $mD,
           'trips' => $trips,
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
        if ($driver->batangasTransactions()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, Batangas transactions has driver records']);
        }

        if ($driver->mindoroTransactions()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, Mindoro transactions has driver records']);
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
