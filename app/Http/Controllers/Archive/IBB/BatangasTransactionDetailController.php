<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBatangasTransactionDetailRequest;
use App\Models\BatangasTransactionDetail;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class BatangasTransactionDetailController extends Controller
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
    public function store(StoreBatangasTransactionDetailRequest $request)
    {
        foreach($request->all() as $detail)
        {
            $batangasTransactionDetail = BatangasTransactionDetail::create([
                'batangas_transaction_id' => $detail['batangas_transaction_id'],
                'date' => $detail['date'],
                'dr_no' => $detail['dr_no'],
                'client_id' => $detail['client_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
            ]);
        }

        return redirect()->route('batangas-transactions.index')->with('success', 'Batangas Transaction was successfully updated.');

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
    public function edit(BatangasTransactionDetail $batangasTransactionDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreBatangasTransactionDetailRequest $request, BatangasTransactionDetail $batangasTransactionDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(BatangasTransactionDetail $batangasTransactionDetail)
    {
        $batangasTransactionDetail->delete();

        return Redirect::back()->with('success', 'Row deleted.');
    }

}
