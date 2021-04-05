<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BatangasTransactionDetail;
use App\Models\MindoroTransactionDetail;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
	protected $fillable = ['name', 'office', 'contact_person', 'email_address', 'contact_no'];


     public function batangasTransactionDetails()
     {
        return $this->hasMany(BatangasTransactionDetail::class);
     }

     public function mindoroTransactionDetails()
     {
        return $this->hasMany(MindoroTransactionDetail::class);
     }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('office', 'like', '%'.$search.'%')
                    ->orWhere('contact_no', 'like', '%'.$search.'%');
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
