<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bebidas',
                'description' => 'Refrescos, jugos, agua y bebidas alcohólicas',
                'icon' => 'cup',
                'color' => '#3b82f6',
                'active' => true
            ],
            [
                'name' => 'Snacks',
                'description' => 'Papas, galletas, dulces y bocadillos',
                'icon' => 'cookie',
                'color' => '#f59e0b',
                'active' => true
            ],
            [
                'name' => 'Lácteos',
                'description' => 'Leche, queso, yogurt y derivados',
                'icon' => 'milk',
                'color' => '#10b981',
                'active' => true
            ],
            [
                'name' => 'Carnes',
                'description' => 'Carnes rojas, pollo, cerdo y embutidos',
                'icon' => 'meat',
                'color' => '#ef4444',
                'active' => true
            ],
            [
                'name' => 'Frutas y Verduras',
                'description' => 'Frutas frescas, verduras y hortalizas',
                'icon' => 'apple',
                'color' => '#22c55e',
                'active' => true
            ],
            [
                'name' => 'Panadería',
                'description' => 'Pan, pasteles, tortas y productos de panadería',
                'icon' => 'bread',
                'color' => '#d97706',
                'active' => true
            ],
            [
                'name' => 'Aseo Personal',
                'description' => 'Jabones, champú, cremas y productos de higiene',
                'icon' => 'soap',
                'color' => '#8b5cf6',
                'active' => true
            ],
            [
                'name' => 'Limpieza',
                'description' => 'Detergentes, desinfectantes y productos de limpieza',
                'icon' => 'spray',
                'color' => '#06b6d4',
                'active' => true
            ],
            [
                'name' => 'Electrónicos',
                'description' => 'Celulares, audífonos, cables y accesorios',
                'icon' => 'smartphone',
                'color' => '#6366f1',
                'active' => true
            ],
            [
                'name' => 'Hogar',
                'description' => 'Utensilios de cocina, decoración y artículos para el hogar',
                'icon' => 'home',
                'color' => '#84cc16',
                'active' => true
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
    }
}
