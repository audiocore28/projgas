<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHaulRequest;
use App\Models\Haul;
use App\Models\HaulDetail;
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

class HaulController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hauls = Haul::filter(Request::only('search', 'trashed'))
            ->orderBy('id', 'desc')
            ->paginate()
            ->transform(function ($haul) {
                return [
                    'id' => $haul->id,
                    'trip_no' => $haul->trip_no,
                    'purchase' => $haul->purchase ? $haul->purchase->only('purchase_no') : null,
                    'tanker' => $haul->tanker ? $haul->tanker->only('plate_no') : null,
                    'driver' => $haul->driver ? $haul->driver->only('name') : null,
                    'helper' => $haul->helper ? $haul->helper->only('name') : null,
                    'clients' => $haul->haulDetails->each(function ($detail) {
                            return [ 'name' => $detail->client->name ];
                        }),
                    // 'tanker_load_id' => $haul->tanker_load_id,
                ];
            });

        return Inertia::render('Hauls/Index', [
            'filters' => Request::all('search', 'trashed'),
            'hauls' => $hauls,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $purchases = Purchase::when(request('term'), function($query, $term) {
            $query->where('purchase_no', 'like', "%$term%");
        })->get();

        $tankers = Tanker::orderBy('plate_no', 'asc')->get();
        $drivers = Driver::orderBy('name', 'asc')->get();
        $helpers = Helper::orderBy('name', 'asc')->get();

        $clients = Client::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->get();

        $products = Product::orderBy('name', 'asc')->get();

        return Inertia::render('Hauls/Create', [
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
    public function store(StoreHaulRequest $request)
    {
        $haulId = Haul::create([
            'trip_no' => $request->trip_no,
            'purchase_id' => $request->purchase_id,
            'tanker_id' => $request->tanker_id,
            'driver_id' => $request->driver_id,
            'helper_id' => $request->helper_id,
        ])->id;


        foreach($request->details as $detail)
        {
            $haulDetail = HaulDetail::create([
                'haul_id' => $haulId,
                'date' => $detail['date'],
                'client_id' => $detail['client_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
            ]);
        }

        return redirect()->route('hauls.index')->with('success', 'Hauling was successfully added.');
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
    public function edit(Haul $haul)
    {
        $purchases = Purchase::when(request('term'), function($query, $term) {
            $query->where('purchase_no', 'like', "%$term%");
        })->get();

        $tankers = Tanker::orderBy('plate_no', 'asc')->get();
        $drivers = Driver::orderBy('name', 'asc')->get();
        $helpers = Helper::orderBy('name', 'asc')->get();

        $clients = Client::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->get();

        $products = Product::orderBy('name', 'asc')->get();
        $details = $haul->haulDetails
                ->map(function ($detail) {
                    return [
                       'id' => $detail->id,
                       'date' => $detail->date,
                       'quantity' => $detail->quantity,
                       'unit_price' => $detail->unit_price,
                       'haul_id' => $detail->haul_id,
                       'product_id' => $detail->product_id,
                       'client_id' => $detail->client_id,
                       'selectedClient' => $detail->client_id,
                    ];
                })
                ->toArray();

        return Inertia::render('Hauls/Edit', [
            'haul' => [
                'id' => $haul->id,
                'trip_no' => $haul->trip_no,
                'tanker_id' => $haul->tanker_id,
                'driver_id' => $haul->driver_id,
                'helper_id' => $haul->helper_id,
                'purchase_id' => $haul->purchase_id,
                'details' => $details,
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
    public function update(StoreHaulRequest $request, Haul $haul)
    {
        // Haul
        $haul->update([
            'trip_no' => $request->trip_no,
            'tanker_id' => $request->tanker_id,
            'driver_id' => $request->driver_id,
            'helper_id' => $request->helper_id,
            'purchase_id' => $request->purchase_id,
        ]);

        // PurchaseDetail
        $existingDetails = collect($request->details)->filter(function($value){
            return $value['id'] !== null;
        });

        foreach($existingDetails as $detail)
        {
            $haul->haulDetails()->find($detail['id'])->update([
                // 'haul_id' => $detail['haul_id'],
                'date' => $detail['date'],
                'client_id' => $detail['client_id'],
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unit_price' => $detail['unit_price'],
            ]);
        }

        return Redirect::back()->with('success', 'Hauling updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Haul $haul)
    {
        $haul->delete();

        return redirect()->route('hauls.index')->with('success', 'Hauling deleted.');
    }

}
