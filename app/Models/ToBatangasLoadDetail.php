<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ToBatangasLoad;
use App\Models\Product;

class ToBatangasLoadDetail extends Model
{
    use HasFactory;

	 protected $fillable = ['quantity', 'to_batangas_load_id', 'product_id', 'unit_price'];


	 public function toBatangasLoad()
	 {
	 	return $this->belongsTo(ToBatangasLoad::class);
	 }

	 public function product()
	 {
	 	return $this->belongsTo(Product::class);
	 }

}
