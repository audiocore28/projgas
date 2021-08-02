<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePumpAttendantRequest;
use App\Models\PumpAttendant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class PumpAttendantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('PumpAttendants/Index', [
            'filters' => Request::all('search', 'trashed'),
            'pump_attendants' => PumpAttendant::filter(Request::only('search', 'trashed'))
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
        return Inertia::render('PumpAttendants/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePumpAttendantRequest $request)
    {
        $pumpAttendant = PumpAttendant::create($request->all());

        return redirect()->route('pump-attendants.index')->with('success', 'Pump Attendant was successfully added.');
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
    public function edit(PumpAttendant $pumpAttendant)
    {
        return Inertia::render('PumpAttendants/Edit', [
            'pump_attendant' => [
                'id' => $pumpAttendant->id,
                'name' => $pumpAttendant->name,
                'nickname' => $pumpAttendant->nickname,
                'address' => $pumpAttendant->address,
                'contact_no' => $pumpAttendant->contact_no,
                'deleted_at' => $pumpAttendant->deleted_at,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePumpAttendantRequest $request, PumpAttendant $pumpAttendant)
    {
        $pumpAttendant->update($request->all());

        return Redirect::back()->with('success', 'Pump Attendant updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PumpAttendant $pumpAttendant)
    {
        $pumpAttendant->delete();

        return Redirect::back()->with('success', 'Pump Attendant deleted.');
    }


    public function restore(PumpAttendant $pumpAttendant)
    {
        $pumpAttendant->restore();

        return Redirect::back()->with('success', 'Pump Attendant restored.');
    }
}
