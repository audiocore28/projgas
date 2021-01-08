<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Purchase;
use App\Models\Tanker;
use App\Models\Driver;
use App\Models\Helper;
use App\Models\TankerLoadDetail;
use Carbon\Carbon;

class TankerLoad extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'status', 'remarks', 'purchase_id', 'tanker_id', 'driver_id', 'helper_id'];


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

	 public function tankerLoadDetails()
	 {
	 	return $this->hasMany(TankerLoadDetail::class);
	 }


    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->WhereHas('purchase', function ($query) use ($search) {
	                        $query->where('purchase_no', 'like', '%'.$search.'%');
	                    })
                		->orWhereHas('tanker', function ($query) use ($search) {
	                        $query->where('plate_no', 'like', '%'.$search.'%');
	                    })
                		->orWhereHas('driver', function ($query) use ($search) {
	                        $query->where('name', 'like', '%'.$search.'%');
	                    })
                		->orWhereHas('helper', function ($query) use ($search) {
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
