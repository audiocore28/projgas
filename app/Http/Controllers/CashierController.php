<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCashierRequest;
use App\Models\Cashier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class CashierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Cashiers/Index', [
            'filters' => Request::all('search', 'trashed'),
            'cashiers' => Cashier::filter(Request::only('search', 'trashed'))
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
        return Inertia::render('Cashiers/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCashierRequest $request)
    {
        $cashier = Cashier::create($request->all());

        return redirect()->route('cashiers.index')->with('success', 'Cashier was successfully added.');
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
    public function edit(Cashier $cashier)
    {
        return Inertia::render('Cashiers/Edit', [
            'cashier' => [
                'id' => $cashier->id,
                'name' => $cashier->name,
                'nickname' => $cashier->nickname,
                'address' => $cashier->address,
                'contact_no' => $cashier->contact_no,
                'deleted_at' => $cashier->deleted_at,
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
    public function update(StoreCashierRequest $request, Cashier $cashier)
    {
        $cashier->update($request->all());

        return Redirect::back()->with('success', 'Cashier updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cashier $cashier)
    {
        $cashier->delete();

        return Redirect::back()->with('success', 'Cashier deleted.');
    }


    public function restore(Cashier $cashier)
    {
        $cashier->restore();

        return Redirect::back()->with('success', 'Cashier restored.');
    }
}
