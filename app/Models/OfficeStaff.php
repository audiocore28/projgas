<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeStaff extends Model
{
	use HasFactory;
	use SoftDeletes;

	protected $dates = ['deleted_at'];
	protected $fillable = ['name', 'nickname', 'address', 'contact_no'];

	public function scopeFilter($query, array $filters)
	{
		$query->when($filters['search'] ?? null, function ($query, $search) {
			$query->where(function ($query) use ($search) {
				$query->where('name', 'like', '%'.$search.'%')
				->orWhere('nickname', 'like', '%'.$search.'%')
				->orWhere('address', 'like', '%'.$search.'%');
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
