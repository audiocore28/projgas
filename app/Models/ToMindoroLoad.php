<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MindoroTransaction;
use App\Models\Purchase;
use App\Models\Tanker;
use App\Models\Driver;
use App\Models\Helper;
use App\Models\ToMindoroLoadDetail;
use Carbon\Carbon;

class ToMindoroLoad extends Model
{
    use HasFactory;

	 protected $fillable = ['mindoro_transaction_id', 'remarks', 'purchase_id'];


	 public function purchase()
	 {
	 	return $this->belongsTo(Purchase::class);
	 }

	 public function mindoroTransaction()
	 {
	 	return $this->belongsTo(MindoroTransaction::class);
	 }

	 public function toMindoroLoadDetails()
	 {
	 	return $this->hasMany(ToMindoroLoadDetail::class);
	 }

}
