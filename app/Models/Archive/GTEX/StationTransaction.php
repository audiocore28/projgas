<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class StationTransaction extends Model
{
	use HasFactory;

	protected $dates = ['date'];
	protected $fillable = ['date', 'shift', 'station_id', 'cashier_id', 'pump_attendant_id', 'office_staff_id'];

	public function scopeFilter($query, array $filters)
	{
		$query->when($filters['search'] ?? null, function ($query, $search) {
			$query->where(function ($query) use ($search) {
				$query->whereHas('station', function ($query) use ($search) {
						$query->where('name', 'like', '%'.$search.'%');
					})
					->orWhereHas('cashier', function ($query) use ($search) {
						$query->where('name', 'like', '%'.$search.'%');
					})
					->orWhereHas('officeStaff', function ($query) use ($search) {
						$query->where('name', 'like', '%'.$search.'%');
					});
			});
		});
	}

	public function station()
	{
		return $this->belongsTo(Station::class);
	}

	public function cashier()
	{
		return $this->belongsTo(Cashier::class);
	}

	public function pumpAttendant()
	{
		return $this->belongsTo(PumpAttendant::class);
	}

	public function officeStaff()
	{
		return $this->belongsTo(OfficeStaff::class);
	}

	public function pumpReadings()
	{
		return $this->hasMany(PumpReading::class);
	}

	public function sales()
	{
		return $this->hasMany(Sale::class);
	}

	public function companyVales()
	{
		return $this->hasMany(CompanyVale::class);
	}

	public function calibrations()
	{
		return $this->hasMany(Calibration::class);
	}

	public function discounts()
	{
		return $this->hasMany(Discount::class);
	}

	public function getDateAttribute($value)
	{
		return Carbon::parse($value)->format('M d, Y');
	}

}
