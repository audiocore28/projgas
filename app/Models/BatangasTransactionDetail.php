<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BatangasTransaction;
use App\Models\Product;
use App\Models\Client;
use App\Models\BatangasPaymentDetail;
use Carbon\Carbon;

class BatangasTransactionDetail extends Model
{
    use HasFactory;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'quantity', 'unit_price', 'remarks', 'batangas_transaction_id', 'product_id', 'client_id'];

	 public function batangasTransaction()
	 {
	 	return $this->belongsTo(BatangasTransaction::class);
	 }

	 public function product()
	 {
	 	return $this->belongsTo(Product::class);
	 }

	 public function client()
	 {
	 	return $this->belongsTo(Client::class);
	 }

	 public function batangasPaymentDetails()
	 {
	 	return $this->hasMany(BatangasPaymentDetail::class);
	 }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y');
    }

    public function addPayments($details)
    {
         foreach ($details as $detail) {
            $payment = $this->batangasPaymentDetails()->findOrNew($detail['id']);

	        	if (auth()->user()->can('verify client payment', $this->client)) {
	            $payment->date = $detail['date'];
	            $payment->mode = $detail['mode'];
	            $payment->amount = $detail['amount'];
	            $payment->remarks = $detail['remarks'];
	            $payment->batangas_transaction_detail_id = $detail['batangas_transaction_detail_id'];
         		$payment->is_verified = $detail['is_verified'];
	        	} else {
	            if (!$payment->is_verified) {
		            $payment->date = $detail['date'];
		            $payment->mode = $detail['mode'];
		            $payment->amount = $detail['amount'];
		            $payment->remarks = $detail['remarks'];
		            $payment->batangas_transaction_detail_id = $detail['batangas_transaction_detail_id'];
	            }
	        	}

            $payment->save();
         }
    }

}
