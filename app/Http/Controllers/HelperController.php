<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHelperRequest;
use App\Http\Requests\UpdateHelperRequest;
use App\Models\Helper;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class HelperController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Helper::class, 'helper');
    }

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
    public function show(Helper $helper)
    {
        $query = $helper->load([
            // Batangas
            'batangasTransactions.monthlyBatangasTransaction',
            'batangasTransactions.tanker:id,plate_no',
            'batangasTransactions.driver:id,name',
            'batangasTransactions.helper:id,name',
            'batangasTransactions.purchases:id,purchase_no',
            'batangasTransactions.batangasTransactionDetails.product:id,name',
            'batangasTransactions.batangasTransactionDetails.client:id,name',
            // Mindoro
            'mindoroTransactions.monthlyMindoroTransaction',
            'mindoroTransactions.tanker:id,plate_no',
            'mindoroTransactions.driver:id,name',
            'mindoroTransactions.helper:id,name',
            'mindoroTransactions.purchases:id,purchase_no',
            'mindoroTransactions.mindoroTransactionDetails.product:id,name',
            'mindoroTransactions.mindoroTransactionDetails.client:id,name',
        ]);

        $batangasTrips = collect($query->batangasTransactions)
                    ->groupBy([
                        'monthlyBatangasTransaction.year',
                        'monthlyBatangasTransaction.month'
                    ]);

        $mindoroTrips = collect($query->mindoroTransactions)
                    ->groupBy([
                        'monthlyMindoroTransaction.year',
                        'monthlyMindoroTransaction.month'
                    ]);


       // if (request()->wantsJson()) {
       //   return [
       //     'batangasDetails' => $bD,
       //     'mindoroDetails' => $mD,
       //   ];
       // }

        return Inertia::render('Helpers/Show', [
            'helper' => [
                'id' => $helper->id,
                'name' => $helper->name,
                'nickname' => $helper->nickname,
                'address' => $helper->address,
                'contact_no' => $helper->contact_no,
                'deleted_at' => $helper->deleted_at,
            ],
           'batangasTrips' => $batangasTrips,
           'mindoroTrips' => $mindoroTrips,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Helper $helper)
    {
        return Inertia::render('Helpers/Edit', [
            'helper' => [
                'id' => $helper->id,
                'name' => $helper->name,
                'nickname' => $helper->nickname,
                'address' => $helper->address,
                'contact_no' => $helper->contact_no,
                'deleted_at' => $helper->deleted_at,
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
    public function update(UpdateHelperRequest $request, Helper $helper)
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
