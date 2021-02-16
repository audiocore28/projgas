<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

	public $table = 'companies';
	protected $dates = ['deleted_at'];
	protected $fillable = ['name', 'contact_no', 'address', 'email_address'];

	public function scopeFilter($query, array $filters)
	{
		$query->when($filters['search'] ?? null, function ($query, $search) {
			$query->where(function ($query) use ($search) {
				$query->where('name', 'like', '%'.$search.'%')
				->orWhere('address', 'like', '%'.$search.'%')
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



}
