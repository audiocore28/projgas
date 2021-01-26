<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseDetailRequest;
use App\Models\PurchaseDetail;
use App\Models\Purchase;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class PurchaseDetailController extends Controller
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
    public function store(StorePurchaseDetailRequest $request)
    {

       // $newDetails = collect($request->details)->filter(function($value){
       //      return $value['id'] === null;
       // });
        // dd($request->all());

        foreach($request->all() as $detail)
        {
            $purchaseDetail = PurchaseDetail::create([
                'purchase_id' => $detail['purchase_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
                'remarks' => $detail['remarks'],
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase was successfully added.');
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
    public function edit(PurchaseDetail $purchaseDetail)
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
    public function update(StorePurchaseDetailRequest $request, PurchaseDetail $purchaseDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseDetail $purchaseDetail, Purchase $purchase)
    {
        $purchaseDetail->delete();

        return Redirect::back()->with('success', 'Row deleted.');
    }

}
