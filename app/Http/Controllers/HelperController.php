<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHelperRequest;
use App\Models\Helper;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class HelperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Helpers/Index', [
            'filters' => Request::all('search'),
            'helpers' => Helper::filter(Request::only('search'))
                // ->orderByName()
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
        return Inertia::render('Helpers/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHelperRequest $request)
    {
        $helper = Helper::create($request->all());

        return redirect()->route('helpers.index')->with('success', 'Helper was successfully added.');
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
    public function edit(Helper $helper)
    {
        return Inertia::render('Helpers/Edit', [
            'helper' => [
                'id' => $helper->id,
                'name' => $helper->name,
                'nickname' => $helper->nickname,
                'address' => $helper->address,
                'dob' => $helper->dob,
                'date_hired' => $helper->date_hired,
                'status' => $helper->status,
                'contact_no' => $helper->contact_no,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreHelperRequest $request, Helper $helper)
    {
        $helper->update($request->all());

        return redirect()->route('helpers.index')->with('success', 'Helper updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Helper $helper)
    {
        $helper->delete();

        return redirect()->route('helpers.index')->with('success', 'Helper deleted.');
    }


    public function restore(Helper $helper)
    {
        $helper->restore();

        return redirect()->route('helpers.index')->with('success', 'Helper restored.');
    }
}
