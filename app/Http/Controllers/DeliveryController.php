<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Models\Delivery;
use App\Models\DeliveryDetail;
use App\Models\Purchase;
use App\Models\TankerLoad;
use App\Models\Client;
use App\Models\Product;
use App\Models\Tanker;
use App\Models\Driver;
use App\Models\Helper;
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
        $deliveries = Delivery::filter(Request::only('search', 'trashed'))
            ->orderBy('id', 'desc')
            ->paginate()
            ->transform(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'trip_no' => $delivery->trip_no,
                    'purchase' => $delivery->purchase ? $delivery->purchase->only('purchase_no') : null,
                    'tanker' => $delivery->tanker ? $delivery->tanker->only('plate_no') : null,
                    'driver' => $delivery->driver ? $delivery->driver->only('name') : null,
                    'helper' => $delivery->helper ? $delivery->helper->only('name') : null,
                    'clients' => $delivery->deliveryDetails->each(function ($detail) {
                            return [ 'name' => $detail->client->name, 'dr_no' => $detail->dr_no ];
                        }),
                    // 'tanker_load_id' => $delivery->tanker_load_id,
                ];
            });

        return Inertia::render('Deliveries/Index', [
            'filters' => Request::all('search', 'trashed'),
            'deliveries' => $deliveries,
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
        $tankers = Tanker::all();
        $drivers = Driver::all();
        $helpers = Helper::all();
        $products = Product::all();

        return Inertia::render('Deliveries/Create', [
            'clients' => $clients,
            'purchases' => $purchases,
            'products' => $products,
            'tankers' => $tankers,
            'drivers' => $drivers,
            'helpers' => $helpers,
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
            'trip_no' => $request->trip_no,
            'purchase_id' => $request->purchase_id,
            'tanker_id' => $request->tanker_id,
            'driver_id' => $request->driver_id,
            'helper_id' => $request->helper_id,
        ])->id;


        foreach($request->details as $detail)
        {
            $deliveryDetail = DeliveryDetail::create([
                'delivery_id' => $deliveryId,
                'date' => $detail['date'],
                'dr_no' => $detail['dr_no'],
                'client_id' => $detail['client_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
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
        $tankers = Tanker::all();
        $drivers = Driver::all();
        $helpers = Helper::all();
        $products = Product::all();

        return Inertia::render('Deliveries/Edit', [
            'delivery' => [
                'id' => $delivery->id,
                'trip_no' => $delivery->trip_no,
                'tanker_id' => $delivery->tanker_id,
                'driver_id' => $delivery->driver_id,
                'helper_id' => $delivery->helper_id,
                'purchase_id' => $delivery->purchase_id,
                'details' => $delivery->deliveryDetails,
            ],
            'clients' => $clients,
            'purchases' => $purchases,
            'tankers' => $tankers,
            'drivers' => $drivers,
            'helpers' => $helpers,
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
    public function update(UpdateDeliveryRequest $request, Delivery $delivery)
    {
        // Delivery
        $delivery->update([
            'trip_no' => $request->trip_no,
            'tanker_id' => $request->tanker_id,
            'driver_id' => $request->driver_id,
            'helper_id' => $request->helper_id,
            'purchase_id' => $request->purchase_id,
        ]);

        // DeliveryDetail
        $existingDetails = collect($request->details)->filter(function($value){
            return $value['id'] !== null;
        });

        foreach($existingDetails as $detail)
        {
            $delivery->deliveryDetails()->find($detail['id'])->update([
                // 'delivery_id' => $detail['delivery_id'],
                'date' => $detail['date'],
                'dr_no' => $detail['dr_no'],
                'client_id' => $detail['client_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
            ]);
        }

        return Redirect::back()->with('success', 'Delivery updated.');
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

}
