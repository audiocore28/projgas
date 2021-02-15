<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PumpReading extends Model
{
    use HasFactory;

	 protected $fillable = ['pump', 'opening', 'closing', 'unit_price', 'station_transaction_id'];

	public function stationTransaction()
	{
		return $this->belongsTo(StationTransaction::class);
	}
}
