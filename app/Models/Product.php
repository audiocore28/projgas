<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PurchaseDetail;
use App\Models\TankerLoadDetail;
use App\Models\HaulDetail;
use App\Models\DeliveryDetail;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
	protected $fillable = ['name', 'description'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function tankerLoadDetails()
    {
        return $this->hasMany(TankerLoadDetail::class);
    }

    public function haulDetails()
    {
        return $this->hasMany(HaulDetail::class);
    }

    public function deliveryDetails()
    {
        return $this->hasMany(DeliveryDetail::class);
    }
}
