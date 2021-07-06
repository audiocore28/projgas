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
use App\Models\ToBatangasLoad;
use App\Models\ToMindoroLoad;
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

     public function toBatangasLoads()
     {
        return $this->hasMany(ToBatangasLoad::class)->orderBy('batangas_transaction_id');
     }

     public function toMindoroLoads()
     {
        return $this->hasMany(ToMindoroLoad::class)->orderBy('mindoro_transaction_id');
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
