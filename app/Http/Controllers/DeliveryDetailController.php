<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryDetailRequest;
use App\Models\DeliveryDetail;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DeliveryDetailController extends Controller
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
    public function store(StoreDeliveryDetailRequest $request)
    {
        foreach($request->all() as $detail)
        {
            $deliveryDetail = DeliveryDetail::create([
                'delivery_id' => $detail['delivery_id'],
                'date' => $detail['date'],
                'dr_no' => $detail['dr_no'],
                'client_id' => $detail['client_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
            ]);
        }

        return redirect()->route('deliveries.index')->with('success', 'Delivery was successfully updated.');
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
    public function edit(DeliveryDetail $deliveryDetail)
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
    public function update(StoreDeliveryDetailRequest $request, DeliveryDetail $deliveryDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryDetail $deliveryDetail)
    {
        $deliveryDetail->delete();

        // return redirect()->route('posts.index')->with('success', 'DeliveryDetail deleted.');
    }


    public function restore(DeliveryDetail $deliveryDetail)
    {
        // $deliveryDetail->restore();

        // return redirect()->route('posts.index')->with('success', 'DeliveryDetail restored.');
    }
}
