<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DeliveryDetail;
use App\Models\Purchase;
use App\Models\Tanker;
use App\Models\Driver;
use App\Models\Helper;
use Carbon\Carbon;

class Delivery extends Model
{
    use HasFactory;
    use SoftDeletes;

	 public $table = 'deliveries';
    protected $dates = ['deleted_at'];
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

	 public function deliveryDetails()
	 {
	 	return $this->hasMany(DeliveryDetail::class);
	 }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->WhereHas('client', function ($query) use ($search) {
	                        $query->where('name', 'like', '%'.$search.'%');
	                    });
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y');
    }

}
