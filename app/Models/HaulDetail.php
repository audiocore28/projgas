<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Haul;
use App\Models\Product;
use App\Models\Client;
use Carbon\Carbon;

class HaulDetail extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'quantity', 'unit_price', 'haul_id', 'product_id', 'client_id'];

	 public function haul()
	 {
	 	return $this->belongsTo(Haul::class);
	 }

	 public function product()
	 {
	 	return $this->belongsTo(Product::class);
	 }

	 public function client()
	 {
	 	return $this->belongsTo(Client::class);
	 }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y');
    }


}
