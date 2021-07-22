<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MindoroTransaction;
use App\Models\Product;
use App\Models\Client;
use App\Models\MindoroPaymentDetail;
use Carbon\Carbon;

class MindoroTransactionDetail extends Model
{
    use HasFactory;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'dr_no', 'si_no', 'quantity', 'unit_price', 'remarks', 'mindoro_transaction_id', 'product_id', 'client_id'];

	 public function mindoroTransaction()
	 {
	 	return $this->belongsTo(MindoroTransaction::class);
	 }

	 public function product()
	 {
	 	return $this->belongsTo(Product::class);
	 }

	 public function client()
	 {
	 	return $this->belongsTo(Client::class);
	 }

	 public function mindoroPaymentDetails()
 	 {
 	 	return $this->hasMany(MindoroPaymentDetail::class);
 	 }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y');
    }

    public function addPayments($details)
    {
         foreach ($details as $detail) {
            $payment = $this->mindoroPaymentDetails()->findOrNew($detail['id']);

            if (auth()->user()->can('verify client payment', $this->client)) {
                $payment->date = $detail['date'];
                $payment->mode = $detail['mode'];
                $payment->amount = $detail['amount'];
                $payment->remarks = $detail['remarks'];
                $payment->mindoro_transaction_detail_id = $detail['mindoro_transaction_detail_id'];
                $payment->is_verified = $detail['is_verified'];
            } else {
                if (!$payment->is_verified) {
                    $payment->date = $detail['date'];
                    $payment->mode = $detail['mode'];
                    $payment->amount = $detail['amount'];
                    $payment->remarks = $detail['remarks'];
                    $payment->mindoro_transaction_detail_id = $detail['mindoro_transaction_detail_id'];
                }
            }

            $payment->save();
         }
    }


}
