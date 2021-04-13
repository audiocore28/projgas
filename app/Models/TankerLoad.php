<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Purchase;
use App\Models\Delivery;
use App\Models\Tanker;
use App\Models\Driver;
use App\Models\Helper;
use App\Models\TankerLoadDetail;
use Carbon\Carbon;

class TankerLoad extends Model
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
	                    })
                    ->orWhere('trip_no', 'like', '%'.$search.'%');
            });
        });
        // })->when($filters['range'] ?? null, function ($query, $range) {
        // 		$query->whereBetween('date', [$range['from'], $range['to']]);
        // });
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y');
    }
}
