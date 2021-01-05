<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Delivery;
use App\Models\Client;
use App\Models\Product;

class DeliveryDetail extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	 protected $fillable = ['quantity', 'unit_price', 'amount', 'delivery_id', 'product_id'];

	 public function delivery()
	 {
	 	return $this->belongsTo(Delivery::class);
	 }

	 public function product()
	 {
	 	return $this->belongsTo(Product::class);
	 }

}
