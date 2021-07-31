<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBatangasPaymentDetailRequest;
use App\Models\Client;
use App\Models\BatangasPaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class BatangasPaymentDetailController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BatangasPaymentDetail  $batangasPaymentDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        $query = $client->load([
            'batangasTransactionDetails' => function ($q) {
                $q->orderBy('date', 'DESC');
            },
            'batangasTransactionDetails.batangasTransaction.monthlyBatangasTransaction',
            'batangasTransactionDetails.client:id,name',
            'batangasTransactionDetails.product:id,name',
            'batangasTransactionDetails.batangasPaymentDetails',
        ]);

        $batangasDetails = collect($query->batangasTransactionDetails)
                                ->groupBy([
                                    'batangasTransaction.monthlyBatangasTransaction.year',
                                    'batangasTransaction.monthlyBatangasTransaction.month'
                                ]);

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
                // initialize vars
                $errorCount = 0;

                // check if all date was filled up
                foreach ($details as $detail) {
                    $validator = Validator::make($detail, ['batangas_payment_details.*.date' => 'required']);

                    if ($validator->fails()) {
                        $errorCount++;
                    }
                }

                // if there's a blank date then don't save any changes, & display error msg
                if ($errorCount > 0) {
                    return redirect::back()->withErrors('Date is required');
                }

                // else save changes
                foreach ($details as $detail) {
                    $transactionDetail = $client->batangasTransactionDetails()->findOrFail($detail['id']);
                    $transactionDetail->addPayments($detail['batangas_payment_details']);
                }
            }
        }

        $this->deletePaymentDetails($request->removed_payment_details, $client);

        return Redirect::back()->with('success', 'Payment/s updated.');
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
