<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;
use App\Models\PurchaseDetail;
use Carbon\Carbon;

class Purchase extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'supplier_id', 'purchase_no'];

	 public function supplier()
	 {
	 	return $this->belongsTo(Supplier::class);
	 }

	 public function purchaseDetails()
	 {
	 	return $this->hasMany(PurchaseDetail::class);
	 }

	 public function loads()
	 {
	 	return $this->hasMany(Load::class);
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
