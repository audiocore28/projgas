<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MindoroTransactionDetail;
use App\Models\Purchase;
use Carbon\Carbon;

class MindoroTransaction extends Model
{
    use HasFactory;

	 protected $fillable = ['trip_no'];

	 public function purchases()
	 {
	 	return $this->belongsToMany(Purchase::class);
	 }

	 public function mindoroTransactionDetails()
	 {
	 	return $this->hasMany(MindoroTransactionDetail::class);
	 }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->WhereHas('purchase', function ($query) use ($search) {
	                        $query->where('purchase_no', 'like', '%'.$search.'%');
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
