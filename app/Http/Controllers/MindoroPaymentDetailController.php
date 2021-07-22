<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMindoroPaymentDetailRequest;
use App\Models\Client;
use App\Models\MindoroPaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class MindoroPaymentDetailController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MindoroPaymentDetail  $mindoroPaymentDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        $query = $client->load([
            'mindoroTransactionDetails.mindoroTransaction.monthlyMindoroTransaction',
            'mindoroTransactionDetails.client:id,name',
            'mindoroTransactionDetails.product:id,name',
            'mindoroTransactionDetails.mindoroPaymentDetails',
        ]);

        $mindoroDetails = collect($query->mindoroTransactionDetails)
                                ->groupBy([
                                    'mindoroTransaction.monthlyMindoroTransaction.year',
                                    'mindoroTransaction.monthlyMindoroTransaction.month'
                                ]);


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
                // initialize vars
                $errorCount = 0;

                // check if all date was filled up
                foreach ($details as $detail) {
                    $validator = Validator::make($detail, ['mindoro_payment_details.*.date' => 'required']);

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
                    $transactionDetail = $client->mindoroTransactionDetails()->findOrFail($detail['id']);

                    if (auth()->user()->can('verify client payment', $client)) {
                        $transactionDetail->si_no = $detail['si_no'];
                        $transactionDetail->save();
                    }

                    $transactionDetail->addPayments($detail['mindoro_payment_details']);
                }
            }
        }

        $this->deletePaymentDetails($request->removed_payment_details, $client);

        return Redirect::back()->with('success', 'Payment/s updated.');
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
