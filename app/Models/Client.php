<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BatangasTransactionDetail;
use App\Models\MindoroTransactionDetail;
use App\Models\BatangasPaymentDetail;
use App\Models\MindoroPaymentDetail;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
	protected $fillable = ['name', 'office', 'contact_person', 'email_address', 'contact_no'];


     public function batangasTransactionDetails()
     {
        return $this->hasMany(BatangasTransactionDetail::class);
     }

     public function mindoroTransactionDetails()
     {
        return $this->hasMany(MindoroTransactionDetail::class);
     }

     public function batangasPaymentDetails()
     {
        return $this->hasManyThrough(BatangasPaymentDetail::class, BatangasTransactionDetail::class);
     }

     public function mindoroPaymentDetails()
     {
        return $this->hasManyThrough(MindoroPaymentDetail::class, MindoroTransactionDetail::class);
     }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone', 'Asia/Manila'))->format('M d, Y');
    }
}
