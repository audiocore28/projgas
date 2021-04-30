<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BatangasTransaction;
use App\Models\Product;
use App\Models\Client;
use Carbon\Carbon;

class BatangasTransactionDetail extends Model
{
    use HasFactory;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'quantity', 'unit_price', 'batangas_transaction_id', 'product_id', 'client_id'];

	 public function batangasTransaction()
	 {
	 	return $this->belongsTo(BatangasTransaction::class);
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
