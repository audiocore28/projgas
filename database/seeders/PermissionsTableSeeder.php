<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('permissions')->insert([
            // Purchase
            ['guard_name' => 'web', 'name' => 'view any purchase', 'group' => 'Purchases', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view purchase', 'group' => 'Purchases', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create purchase', 'group' => 'Purchases', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update purchase', 'group' => 'Purchases', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete purchase', 'group' => 'Purchases', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'print purchase', 'group' => 'Purchases', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Batangas Transaction
            ['guard_name' => 'web', 'name' => 'view any batangas transaction', 'group' => 'Batangas Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view batangas transaction', 'group' => 'Batangas Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create batangas transaction', 'group' => 'Batangas Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update batangas transaction', 'group' => 'Batangas Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete batangas transaction', 'group' => 'Batangas Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'print batangas transaction', 'group' => 'Batangas Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Mindoro Transaction
            ['guard_name' => 'web', 'name' => 'view any mindoro transaction', 'group' => 'Mindoro Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view mindoro transaction', 'group' => 'Mindoro Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create mindoro transaction', 'group' => 'Mindoro Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update mindoro transaction', 'group' => 'Mindoro Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete mindoro transaction', 'group' => 'Mindoro Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'print mindoro transaction', 'group' => 'Mindoro Transactions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Supplier
            ['guard_name' => 'web', 'name' => 'view any supplier', 'group' => 'Suppliers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view supplier', 'group' => 'Suppliers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create supplier', 'group' => 'Suppliers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update supplier', 'group' => 'Suppliers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete supplier', 'group' => 'Suppliers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore supplier', 'group' => 'Suppliers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Depot
            ['guard_name' => 'web', 'name' => 'view any depot', 'group' => 'Depots', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view depot', 'group' => 'Depots', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create depot', 'group' => 'Depots', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update depot', 'group' => 'Depots', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete depot', 'group' => 'Depots', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore depot', 'group' => 'Depots', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Account
            ['guard_name' => 'web', 'name' => 'view any account', 'group' => 'Accounts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view account', 'group' => 'Accounts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create account', 'group' => 'Accounts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update account', 'group' => 'Accounts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete account', 'group' => 'Accounts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore account', 'group' => 'Accounts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Client
            ['guard_name' => 'web', 'name' => 'view any client', 'group' => 'Clients', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view client', 'group' => 'Clients', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create client', 'group' => 'Clients', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update client', 'group' => 'Clients', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete client', 'group' => 'Clients', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore client', 'group' => 'Clients', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Client Payment
            ['guard_name' => 'web', 'name' => 'view client payment', 'group' => 'Client Payments', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update client payment', 'group' => 'Client Payments', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'verify client payment', 'group' => 'Client Payments', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Driver
            ['guard_name' => 'web', 'name' => 'view any driver', 'group' => 'Drivers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view driver', 'group' => 'Drivers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create driver', 'group' => 'Drivers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update driver', 'group' => 'Drivers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete driver', 'group' => 'Drivers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore driver', 'group' => 'Drivers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Helper
            ['guard_name' => 'web', 'name' => 'view any helper', 'group' => 'Helpers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view helper', 'group' => 'Helpers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create helper', 'group' => 'Helpers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update helper', 'group' => 'Helpers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete helper', 'group' => 'Helpers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore helper', 'group' => 'Helpers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Tanker
            ['guard_name' => 'web', 'name' => 'view any tanker', 'group' => 'Tankers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view tanker', 'group' => 'Tankers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create tanker', 'group' => 'Tankers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update tanker', 'group' => 'Tankers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete tanker', 'group' => 'Tankers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore tanker', 'group' => 'Tankers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Product
            ['guard_name' => 'web', 'name' => 'view any product', 'group' => 'Products', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view product', 'group' => 'Products', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create product', 'group' => 'Products', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update product', 'group' => 'Products', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete product', 'group' => 'Products', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore product', 'group' => 'Products', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // User
            ['guard_name' => 'web', 'name' => 'view any user', 'group' => 'Users', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view user', 'group' => 'Users', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create user', 'group' => 'Users', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update user', 'group' => 'Users', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete user', 'group' => 'Users', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore user', 'group' => 'Users', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Permission
    		['guard_name' => 'web', 'name' => 'view any permission', 'group' => 'Permissions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    		['guard_name' => 'web', 'name' => 'view permission', 'group' => 'Permissions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    		['guard_name' => 'web', 'name' => 'create permission', 'group' => 'Permissions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    		['guard_name' => 'web', 'name' => 'update permission', 'group' => 'Permissions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    		['guard_name' => 'web', 'name' => 'delete permission', 'group' => 'Permissions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore permission', 'group' => 'Permissions', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            // Role
            ['guard_name' => 'web', 'name' => 'view any role', 'group' => 'Roles', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'view role', 'group' => 'Roles', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'create role', 'group' => 'Roles', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'update role', 'group' => 'Roles', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'delete role', 'group' => 'Roles', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['guard_name' => 'web', 'name' => 'restore role', 'group' => 'Roles', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
    	]);
    }
}
