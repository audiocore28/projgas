<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepotRequest;
use App\Http\Requests\UpdateDepotRequest;
use App\Models\Depot;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DepotController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Depot::class, 'depot');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Depots/Index', [
            'filters' => Request::all('search', 'trashed'),
            'depots' => Depot::filter(Request::only('search', 'trashed'))
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
        return Inertia::render('Depots/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDepotRequest $request)
    {
        //
        $depot = Depot::create($request->all());

        return redirect()->route('depots.index')->with('success', 'Depot was successfully added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Depot  $depot
     * @return \Illuminate\Http\Response
     */
    public function show(Depot $depot)
    {
        $pD = $depot->purchases()
            ->latest()
            ->paginate()
            ->transform(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'date' => $purchase->date,
                    'purchase_no' => $purchase->purchase_no,
                    'purchases' => $purchase->purchaseDetails->each(function ($detail) {
                            return ['product' => $detail->product->name, 'purchase_no' => $detail->purchase->purchase_no];
                        })
                ];
            });

       if (request()->wantsJson()) {
         return [
            'purchaseDetails' => $pD,
         ];
       }

        return Inertia::render('Depots/Show', [
            'depot' => [
                'id' => $depot->id,
                'name' => $depot->name,
                'address' => $depot->address,
                'email_address' => $depot->email_address,
                'contact_person' => $depot->contact_person,
                'contact_no' => $depot->contact_no,
                'deleted_at' => $depot->deleted_at,
            ],
            'purchaseDetails' => $pD,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Depot  $depot
     * @return \Illuminate\Http\Response
     */
    public function edit(Depot $depot)
    {
        return Inertia::render('Depots/Edit', [
            'depot' => [
                'id' => $depot->id,
                'name' => $depot->name,
                'address' => $depot->address,
                'email_address' => $depot->email_address,
                'contact_person' => $depot->contact_person,
                'contact_no' => $depot->contact_no,
                'deleted_at' => $depot->deleted_at,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Depot  $depot
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDepotRequest $request, Depot $depot)
    {
        $depot->update($request->all());

        return Redirect::back()->with('success', 'Depot updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Depot  $depot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Depot $depot)
    {
        if ($depot->purchases()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, purchase has depot records']);
        }

        $depot->delete();

        return Redirect::back()->with('success', 'Depot deleted.');
    }


    public function restore(Depot $depot)
    {
        $depot->restore();

        return Redirect::back()->with('success', 'Depot restored.');
    }
}
