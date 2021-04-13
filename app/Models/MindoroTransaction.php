<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MindoroTransactionDetail;
use App\Models\Purchase;
use Carbon\Carbon;

class MindoroTransaction extends Model
{
    use HasFactory;

     protected $dates = ['date'];
	 protected $fillable = ['date', 'trip_no', 'driver_id', 'helper_id', 'tanker_id', 'monthly_mindoro_transaction_id'];

     public function monthlyMindoroTransaction()
     {
        return $this->belongsTo(MonthlyMindoroTransaction::class);
     }

	 public function purchases()
	 {
	 	return $this->belongsToMany(Purchase::class);
	 }

     public function driver()
     {
        return $this->belongsTo(Driver::class);
     }

     public function helper()
     {
        return $this->belongsTo(Helper::class);
     }

     public function tanker()
     {
        return $this->belongsTo(Tanker::class);
     }

	 public function mindoroTransactionDetails()
	 {
	 	return $this->hasMany(MindoroTransactionDetail::class);
	 }

     public function tankerLoads()
     {
        return $this->hasMany(TankerLoad::class);
     }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->WhereHas('purchases', function ($query) use ($search) {
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
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y');
    }

}
