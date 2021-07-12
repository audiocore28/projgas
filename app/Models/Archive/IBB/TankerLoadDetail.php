<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TankerLoad;
use App\Models\Product;

class TankerLoadDetail extends Model
{
    use HasFactory;

	 protected $fillable = ['quantity', 'tanker_load_id', 'product_id', 'unit_price'];


	 public function tankerLoad()
	 {
	 	return $this->belongsTo(TankerLoad::class);
	 }

	 public function product()
	 {
	 	return $this->belongsTo(Product::class);
	 }

}
