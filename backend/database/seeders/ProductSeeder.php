<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Bebidas
            [
                'name' => 'Coca Cola 350ml',
                'description' => 'Refresco de cola en lata de 350ml',
                'sku' => 'BEB001',
                'barcode' => '7702129110011',
                'category_id' => 1,
                'supplier_id' => 4,
                'cost_price' => 1800.00,
                'sale_price' => 2500.00,
                'wholesale_price' => 2200.00,
                'current_stock' => 120,
                'min_stock' => 20,
                'max_stock' => 300,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['refresco', 'cola', 'lata']
            ],
            [
                'name' => 'Agua Cristal 600ml',
                'description' => 'Agua purificada en botella de 600ml',
                'sku' => 'BEB002',
                'barcode' => '7702129220022',
                'category_id' => 1,
                'supplier_id' => 4,
                'cost_price' => 800.00,
                'sale_price' => 1200.00,
                'wholesale_price' => 1000.00,
                'current_stock' => 200,
                'min_stock' => 50,
                'max_stock' => 500,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['agua', 'botella']
            ],
            [
                'name' => 'Jugo Hit Mango 200ml',
                'description' => 'Jugo de mango en caja de 200ml',
                'sku' => 'BEB003',
                'barcode' => '7702129330033',
                'category_id' => 1,
                'supplier_id' => 4,
                'cost_price' => 1200.00,
                'sale_price' => 1800.00,
                'wholesale_price' => 1500.00,
                'current_stock' => 80,
                'min_stock' => 15,
                'max_stock' => 200,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['jugo', 'mango', 'caja']
            ],

            // Snacks
            [
                'name' => 'Papas Margarita 150g',
                'description' => 'Papas fritas sabor natural de 150g',
                'sku' => 'SNK001',
                'barcode' => '7702129440044',
                'category_id' => 2,
                'supplier_id' => 1,
                'cost_price' => 2800.00,
                'sale_price' => 4200.00,
                'wholesale_price' => 3500.00,
                'current_stock' => 60,
                'min_stock' => 10,
                'max_stock' => 150,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['papas', 'frito', 'snack']
            ],
            [
                'name' => 'Galletas Oreo 154g',
                'description' => 'Galletas de chocolate con crema',
                'sku' => 'SNK002',
                'barcode' => '7702129550055',
                'category_id' => 2,
                'supplier_id' => 1,
                'cost_price' => 3500.00,
                'sale_price' => 5200.00,
                'wholesale_price' => 4500.00,
                'current_stock' => 45,
                'min_stock' => 8,
                'max_stock' => 100,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['galletas', 'chocolate', 'oreo']
            ],

            // Lácteos
            [
                'name' => 'Leche Alpina Deslactosada 1L',
                'description' => 'Leche deslactosada entera en tetra pak',
                'sku' => 'LAC001',
                'barcode' => '7702129660066',
                'category_id' => 3,
                'supplier_id' => 5,
                'cost_price' => 3200.00,
                'sale_price' => 4800.00,
                'wholesale_price' => 4200.00,
                'current_stock' => 75,
                'min_stock' => 15,
                'max_stock' => 180,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['leche', 'deslactosada', 'alpina']
            ],
            [
                'name' => 'Queso Campesino 500g',
                'description' => 'Queso campesino fresco de 500g',
                'sku' => 'LAC002',
                'barcode' => '7702129770077',
                'category_id' => 3,
                'supplier_id' => 5,
                'cost_price' => 8500.00,
                'sale_price' => 12000.00,
                'wholesale_price' => 10500.00,
                'current_stock' => 25,
                'min_stock' => 5,
                'max_stock' => 50,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['queso', 'campesino', 'fresco']
            ],

            // Carnes
            [
                'name' => 'Pollo Entero por Kg',
                'description' => 'Pollo entero fresco vendido por kilogramo',
                'sku' => 'CAR001',
                'barcode' => '7702129880088',
                'category_id' => 4,
                'supplier_id' => 3,
                'cost_price' => 6800.00,
                'sale_price' => 9500.00,
                'wholesale_price' => 8500.00,
                'current_stock' => 50,
                'min_stock' => 10,
                'max_stock' => 100,
                'unit' => 'kg',
                'active' => true,
                'tags' => ['pollo', 'entero', 'fresco']
            ],
            [
                'name' => 'Salchicha Ranchera 500g',
                'description' => 'Salchicha ranchera empacada al vacío',
                'sku' => 'CAR002',
                'barcode' => '7702129990099',
                'category_id' => 4,
                'supplier_id' => 3,
                'cost_price' => 4200.00,
                'sale_price' => 6800.00,
                'wholesale_price' => 5800.00,
                'current_stock' => 35,
                'min_stock' => 8,
                'max_stock' => 80,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['salchicha', 'ranchera', 'empacada']
            ],

            // Frutas y Verduras
            [
                'name' => 'Banano por Kg',
                'description' => 'Banano maduro vendido por kilogramo',
                'sku' => 'FRU001',
                'barcode' => '7702130000100',
                'category_id' => 5,
                'supplier_id' => 6,
                'cost_price' => 2200.00,
                'sale_price' => 3500.00,
                'wholesale_price' => 3000.00,
                'current_stock' => 80,
                'min_stock' => 20,
                'max_stock' => 200,
                'unit' => 'kg',
                'active' => true,
                'tags' => ['banano', 'fruta', 'maduro']
            ],
            [
                'name' => 'Tomate por Kg',
                'description' => 'Tomate chonto fresco por kilogramo',
                'sku' => 'VER001',
                'barcode' => '7702130111211',
                'category_id' => 5,
                'supplier_id' => 6,
                'cost_price' => 2800.00,
                'sale_price' => 4200.00,
                'wholesale_price' => 3600.00,
                'current_stock' => 45,
                'min_stock' => 10,
                'max_stock' => 100,
                'unit' => 'kg',
                'active' => true,
                'tags' => ['tomate', 'verdura', 'chonto']
            ],

            // Panadería
            [
                'name' => 'Pan Tajado Bimbo 550g',
                'description' => 'Pan de molde tajado integral',
                'sku' => 'PAN001',
                'barcode' => '7702130222322',
                'category_id' => 6,
                'supplier_id' => 2,
                'cost_price' => 3800.00,
                'sale_price' => 5500.00,
                'wholesale_price' => 4800.00,
                'current_stock' => 30,
                'min_stock' => 5,
                'max_stock' => 60,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['pan', 'tajado', 'bimbo']
            ],

            // Aseo Personal
            [
                'name' => 'Jabón Protex Antibacterial 110g',
                'description' => 'Jabón en barra antibacterial',
                'sku' => 'ASE001',
                'barcode' => '7702130333433',
                'category_id' => 7,
                'supplier_id' => 1,
                'cost_price' => 2100.00,
                'sale_price' => 3200.00,
                'wholesale_price' => 2800.00,
                'current_stock' => 90,
                'min_stock' => 20,
                'max_stock' => 200,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['jabon', 'antibacterial', 'protex']
            ],

            // Limpieza
            [
                'name' => 'Detergente Fab 500g',
                'description' => 'Detergente en polvo para ropa',
                'sku' => 'LIM001',
                'barcode' => '7702130444544',
                'category_id' => 8,
                'supplier_id' => 1,
                'cost_price' => 4500.00,
                'sale_price' => 6800.00,
                'wholesale_price' => 5900.00,
                'current_stock' => 55,
                'min_stock' => 10,
                'max_stock' => 120,
                'unit' => 'unidad',
                'active' => true,
                'tags' => ['detergente', 'polvo', 'fab']
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
