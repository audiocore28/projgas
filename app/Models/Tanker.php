<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Tanker extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	protected $fillable = ['plate_no', 'compartment'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('plate_no', 'like', '%'.$search.'%')
                    ->orWhere('compartment', 'like', '%'.$search.'%');
            });
        });
    }

    public function drivers()
    {
       return $this->belongsToMany(Driver::class);
    }

    public function helpers()
    {
       return $this->belongsToMany(Helper::class);
    }
}
