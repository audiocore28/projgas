<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryRequest;
use App\Models\Delivery;
use App\Models\DeliveryDetail;
use App\Models\Purchase;
use App\Models\TankerLoad;
use App\Models\Client;
use App\Models\Product;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Deliveries/Index', [
            'filters' => Request::all('search'),
            'deliveries' => Delivery::filter(Request::only('search'))
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
        $clients = Client::all();
        $purchases = Purchase::all();
        $tankerLoads = TankerLoad::all();
        $products = Product::all();

        return Inertia::render('Deliveries/Create', [
            'clients' => $clients,
            'purchases' => $purchases,
            'tanker_loads' => $tankerLoads,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDeliveryRequest $request)
    {
        $deliveryId = Delivery::create([
            'date' => $request->date,
            'client_id' => $request->client_id,
            'purchase_id' => $request->purchase_id,
            'tanker_load_id' => $request->tanker_load_id,
        ])->id;


        foreach($request->details as $detail)
        {
            $deliveryDetail = DeliveryDetail::create([
                'delivery_id' => $deliveryId,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
                'amount' => $detail['amount'],
            ]);
        }

        return redirect()->route('deliveries.index')->with('success', 'Delivery was successfully added.');
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
    public function edit(Delivery $delivery)
    {
        $clients = Client::all();
        $purchases = Purchase::all();
        $tankerLoads = TankerLoad::all();
        $products = Product::all();

        return Inertia::render('Deliveries/Edit', [
            'delivery' => [
                'id' => $delivery->id,
                'date' => $delivery->date,
                'client_id' => $delivery->client_id,
                'purchase_id' => $delivery->purchase_id,
                'tanker_load_id' => $delivery->tanker_load_id,
                'details' => $delivery->deliveryDetails,
            ],
            'clients' => $clients,
            'purchases' => $purchases,
            'tanker_loads' => $tankerLoads,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreDeliveryRequest $request, Delivery $delivery)
    {
        // Purchase
        $delivery->update([
            'date' => $request->date,
            'client_id' => $request->client_id,
            'purchase_id' => $request->purchase_id,
            'tanker_load_id' => $request->tanker_load_id,
        ]);

        // PurchaseDetail
        $existingDetails = collect($request->details)->filter(function($value){
            return $value['id'] !== null;
        });

        foreach($existingDetails as $detail)
        {
            $delivery->deliveryDetails()->find($detail['id'])->update([
                // 'delivery_id' => $detail['delivery_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
                'amount' => $detail['amount'],
            ]);
        }

        return redirect()->route('deliveries.index')->with('success', 'Delivery updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('deliveries.index')->with('success', 'Delivery deleted.');
    }


    public function restore(Delivery $delivery)
    {
        $delivery->restore();

        return redirect()->route('deliveries.index')->with('success', 'Delivery restored.');
    }
}
