<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'index_users',
            'delete_user',
            'index_products',
            'view_product_details',
            'create_product',
            'delete_product',
            'index_categories',
            'create_category',
            'delete_category',
            'index_vendors',
            'delete_vendor',
            'assign_role',
            'create_order',
            'index_orders',
            'view_order_details',
            'update_order',
            'create_review',
            'update_review',    
            'delete_review',
            'add_to_cart',
            'remove_from_cart',
        ];
    }
}
