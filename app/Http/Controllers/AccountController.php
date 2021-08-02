<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Account::class, 'account');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Accounts/Index', [
            'filters' => Request::all('search', 'trashed'),
            'accounts' => Account::filter(Request::only('search', 'trashed'))
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
        return Inertia::render('Accounts/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccountRequest $request)
    {
        $account = Account::create($request->all());

        return redirect()->route('accounts.index')->with('success', 'Account was successfully added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        $query = $account->load([
            'purchases' => function ($query) {
                $query->select(['id', 'account_id', 'date', 'purchase_no']);
            },
            'purchases.purchaseDetails.product:id,name'
        ]);

        $account = collect($query)->toArray();

       // if (request()->wantsJson()) {
       //   return [
       //      'purchaseDetails' => $account,
       //   ];
       // }

        return Inertia::render('Accounts/Show', [
            'account' => $account,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        return Inertia::render('Accounts/Edit', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
                'deleted_at' => $account->deleted_at,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountRequest $request, Account $account)
    {
        $account->update($request->all());

        return Redirect::back()->with('success', 'Account updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        if ($account->purchases()->count()) {
            return back()->withErrors(['error' => 'Cannot delete, purchase has account records']);
        }

        $account->delete();

        return Redirect::back()->with('success', 'Account deleted.');
    }


    public function restore(Account $account)
    {
        $account->restore();

        return Redirect::back()->with('success', 'Account restored.');
    }
}
