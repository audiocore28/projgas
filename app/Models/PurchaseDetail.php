<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Purchase;
use App\Models\Product;

class PurchaseDetail extends Model
{
    use HasFactory;

	 protected $fillable = ['quantity', 'unit_price', 'remarks', 'purchase_id', 'product_id'];


	 public function purchase()
	 {
	 	return $this->belongsTo(Purchase::class);
	 }

	 public function product()
	 {
	 	return $this->belongsTo(Product::class);
	 }

}
