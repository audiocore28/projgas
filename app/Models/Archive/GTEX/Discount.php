<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
	use HasFactory;

	protected $fillable = ['voucher_no', 'cash', 'client_id', 'quantity', 'disc_amount', 'station_transaction_id'];

	public function stationTransaction()
	{
		return $this->belongsTo(StationTransaction::class);
	}

}
