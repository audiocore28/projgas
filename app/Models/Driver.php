<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tanker;

class Driver extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $dates = ['dob', 'date_hired'];

    protected $fillable = [
        'name', 'nickname', 'address', 'license_no', 'dob', 'date_hired', 'status', 'contact_no'
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

    public function tankers()
    {
       return $this->belongsToMany(Tanker::class);
    }
}
