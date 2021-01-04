<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	 protected $fillable = ['name', 'contact_no', 'office', 'contact_person', 'email_address'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('office', 'like', '%'.$search.'%');
            });
        });
    }

	 public function purchases()
	 {
       return $this->hasMany(Purchase::class);
	 }
}
