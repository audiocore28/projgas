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
        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:id,name'],
        ]);

        $query = Client::query();

        if (request('search')) {
            $query->where('name', 'like', '%'.request('search').'%')
                ->orWhere('office', 'like', '%'.request('search').'%')
                ->orWhere('contact_no', 'like', '%'.request('search').'%');
        }

        if (request()->has(['field', 'direction'])) {
            $query->orderBy(request('field'), request('direction'));
        }

        if (request('trashed')) {
            if (request('trashed') === 'with') {
                $query->withTrashed();
            } elseif (request('trashed') === 'only') {
                $query->onlyTrashed();
            }
        }

        return Inertia::render('Clients/Index', [
            'filters' => Request::all('search', 'field', 'direction', 'trashed'),
            'clients' => $query->orderBy('id', 'desc')->paginate(),
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
        $mD = $client->mindoroTransactionDetails()
            ->latest()
            ->paginate()
            ->transform(function ($detail) {
                return [
                    'month' => $detail->mindoroTransaction->monthlyMindoroTransaction ? $detail->mindoroTransaction->monthlyMindoroTransaction->month : null,
                    'year' => $detail->mindoroTransaction->monthlyMindoroTransaction ? $detail->mindoroTransaction->monthlyMindoroTransaction->year : null,
                    'id' => $detail->id,
                    'date' => $detail->date,
                    'dr_no' => $detail->dr_no,
                    'mindoro_transaction_id' => $detail->mindoro_transaction_id,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'client' => $detail->client ? $detail->client->only('id', 'name') : null,
                    'product' => $detail->product ? $detail->product->only('id', 'name') : null,
                ];
            });

        $bD = $client->batangasTransactionDetails()
            ->latest()
            ->paginate()
            ->transform(function ($detail) {
                return [
                    'month' => $detail->batangasTransaction->monthlyBatangasTransaction ? $detail->batangasTransaction->monthlyBatangasTransaction->month : null,
                    'year' => $detail->batangasTransaction->monthlyBatangasTransaction ? $detail->batangasTransaction->monthlyBatangasTransaction->year : null,
                    'id' => $detail->id,
                    'date' => $detail->date,
                    'dr_no' => $detail->dr_no,
                    'batangas_transaction_id' => $detail->batangas_transaction_id,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'client' => $detail->client ? $detail->client->only('id', 'name') : null,
                    'product' => $detail->product ? $detail->product->only('id', 'name') : null,
                ];
            });

        $batangasDetails = $bD->groupBy(['year', 'month']);
        $mindoroDetails = $mD->groupBy(['year', 'month']);

       if (request()->wantsJson()) {
         return [
           'batangasDetails' => $bD,
           'mindoroDetails' => $mD,
         ];
       }

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
            'batangasDetails' => $batangasDetails,
            'mindoroDetails' => $mindoroDetails,
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
        if ($client->batangasTransactionDetails()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, Batangas transactions has client records']);
        }

        if ($client->mindoroTransactionDetails()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, Mindoro transactions has client records']);
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
