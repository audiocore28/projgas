<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }

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

        $query->withCount([
            'batangasTransactionDetails',
            'mindoroTransactionDetails',
            'batangasPaymentDetails as unverified_batangas_payment_count' => function ($q) {
                $q->where('is_verified', false);
            },
            'mindoroPaymentDetails as unverified_mindoro_payment_count' => function ($q) {
                $q->where('is_verified', false);
            },
        ]);

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
    public function show(Client $client)
    {
       //  $mD = $client->mindoroTransactionDetails()
       //      ->latest()
       //      ->paginate()
       //      ->transform(function ($detail) {
       //          return [
       //              'month' => $detail->mindoroTransaction->monthlyMindoroTransaction ? $detail->mindoroTransaction->monthlyMindoroTransaction->month : null,
       //              'year' => $detail->mindoroTransaction->monthlyMindoroTransaction ? $detail->mindoroTransaction->monthlyMindoroTransaction->year : null,
       //              'monthly_mindoro_transaction_id' => $detail->mindoroTransaction->monthlyMindoroTransaction ? $detail->mindoroTransaction->monthlyMindoroTransaction->id : null,
       //              'trip_no' => $detail->mindoroTransaction ? $detail->mindoroTransaction->trip_no : null,
       //              'id' => $detail->id,
       //              'date' => $detail->date,
       //              'dr_no' => $detail->dr_no,
       //              'mindoro_transaction_id' => $detail->mindoro_transaction_id,
       //              'quantity' => $detail->quantity,
       //              'unit_price' => $detail->unit_price,
       //              'client' => $detail->client ? $detail->client->only('id', 'name') : null,
       //              'remarks' => $detail->remarks,
       //              'product' => $detail->product ? $detail->product->only('id', 'name') : null,
       //              'payments' => $detail->mindoroPaymentDetails->map(function ($payment) {
       //                  return [
       //                      'id' => $payment->id,
       //                      'date' => $payment->date,
       //                      'mode' => $payment->mode,
       //                      'amount' => $payment->amount,
       //                      'remarks' => $payment->remarks,
       //                      'mindoro_transaction_detail_id' => $payment->mindoro_transaction_detail_id,
       //                  ];
       //              }),
       //          ];
       //      });

       //  $bD = $client->batangasTransactionDetails()
       //      ->latest()
       //      ->paginate()
       //      ->transform(function ($detail) {
       //          return [
       //              'month' => $detail->batangasTransaction->monthlyBatangasTransaction ? $detail->batangasTransaction->monthlyBatangasTransaction->month : null,
       //              'year' => $detail->batangasTransaction->monthlyBatangasTransaction ? $detail->batangasTransaction->monthlyBatangasTransaction->year : null,
       //              'monthly_batangas_transaction_id' => $detail->batangasTransaction->monthlyBatangasTransaction ? $detail->batangasTransaction->monthlyBatangasTransaction->id : null,
       //              'trip_no' => $detail->batangasTransaction ? $detail->batangasTransaction->trip_no : null,
       //              'id' => $detail->id,
       //              'date' => $detail->date,
       //              'dr_no' => $detail->dr_no,
       //              'batangas_transaction_id' => $detail->batangas_transaction_id,
       //              'quantity' => $detail->quantity,
       //              'unit_price' => $detail->unit_price,
       //              'client' => $detail->client ? $detail->client->only('id', 'name') : null,
       //              'remarks' => $detail->remarks,
       //              'product' => $detail->product ? $detail->product->only('id', 'name') : null,
       //              'payments' => $detail->batangasPaymentDetails->map(function ($payment) {
       //                  return [
       //                      'id' => $payment->id,
       //                      'date' => $payment->date,
       //                      'mode' => $payment->mode,
       //                      'amount' => $payment->amount,
       //                      'remarks' => $payment->remarks,
       //                      'batangas_transaction_detail_id' => $payment->batangas_transaction_detail_id,
       //                  ];
       //              }),
       //          ];
       //      });

       //  $batangasDetails = $bD->groupBy(['year', 'month']);
       //  $mindoroDetails = $mD->groupBy(['year', 'month']);

       // if (request()->wantsJson()) {
       //   return [
       //     // 'batangasDetails' => $bD,
       //     // 'mindoroDetails' => $mD,
       //     'batangasDetails' => $batangasDetails,
       //     'mindoroDetails' => $mindoroDetails,
       //   ];
       // }

       //  return Inertia::render('Clients/Show', [
       //      'client' => [
       //          'id' => $client->id,
       //          'name' => $client->name,
       //          'office' => $client->office,
       //          'contact_person' => $client->contact_person,
       //          'contact_no' => $client->contact_no,
       //          'email_address' => $client->email_address,
       //          'deleted_at' => $client->deleted_at,
       //      ],
       //      'batangasDetails' => $batangasDetails,
       //      'mindoroDetails' => $mindoroDetails,
       //  ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
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
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, Client $client)
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
