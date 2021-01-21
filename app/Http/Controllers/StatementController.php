<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStatementRequest;
use App\Models\Statement;
use App\Models\Client;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class StatementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statements = Statement::filter(Request::only('search', 'trashed'))
                ->orderBy('id', 'desc')
                ->paginate(5)
                ->transform(function ($statement) {
                    return [
                        'id' => $statement->id,
                        'date' => $statement->date,
                        'soa_no' => $statement->soa_no,
                        'deleted_at' => $statement->deleted_at,
                        'client' => $statement->client ? $statement->client->only('name') : null,
                        ];
                });

        return Inertia::render('Statements/Index', [
            'filters' => Request::all('search', 'trashed'),
            'statements' => $statements,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clients = Client::all();

        return Inertia::render('Statements/Create', [
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStatementRequest $request)
    {
        $statement = Statement::create($request->all());

        return redirect()->route('statements.index')->with('success', 'SOA was successfully added.');
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
    public function edit(Statement $statement)
    {
        $clients = Client::all();

        return Inertia::render('Statements/Edit', [
            'statement' => [
                'id' => $statement->id,
                'date' => $statement->date,
                'client_id' => $statement->client_id,
                'payment' => $statement->payment,
                'check_no' => $statement->check_no,
                'soa_no' => $statement->soa_no,
                'deleted_at' => $statement->deleted_at,
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
    public function update(StoreStatementRequest $request, Statement $statement)
    {
        $statement->update($request->all());

        return Redirect::back()->with('success', 'SOA updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Statement $statement)
    {
        $statement->delete();

        return Redirect::back()->with('success', 'SOA deleted.');
    }


    public function restore(Statement $statement)
    {
        $statement->restore();

        return Redirect::back()->with('success', 'SOA restored.');
    }
}
