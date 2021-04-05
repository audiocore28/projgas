<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyValeRequest;
use App\Models\CompanyVale;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class CompanyValeController extends Controller
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
    public function store(StoreCompanyValeRequest $request)
    {
        foreach($request->all() as $vale)
        {
            $valeDetail = CompanyVale::create([
                'station_transaction_id' => $vale['station_transaction_id'],
                'pump_no' => $vale['pump_no'],
                'voucher_no' => $vale['voucher_no'],
                'company_id' => $vale['company_id'],
                'product_id' => $vale['product_id'],
                'quantity' => $vale['quantity'],
                'remarks' => $vale['remarks'],
            ]);
        }

        return redirect()->route('station-transactions.index')->with('success', 'Transaction was successfully updated.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompanyVale  $companyVale
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyVale $companyVale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompanyVale  $companyVale
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyVale $companyVale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompanyVale  $companyVale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyVale $companyVale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyVale  $companyVale
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyVale $companyVale)
    {
        $companyVale->delete();

        return Redirect::back()->with('success', 'Company Vale row deleted.');
    }
}
