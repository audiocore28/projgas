<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tanker;
use App\Models\Delivery;

class Driver extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'name', 'nickname', 'address', 'license_no', 'contact_no'
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('nickname', 'like', '%'.$search.'%');
            });
        });
    }

     public function deliveries()
     {
        return $this->hasMany(Delivery::class);
     }

    public function tankers()
    {
       return $this->belongsToMany(Tanker::class);
    }
}
