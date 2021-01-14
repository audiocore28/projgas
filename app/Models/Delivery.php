<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Driver;
use App\Models\Tanker;
use App\Models\DeliveryDetail;
use App\Models\Purchase;
use App\Models\Client;
use Carbon\Carbon;

class Delivery extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	 public $table = 'deliveries';
	 protected $dates = ['date'];
	 protected $fillable = ['date', 'purchase_id', 'tanker_load_id', 'client_id'];

	 public function purchase()
	 {
	 	return $this->belongsTo(Purchase::class);
	 }

	 public function tankerLoad()
	 {
	 	return $this->belongsTo(TankerLoad::class);
	 }

	 public function client()
	 {
	 	return $this->belongsTo(Client::class);
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
        });
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y');
    }

}
