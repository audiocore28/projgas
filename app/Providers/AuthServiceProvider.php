<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Purchase' => 'App\Policies\PurchasePolicy',
        'App\Models\MonthlyBatangasTransaction' => 'App\Policies\MonthlyBatangasTransactionPolicy',
        'App\Models\MonthlyMindoroTransaction' => 'App\Policies\MonthlyMindoroTransactionPolicy',
        'App\Models\Supplier' => 'App\Policies\SupplierPolicy',
        'App\Models\Client' => 'App\Policies\ClientPolicy',
        'App\Models\ClientPayment' => 'App\Policies\ClientPaymentPolicy',
        'App\Models\Driver' => 'App\Policies\DriverPolicy',
        'App\Models\Helper' => 'App\Policies\HelperPolicy',
        'App\Models\Tanker' => 'App\Policies\TankerPolicy',
        'App\Models\Product' => 'App\Policies\ProductPolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
        'Spatie\Permission\Models\Role' => 'App\Policies\RolePolicy',
        'Spatie\Permission\Models\Permission' => 'App\Policies\PermissionPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
