<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ToMindoroLoad;
use App\Models\Product;

class ToMindoroLoadDetail extends Model
{
    use HasFactory;

	 protected $fillable = ['quantity', 'to_mindoro_load_id', 'product_id', 'unit_price'];


	 public function toMindoroLoad()
	 {
	 	return $this->belongsTo(ToMindoroLoad::class);
	 }

	 public function product()
	 {
	 	return $this->belongsTo(Product::class);
	 }

}
