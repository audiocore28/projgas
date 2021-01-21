<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Client;
use App\Models\DeliveryDetail;
use Carbon\Carbon;

class Statement extends Model
{
    use HasFactory;
    use SoftDeletes;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'client_id', 'payment', 'check_no', 'soa_no'];

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
                $query->where('soa_no', 'like', '%'.$search.'%');
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
