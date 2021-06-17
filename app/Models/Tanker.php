<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TankerLoad;
use App\Models\BatangasTransaction;
use App\Models\MindoroTransaction;
use App\Models\Driver;
use App\Models\Helper;

class Tanker extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
	protected $fillable = ['plate_no', 'compartment'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('plate_no', 'like', '%'.$search.'%')
                    ->orWhere('compartment', 'like', '%'.$search.'%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

     public function tankerLoads()
     {
        return $this->hasMany(TankerLoad::class);
     }

     public function batangasTransactions()
     {
        return $this->hasMany(BatangasTransaction::class);
     }

     public function mindoroTransactions()
     {
        return $this->hasMany(MindoroTransaction::class);
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
