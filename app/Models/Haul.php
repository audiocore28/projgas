<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\HaulDetail;
use App\Models\Purchase;
use App\Models\Tanker;
use App\Models\Driver;
use App\Models\Helper;
use Carbon\Carbon;

class Haul extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	 protected $fillable = ['purchase_id', 'tanker_id', 'driver_id', 'helper_id'];

	 public function purchase()
	 {
	 	return $this->belongsTo(Purchase::class);
	 }

	 public function tanker()
	 {
	 	return $this->belongsTo(Tanker::class);
	 }

	 public function driver()
	 {
	 	return $this->belongsTo(Driver::class);
	 }

	 public function helper()
	 {
	 	return $this->belongsTo(Helper::class);
	 }

	 public function HaulDetails()
	 {
	 	return $this->hasMany(HaulDetail::class);
	 }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->WhereHas('client', function ($query) use ($search) {
	                        $query->where('name', 'like', '%'.$search.'%');
	                    });
            });
        });
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y');
    }

}
