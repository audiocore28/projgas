<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientPaymentRequest;
use App\Models\ClientPayment;
use App\Models\Client;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ClientPaymentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ClientPayment::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('ClientPayments/Index', [
            'filters' => Request::all('search', 'trashed'),
            'client_payments' => ClientPayment::filter(Request::only('search', 'trashed'))
                ->with('client')
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
        // Clients
        $clients = Client::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->orderBy('name', 'asc')->get()->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => Str::limit($client->name, 22, '...'),
            ];
        });

        return Inertia::render('ClientPayments/Create', [
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientPaymentRequest $request)
    {
        $clientPayment = ClientPayment::create([
            'date' => $request->date,
            'mode' => $request->mode,
            'amount' => $request->amount,
            'remarks' => $request->remarks,
            'client_id' => $request->client['id'],
        ]);

        return redirect()->route('client-payments.index')->with('success', 'Payment was successfully added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ClientPayment $clientPayment)
    {
        return Inertia::render('ClientPayments/Show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ClientPayment $clientPayment)
    {
        // Clients
        $clients = Client::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->orderBy('name', 'asc')->get()->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => Str::limit($client->name, 22, '...'),
            ];
        });

        return Inertia::render('ClientPayments/Edit', [
            'client_payment' => [
                'id' => $clientPayment->id,
                'date' => $clientPayment->date,
                'mode' => $clientPayment->mode,
                'amount' => $clientPayment->amount,
                'remarks' => $clientPayment->remarks,
                'selected_client' => $clientPayment->client_id,
                'client' => $clientPayment->client ? $clientPayment->client->only('id', 'name') : null,
                'deleted_at' => $clientPayment->deleted_at,
            ],
            'clients' => $clients,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreClientPaymentRequest $request, ClientPayment $clientPayment)
    {
        $clientPayment->update($request->all());

        return Redirect::back()->with('success', 'Payment updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientPayment $clientPayment)
    {
        $clientPayment->delete();

        return Redirect::back()->with('success', 'Payment deleted.');
    }


    public function restore(ClientPayment $clientPayment)
    {
        $clientPayment->restore();

        return Redirect::back()->with('success', 'Payment restored.');
    }
}
