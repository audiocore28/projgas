<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MindoroTransaction;
use App\Models\Product;
use App\Models\Client;
use Carbon\Carbon;

class MindoroTransactionDetail extends Model
{
    use HasFactory;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'dr_no', 'quantity', 'unit_price', 'mindoro_transaction_id', 'product_id', 'client_id'];

	 public function mindoroTransaction()
	 {
	 	return $this->belongsTo(MindoroTransaction::class);
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
