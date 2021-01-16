<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Delivery;
use App\Models\DeliveryDetail;

class Client extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	 protected $fillable = ['name', 'office', 'contact_person', 'email_address', 'contact_no'];


     public function deliveryDetails()
     {
        return $this->hasMany(DeliveryDetail::class);
     }

     public function deliveries()
     {
        return $this->hasMany(Delivery::class);
     }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('contact_person', 'like', '%'.$search.'%')
                    ->orWhere('office', 'like', '%'.$search.'%');
            });
        });
    }

}
