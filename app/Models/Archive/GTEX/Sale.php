<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
	use HasFactory;

	protected $fillable = ['pump_no', 'dr_no', 'client_id', 'product_id', 'quantity', 'rs_no', 'station_transaction_id'];

	public function stationTransaction()
	{
		return $this->belongsTo(StationTransaction::class);
	}

}
