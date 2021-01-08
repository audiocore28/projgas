<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDriverRequest;
use App\Models\Driver;
// use Illuminate\Http\Request;
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
            'filters' => Request::all('search'),
            'drivers' => Driver::filter(Request::only('search'))
                ->orderBy('id', 'desc')
                ->paginate(5)
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
        return Inertia::render('Drivers/Edit', [
           'driver' => [
                'id' => $driver->id,
                'name' => $driver->name,
                'nickname' => $driver->nickname,
                'address' => $driver->address,
                'license_no' => $driver->license_no,
                'dob' => $driver->dob,
                'gender' => $driver->gender,
                'date_hired' => $driver->date_hired,
                'status' => $driver->status,
                'contact_no' => $driver->contact_no,
           ],
           'tankers' => $driver->tankers,
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

        return redirect()->route('drivers.index')->with('success', 'Driver updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driver $driver)
    {
        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver deleted.');
    }


    public function restore(Driver $driver)
    {
        $driver->restore();

        return redirect()->route('drivers.index')->with('success', 'Driver restored.');
    }
}
