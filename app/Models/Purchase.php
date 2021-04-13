<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Supplier;
use App\Models\PurchaseDetail;
use App\Models\MonthlyMindoroTransaction;
use App\Models\MindoroTransaction;
use App\Models\TankerLoad;
use Carbon\Carbon;

class Purchase extends Model
{
    use HasFactory;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'supplier_id', 'purchase_no', 'monthly_mindoro_transaction_id'];

	 public function supplier()
	 {
	 	return $this->belongsTo(Supplier::class);
	 }

     public function monthlyMindoroTransaction()
     {
        return $this->belongsTo(MonthlyMindoroTransaction::class);
     }

	 public function purchaseDetails()
	 {
	 	return $this->hasMany(PurchaseDetail::class);
	 }

	 public function tankerLoads()
	 {
	 	return $this->hasMany(TankerLoad::class);
	 }

     public function batangasTransactions()
     {
        return $this->belongsToMany(BatangasTransaction::class);
     }

     public function mindoroTransactions()
     {
        return $this->belongsToMany(MindoroTransaction::class);
     }

     public function deliveries()
     {
        return $this->hasMany(Delivery::class);
     }


    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('purchase_no', 'like', '%'.$search.'%')
                		->orWhereHas('supplier', function ($query) use ($search) {
	                        $query->where('name', 'like', '%'.$search.'%');
	                    });
            });
        });
    }

    // public function setDateAttribute($value)
    // {
    //     $this->attributes['date'] = Carbon::createFromFormat('MM-DD-YYYY', $value)->format('Y-m-d');
    // }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y');
    }
}
