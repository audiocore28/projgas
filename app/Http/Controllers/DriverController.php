<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Driver::class, 'driver');
    }

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
        $batangasQuery = $driver->load([
            'batangasTransactions' => function ($q) {
                $q->orderByRaw('LENGTH(trip_no)', 'ASC')->orderBy('trip_no', 'ASC');
            },
            'batangasTransactions.monthlyBatangasTransaction',
            'batangasTransactions.tanker:id,plate_no',
            'batangasTransactions.driver:id,name',
            'batangasTransactions.helper:id,name',
            'batangasTransactions.purchases:id,purchase_no',
            'batangasTransactions.batangasTransactionDetails.product:id,name',
            'batangasTransactions.batangasTransactionDetails.client:id,name',
        ]);

        $mindoroQuery = $driver->load([
            'mindoroTransactions' => function ($q) {
                $q->orderByRaw('LENGTH(trip_no)', 'ASC')->orderBy('trip_no', 'ASC');
            },
            'mindoroTransactions.monthlyMindoroTransaction',
            'mindoroTransactions.tanker:id,plate_no',
            'mindoroTransactions.driver:id,name',
            'mindoroTransactions.helper:id,name',
            'mindoroTransactions.purchases:id,purchase_no',
            'mindoroTransactions.mindoroTransactionDetails.product:id,name',
            'mindoroTransactions.mindoroTransactionDetails.client:id,name',
        ]);

        $batangasTrips = collect($batangasQuery->batangasTransactions)
                    ->groupBy([
                        'monthlyBatangasTransaction.year',
                        'monthlyBatangasTransaction.month'
                    ]);

        $mindoroTrips = collect($mindoroQuery->mindoroTransactions)
                    ->groupBy([
                        'monthlyMindoroTransaction.year',
                        'monthlyMindoroTransaction.month'
                    ]);

       // if (request()->wantsJson()) {
       //   return [
       //     'batangasDetails' => $bD,
       //     'mindoroDetails' => $mD,
       //   ];
       // }

        return Inertia::render('Drivers/Show', [
           'driver' => [
                'id' => $driver->id,
                'name' => $driver->name,
                'nickname' => $driver->nickname,
                'address' => $driver->address,
                'license_no' => $driver->license_no,
                'contact_no' => $driver->contact_no,
                'deleted_at' => $driver->deleted_at,
           ],
           'mindoroTrips' => $mindoroTrips,
           'batangasTrips' => $batangasTrips,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function edit(Driver $driver)
    {
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
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDriverRequest $request, Driver $driver)
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
