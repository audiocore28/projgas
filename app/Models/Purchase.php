<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Supplier;
use App\Models\Depot;
use App\Models\Account;
use App\Models\PurchaseDetail;
use App\Models\MonthlyMindoroTransaction;
use App\Models\MindoroTransaction;
use App\Models\MonthlyBatangasTransaction;
use App\Models\BatangasTransaction;
use App\Models\TankerLoad;
use Carbon\Carbon;

class Purchase extends Model
{
    use HasFactory;

	 protected $dates = ['date'];
	 protected $fillable = ['date', 'supplier_id', 'depot_id', 'account_id', 'purchase_no', 'monthly_mindoro_transaction_id', 'monthly_batangas_transaction_id'];

	 public function supplier()
	 {
	 	return $this->belongsTo(Supplier::class);
	 }

     public function depot()
     {
        return $this->belongsTo(Depot::class);
     }

     public function account()
     {
        return $this->belongsTo(Account::class);
     }

     public function monthlyMindoroTransaction()
     {
        return $this->belongsTo(MonthlyMindoroTransaction::class);
     }

     public function monthlyBatangasTransaction()
     {
        return $this->belongsTo(MonthlyBatangasTransaction::class);
     }

	 public function purchaseDetails()
	 {
	 	return $this->hasMany(PurchaseDetail::class);
	 }

	 public function tankerLoads()
	 {
	 	return $this->hasMany(TankerLoad::class);
	 }

     public function batangasLoads()
     {
        return $this->hasMany(TankerLoad::class)->where('batangas_transaction_id', '>', 0);
     }

     public function mindoroLoads()
     {
        return $this->hasMany(TankerLoad::class)->where('mindoro_transaction_id', '>', 0);
     }

     public function batangasTransactions()
     {
        return $this->belongsToMany(BatangasTransaction::class);
     }

     public function mindoroTransactions()
     {
        return $this->belongsToMany(MindoroTransaction::class);
     }

    // public function setDateAttribute($value)
    // {
    //     $this->attributes['date'] = Carbon::createFromFormat('MM-DD-YYYY', $value)->format('Y-m-d');
    // }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('M d, Y');
    }
}
