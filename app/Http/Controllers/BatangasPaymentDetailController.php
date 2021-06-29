<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBatangasPaymentDetailRequest;
use App\Models\Client;
use App\Models\BatangasPaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class BatangasPaymentDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBatangasPaymentDetailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BatangasPaymentDetail  $batangasPaymentDetail
     * @return \Illuminate\Http\Response
     */
    public function show(BatangasPaymentDetail $batangasPaymentDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BatangasPaymentDetail  $batangasPaymentDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        $bD = $client->batangasTransactionDetails()
            ->latest()
            ->paginate()
            ->transform(function ($detail) {
                return [
                    'month' => $detail->batangasTransaction->monthlyBatangasTransaction ? $detail->batangasTransaction->monthlyBatangasTransaction->month : null,
                    'year' => $detail->batangasTransaction->monthlyBatangasTransaction ? $detail->batangasTransaction->monthlyBatangasTransaction->year : null,
                    'monthly_batangas_transaction_id' => $detail->batangasTransaction->monthlyBatangasTransaction ? $detail->batangasTransaction->monthlyBatangasTransaction->id : null,
                    'trip_no' => $detail->batangasTransaction ? $detail->batangasTransaction->trip_no : null,
                    'id' => $detail->id,
                    'date' => $detail->date,
                    'dr_no' => $detail->dr_no,
                    'batangas_transaction_id' => $detail->batangas_transaction_id,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'client' => $detail->client ? $detail->client->only('id', 'name') : null,
                    'remarks' => $detail->remarks,
                    'product' => $detail->product ? $detail->product->only('id', 'name') : null,
                    'payments' => $detail->batangasPaymentDetails->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'date' => $payment->date,
                            'mode' => $payment->mode,
                            'amount' => $payment->amount,
                            'remarks' => $payment->remarks,
                            'is_verified' => $payment->is_verified,
                            'batangas_transaction_detail_id' => $payment->batangas_transaction_detail_id,
                        ];
                    }),
                ];
            });

        $batangasDetails = $bD->groupBy(['year', 'month']);

        return Inertia::render('BatangasPaymentDetails/Edit', [
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
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BatangasPaymentDetail  $batangasPaymentDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        foreach ($request->batangasDetails as $year => $months) {
            foreach ($months as $month => $details) {
                foreach ($details as $detail) {
                    $transactionDetail = $client->batangasTransactionDetails()->findOrFail($detail['id']);
                    $transactionDetail->addPayments($detail['payments']);
                }
            }
        }

        $this->deletePaymentDetails($request->removed_payment_details, $client);

        return Redirect::back()->with('success', 'Payment/s updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BatangasPaymentDetail  $batangasPaymentDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(BatangasPaymentDetail $batangasPaymentDetail)
    {
        //
    }

    public function deletePaymentDetails($paymentDetailIds, $client)
    {
        if (auth()->user()->can('verify client payment', $client)) {
            BatangasPaymentDetail::whereIn('id', $paymentDetailIds)->delete();
        } else {
            BatangasPaymentDetail::whereIn('id', $paymentDetailIds)->whereIn('is_verified', [false])->delete();
        }
    }
}
