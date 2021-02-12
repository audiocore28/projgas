<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;

class Pump extends Model
{
    use HasFactory;

	 protected $fillable = ['pump', 'product_id', 'nozzle', 'station_id'];

	public function station()
	{
		return $this->belongsTo(Station::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	// public function getNameAttribute()
	// {
	// 	return $this->pump.' - '.$this->product->name;
	// }

}
