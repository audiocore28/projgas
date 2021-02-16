<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Calibration extends Model
{
	use HasFactory;

	protected $fillable = ['pump', 'quantity', 'pump_no', 'voucher_no', 'station_transaction_id', ];

	public function stationTransaction()
	{
		return $this->belongsTo(StationTransaction::class);
	}

}
