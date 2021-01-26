<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Clients/Index', [
            'filters' => Request::all('search', 'trashed'),
            'clients' => Client::filter(Request::only('search', 'trashed'))
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
        return Inertia::render('Clients/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientRequest $request)
    {
        $client = Client::create($request->all());

        return redirect()->route('clients.index')->with('success', 'Client was successfully added.');
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
    public function edit(Client $client)
    {
        $hD = $client->haulDetails->transform(function ($haul) {
            return [
                'id' => $haul->id,
                'date' => $haul->date,
                'haul_id' => $haul->haul_id,
                'quantity' => $haul->quantity,
                'unit_price' => $haul->unit_price,
                'client' => $haul->client ? $haul->client->only('id', 'name') : null,
                'product' => $haul->product ? $haul->product->only('id', 'name') : null,
            ];
        });

        $dD = $client->deliveryDetails->transform(function ($delivery) {
            return [
                'id' => $delivery->id,
                'date' => $delivery->date,
                'delivery_id' => $delivery->delivery_id,
                'dr_no' => $delivery->dr_no,
                'quantity' => $delivery->quantity,
                'unit_price' => $delivery->unit_price,
                'client' => $delivery->client ? $delivery->client->only('id', 'name') : null,
                'product' => $delivery->product ? $delivery->product->only('id', 'name') : null,
            ];
        });

        return Inertia::render('Clients/Edit', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'office' => $client->office,
                'contact_person' => $client->contact_person,
                'contact_no' => $client->contact_no,
                'email_address' => $client->email_address,
                'deleted_at' => $client->deleted_at,
            ],
            'deliveryDetails' => $dD,
            'haulDetails' => $hD,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreClientRequest $request, Client $client)
    {
        $client->update($request->all());

        return Redirect::back()->with('success', 'Client updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        if ($client->deliveryDetails()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, delivery has client records']);
        }

        if ($client->haulDetails()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, hauling has client records']);
        }

        $client->delete();

        return Redirect::back()->with('success', 'Client deleted.');
    }


    public function restore(Client $client)
    {
        $client->restore();

        return Redirect::back()->with('success', 'Client restored.');
    }
}
