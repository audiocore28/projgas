<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Station extends Model
{
	use HasFactory;

	protected $fillable = ['name', 'contact_no', 'address'];

	public function scopeFilter($query, array $filters)
	{
		$query->when($filters['search'] ?? null, function ($query, $search) {
			$query->where(function ($query) use ($search) {
				$query->where('name', 'like', '%'.$search.'%')
				->orWhere('address', 'like', '%'.$search.'%')
				->orWhere('contact_no', 'like', '%'.$search.'%');
			});
		});
	}

	public function pumps()
	{
		return $this->hasMany(Pump::class);
	}

}
