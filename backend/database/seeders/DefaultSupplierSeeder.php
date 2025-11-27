<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class DefaultSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Supplier::count() === 0) {
            Supplier::create([
                'name' => 'Proveedor General',
                'contact_name' => 'Contacto General',
                'email' => 'general@proveedor.local',
                'phone' => '000-000-0000',
                'address' => 'Dirección General',
                'active' => true
            ]);
        }
    }
}
