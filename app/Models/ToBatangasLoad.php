<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BatangasTransaction;
use App\Models\Purchase;
use App\Models\Tanker;
use App\Models\Driver;
use App\Models\Helper;
use App\Models\ToBatangasLoadDetail;
use Carbon\Carbon;

class ToBatangasLoad extends Model
{
    use HasFactory;

	 protected $fillable = ['batangas_transaction_id', 'remarks', 'purchase_id'];


	 public function purchase()
	 {
	 	return $this->belongsTo(Purchase::class);
	 }

	 public function batangasTransaction()
	 {
	 	return $this->belongsTo(BatangasTransaction::class);
	 }

	 public function toBatangasLoadDetails()
	 {
	 	return $this->hasMany(ToBatangasLoadDetail::class);
	 }

}
