<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMindoroPaymentDetailRequest;
use App\Models\Client;
use App\Models\MindoroPaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class MindoroPaymentDetailController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MindoroPaymentDetail  $mindoroPaymentDetail
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MindoroPaymentDetail  $mindoroPaymentDetail
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
                    'monthly_mindoro_transaction_id' => $detail->mindoroTransaction->monthlyMindoroTransaction ? $detail->mindoroTransaction->monthlyMindoroTransaction->id : null,
                    'trip_no' => $detail->mindoroTransaction ? $detail->mindoroTransaction->trip_no : null,
                    'id' => $detail->id,
                    'date' => $detail->date,
                    'dr_no' => $detail->dr_no,
                    'mindoro_transaction_id' => $detail->mindoro_transaction_id,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'client' => $detail->client ? $detail->client->only('id', 'name') : null,
                    'remarks' => $detail->remarks,
                    'product' => $detail->product ? $detail->product->only('id', 'name') : null,
                    'payments' => $detail->mindoroPaymentDetails->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'date' => $payment->date,
                            'mode' => $payment->mode,
                            'amount' => $payment->amount,
                            'remarks' => $payment->remarks,
                            'is_verified' => $payment->is_verified,
                            'mindoro_transaction_detail_id' => $payment->mindoro_transaction_detail_id,
                        ];
                    }),
                ];
            });

        $mindoroDetails = $mD->groupBy(['year', 'month']);

        return Inertia::render('MindoroPaymentDetails/Edit', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'office' => $client->office,
                'contact_person' => $client->contact_person,
                'contact_no' => $client->contact_no,
                'email_address' => $client->email_address,
                'deleted_at' => $client->deleted_at,
            ],
            'mindoroDetails' => $mindoroDetails,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MindoroPaymentDetail  $mindoroPaymentDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        foreach ($request->mindoroDetails as $year => $months) {
            foreach ($months as $month => $details) {
                foreach ($details as $detail) {
                    $transactionDetail = $client->mindoroTransactionDetails()->findOrFail($detail['id']);
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
     * @param  \App\Models\MindoroPaymentDetail  $mindoroPaymentDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(MindoroPaymentDetail $mindoroPaymentDetail)
    {
        //
    }

    public function deletePaymentDetails($paymentDetailIds, $client)
    {
        if (auth()->user()->can('verify client payment', $client)) {
            MindoroPaymentDetail::whereIn('id', $paymentDetailIds)->delete();
        } else {
            MindoroPaymentDetail::whereIn('id', $paymentDetailIds)->whereIn('is_verified', [false])->delete();
        }
    }
}
