<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeStaffRequest;
use App\Models\OfficeStaff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class OfficeStaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('OfficeStaffs/Index', [
            'filters' => Request::all('search', 'trashed'),
            'office_staffs' => OfficeStaff::filter(Request::only('search', 'trashed'))
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
        return Inertia::render('OfficeStaffs/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOfficeStaffRequest $request)
    {
        $officeStaff = OfficeStaff::create($request->all());

        return redirect()->route('office-staffs.index')->with('success', 'Office Staff was successfully added.');
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
    public function edit(OfficeStaff $officeStaff)
    {
        return Inertia::render('OfficeStaffs/Edit', [
            'office_staff' => [
                'id' => $officeStaff->id,
                'name' => $officeStaff->name,
                'nickname' => $officeStaff->nickname,
                'address' => $officeStaff->address,
                'contact_no' => $officeStaff->contact_no,
                'deleted_at' => $officeStaff->deleted_at,
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
    public function update(StoreOfficeStaffRequest $request, OfficeStaff $officeStaff)
    {
        $officeStaff->update($request->all());

        return Redirect::back()->with('success', 'Office Staff updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OfficeStaff $officeStaff)
    {
        $officeStaff->delete();

        return Redirect::back()->with('success', 'Office Staff deleted.');
    }


    public function restore(OfficeStaff $officeStaff)
    {
        $officeStaff->restore();

        return Redirect::back()->with('success', 'Office Staff restored.');
    }
}
