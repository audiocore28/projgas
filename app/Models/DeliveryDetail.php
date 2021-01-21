<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\Client;
use Carbon\Carbon;

class DeliveryDetail extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'dr_no', 'quantity', 'unit_price', 'delivery_id', 'product_id', 'client_id'];

	 public function delivery()
	 {
	 	return $this->belongsTo(Delivery::class);
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
