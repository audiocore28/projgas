<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BatangasTransaction;
use App\Models\Purchase;

class MonthlyBatangasTransaction extends Model
{
    use HasFactory;

	 protected $fillable = ['year', 'month'];

	 public function batangasTransactions()
	 {
	 	return $this->hasMany(BatangasTransaction::class)->orderByRaw('LENGTH(trip_no) ASC')->orderBy('trip_no', 'ASC');
	 }

     public function purchases()
     {
        return $this->hasMany(Purchase::class);
     }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('year', 'like', '%'.$search.'%')
                    ->orWhere('month', 'like', '%'.$search.'%');
            });
        });
    }

}
