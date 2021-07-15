<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTankerRequest;
use App\Http\Requests\UpdateTankerRequest;
use App\Models\Tanker;
use App\Models\Driver;
use App\Models\Helper;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class TankerController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Tanker::class, 'tanker');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Tankers/Index', [
            'filters' => Request::all('search', 'trashed'),
            'tankers' => Tanker::filter(Request::only('search', 'trashed'))
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
        $drivers = Driver::all()->pluck('id');
        $helpers = Helper::all()->pluck('id');

        // dd($drivers);

        return Inertia::render('Tankers/Create', [
            // 'tankers' => Auth::user()->account
            'drivers' => $drivers,
            'helpers' => $helpers,
                // ->organizations()
                // ->orderBy('name')
                // ->get()
                // ->map
                // ->only('id', 'name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTankerRequest $request)
    {
        $tanker = Tanker::create($request->all());

        $tanker->drivers()->sync($request->driver_id);
        $tanker->helpers()->sync($request->helper_id);

        return redirect()->route('tankers.index')->with('success', 'Tanker was successfully added.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Tanker $tanker)
    {
        return Inertia::render('Tankers/Edit', [
            'tanker' => [
                'id' => $tanker->id,
                'plate_no' => $tanker->plate_no,
                'compartment' => $tanker->compartment,
                'deleted_at' => $tanker->deleted_at,
            ],
            'drivers' => $tanker->drivers,
            'helpers' => $tanker->helpers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTankerRequest $request, Tanker $tanker)
    {
        $tanker->update($request->all());

        return Redirect::back()->with('success', 'Tanker updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tanker $tanker)
    {
        if ($tanker->batangasTransactions()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, Batangas Transaction has tanker records']);
        }

        if ($tanker->mindoroTransactions()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, Mindoro Transaction has tanker records']);
        }

        $tanker->delete();

        return Redirect::back()->with('success', 'Tanker deleted.');
    }


    public function restore(Tanker $tanker)
    {
        $tanker->restore();

        return Redirect::back()->with('success', 'Tanker restored.');
    }
}
