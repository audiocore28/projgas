<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHelperRequest;
use App\Models\Helper;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class HelperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Helpers/Index', [
            'filters' => Request::all('search', 'trashed'),
            'helpers' => Helper::filter(Request::only('search', 'trashed'))
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
        return Inertia::render('Helpers/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHelperRequest $request)
    {
        $helper = Helper::create($request->all());

        return redirect()->route('helpers.index')->with('success', 'Helper was successfully added.');
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
    public function edit(Helper $helper)
    {
        $trips = $helper->mindoroTransactions()
            ->selectRaw('year(date) year, monthname(date) month, count(*) loaded')
            ->groupBy('year', 'month')
            ->orderByRaw('min(date)')
            ->get()
            ->toArray();

        $bD = $helper->batangasTransactions()
            ->latest()
            ->paginate()
            ->transform(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->date,
                    'trip_no' => $transaction->trip_no,
                    'purchases' => $transaction->purchases->each(function ($purchase) {
                            return ['purchase_no' => $purchase->purchase_no, ];
                        }),
                    'tanker' => $transaction->tanker ? $transaction->tanker->only('plate_no') : null,
                    'driver' => $transaction->driver ? $transaction->driver->only('name') : null,
                    'helper' => $transaction->helper ? $transaction->helper->only('name') : null,
                    'details' => $transaction->batangasTransactionDetails->each(function ($detail) {
                            return ['p' => $detail->product->name, 'c' => $detail->client->name];
                        })
                ];
            });

        $mD = $helper->mindoroTransactions()
            ->latest()
            ->paginate()
            ->transform(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->date,
                    'trip_no' => $transaction->trip_no,
                    'purchases' => $transaction->purchases->each(function ($purchase) {
                            return ['purchase_no' => $purchase->purchase_no, ];
                        }),
                    'tanker' => $transaction->tanker ? $transaction->tanker->only('plate_no') : null,
                    'driver' => $transaction->driver ? $transaction->driver->only('name') : null,
                    'helper' => $transaction->helper ? $transaction->helper->only('name') : null,
                    'details' => $transaction->mindoroTransactionDetails->each(function ($detail) {
                            return ['p' => $detail->product->name, 'c' => $detail->client->name];
                        })
                ];
            });

       if (request()->wantsJson()) {
         return [
           // 'loadDetails' => $lD,
           'batangasDetails' => $bD,
           'mindoroDetails' => $mD,
         ];
       }

        return Inertia::render('Helpers/Edit', [
            'helper' => [
                'id' => $helper->id,
                'name' => $helper->name,
                'nickname' => $helper->nickname,
                'address' => $helper->address,
                'contact_no' => $helper->contact_no,
                'deleted_at' => $helper->deleted_at,
            ],
           // 'loadDetails' => $lD,
           'batangasDetails' => $bD,
           'mindoroDetails' => $mD,
           'trips' => $trips,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreHelperRequest $request, Helper $helper)
    {
        $helper->update($request->all());

        return Redirect::back()->with('success', 'Helper updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Helper $helper)
    {
        if ($helper->batangasTransactions()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, Batangas transactions has helper records']);
        }

        if ($helper->mindoroTransactions()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, Mindoro transactions has helper records']);
        }

        $helper->delete();

        return Redirect::back()->with('success', 'Helper deleted.');
    }


    public function restore(Helper $helper)
    {
        $helper->restore();

        return Redirect::back()->with('success', 'Helper restored.');
    }
}
