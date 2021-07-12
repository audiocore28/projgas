<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Client;
use Carbon\Carbon;

class ClientPayment extends Model
{
    use HasFactory;
    use SoftDeletes;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'mode', 'amount', 'remarks', 'client_id'];

	 public function client()
	 {
	 	return $this->belongsTo(Client::class);
	 }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('mode', 'like', '%'.$search.'%');
                    // ->orWhere('office', 'like', '%'.$search.'%')
                    // ->orWhere('contact_no', 'like', '%'.$search.'%');
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
