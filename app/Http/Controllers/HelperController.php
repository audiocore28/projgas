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
        $lD = $helper->tankerLoads()
            ->latest()
            ->paginate()
            ->transform(function ($load) {
                return [
                    'id' => $load->id,
                    'date' => $load->date,
                    'trip_no' => $load->trip_no,
                    'purchase' => $load->purchase ? $load->purchase->only('purchase_no') : null,
                    'tanker' => $load->tanker ? $load->tanker->only('plate_no') : null,
                    'driver' => $load->driver ? $load->driver->only('name') : null,
                    'helper' => $load->helper ? $load->helper->only('name') : null,
                    'loads' => $load->tankerLoadDetails->each(function ($detail) {
                            return ['p' => $detail->product->name];
                        })
                ];
            });

        $hD = $helper->hauls()
            ->latest()
            ->paginate()
            ->transform(function ($haul) {
                return [
                    'id' => $haul->id,
                    'tanker' => $haul->tanker ? $haul->tanker->only('plate_no') : null,
                    'driver' => $haul->driver ? $haul->driver->only('name') : null,
                    'helper' => $haul->helper ? $haul->helper->only('name') : null,
                    'hauls' => $haul->haulDetails->each(function ($detail) {
                            return ['p' => $detail->product->name, 'c' => $detail->client->name];
                        })
                ];
            });

        $dD = $helper->deliveries()
            ->latest()
            ->paginate()
            ->transform(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'tanker' => $delivery->tanker ? $delivery->tanker->only('plate_no') : null,
                    'driver' => $delivery->driver ? $delivery->driver->only('name') : null,
                    'helper' => $delivery->helper ? $delivery->helper->only('name') : null,
                    'deliveries' => $delivery->deliveryDetails->each(function ($detail) {
                            return ['p' => $detail->product->name, 'c' => $detail->client->name];
                        })
                ];
            });

       if (request()->wantsJson()) {
         return [
           'loadDetails' => $lD,
           'deliveryDetails' => $dD,
           'haulDetails' => $hD,
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
           'loadDetails' => $lD,
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
        if ($helper->deliveries()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, delivery has helper records']);
        }

        if ($helper->hauls()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, hauling has helper records']);
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
