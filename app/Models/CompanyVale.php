<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyVale extends Model
{
	use HasFactory;

	protected $fillable = [
		'pump_no', 'voucher_no', 'company_id', 'product_id', 'quantity', 'station_transaction_id', 'remarks'
	];

	public function stationTransaction()
	{
		return $this->belongsTo(StationTransaction::class);
	}
}
