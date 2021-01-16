<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\TankerLoad;
use App\Models\Product;

class TankerLoadDetail extends Model
{
    use HasFactory;
  //   use SoftDeletes;

	 protected $fillable = ['quantity', 'tanker_load_id', 'product_id'];


	 public function tankerLoad()
	 {
	 	return $this->belongsTo(TankerLoad::class);
	 }

	 public function product()
	 {
	 	return $this->belongsTo(Product::class);
	 }

}
