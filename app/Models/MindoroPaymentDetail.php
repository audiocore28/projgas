<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Client;
use App\Models\MindoroTransactionDetail;
use Carbon\Carbon;

class MindoroPaymentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'mode', 'amount', 'remarks', 'mindoro_transaction_detail_id'];


	 public function mindoro_transaction_detail()
	 {
	 	return $this->belongsTo(MindoroTransactionDetail::class);
	 }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone', 'Asia/Manila'))->format('M d, Y');
    }

}
