<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Servicios Públicos',
                'color' => '#3B82F6', // Azul
                'description' => 'Agua, luz, internet, teléfono',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nómina y Salarios',
                'color' => '#10B981', // Verde
                'description' => 'Salarios, prestaciones, pagos a empleados',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mantenimiento',
                'color' => '#F59E0B', // Naranja
                'description' => 'Reparaciones, mantenimiento de equipos e instalaciones',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Suministros y Materiales',
                'color' => '#8B5CF6', // Morado
                'description' => 'Papelería, productos de limpieza, insumos',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Otros Gastos',
                'color' => '#6B7280', // Gris
                'description' => 'Gastos diversos no categorizados',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('expense_categories')->insert($categories);
    }
}
