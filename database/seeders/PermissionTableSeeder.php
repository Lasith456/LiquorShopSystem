<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
           'product-list',
           'product-create',
           'product-edit',
           'product-delete',
           'category-list',
           'category-create',
           'category-edit',
           'category-delete',
           'size-list',
           'size-create',
           'size-edit',
           'size-delete',
           'stock-list',
           'stock-create',
           'stock-delete',
           'sell-list',
            'sell-create',
            'sell-delete',
        ];

        foreach ($permissions as $permission) {
             Permission::firstOrCreate(['name' => $permission]);
        }
    }
}

//php artisan db:seed --class=PermissionTableSeeder