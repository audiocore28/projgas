<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Client;
use App\Models\BatangasTransactionDetail;
use Carbon\Carbon;

class BatangasPaymentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'mode', 'amount', 'remarks', 'batangas_transaction_detail_id'];


	 public function batangas_transaction_detail()
	 {
	 	return $this->belongsTo(BatangasTransactionDetail::class);
	 }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone', 'Asia/Manila'))->format('M d, Y');
    }

}
