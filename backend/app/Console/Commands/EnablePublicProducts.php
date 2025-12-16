<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class EnablePublicProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:enable-public {--all : Enable all products} {--with-stock : Only products with stock}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable products for public catalog (set is_public = true)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking products status...');

        // Estadísticas actuales
        $total = Product::count();
        $active = Product::where('active', true)->count();
        $public = Product::where('is_public', true)->count();
        $withStock = Product::where('current_stock', '>', 0)->count();
        $availableOnline = Product::where('is_public', true)
            ->where('active', true)
            ->where('current_stock', '>', 0)
            ->count();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Products', $total],
                ['Active Products', $active],
                ['Public Products (is_public=true)', $public],
                ['Products with Stock > 0', $withStock],
                ['Available Online', $availableOnline],
            ]
        );

        if ($availableOnline > 0) {
            $this->info("✅ You already have {$availableOnline} products available online!");
        }

        if (!$this->option('all') && !$this->option('with-stock')) {
            $this->warn('⚠️  Use --all to enable all active products or --with-stock to enable only products with stock');
            return 0;
        }

        // Construir query
        $query = Product::where('active', true);

        if ($this->option('with-stock')) {
            $query->where('current_stock', '>', 0);
        }

        $count = $query->count();

        if ($count === 0) {
            $this->warn('⚠️  No products found matching criteria');
            return 0;
        }

        $this->info("📦 Found {$count} products to enable");

        if ($this->confirm('Do you want to enable these products for public catalog?', true)) {
            $updated = $query->update(['is_public' => true]);

            $this->info("✅ Successfully enabled {$updated} products!");

            // Verificar nuevamente
            $newAvailable = Product::where('is_public', true)
                ->where('active', true)
                ->where('current_stock', '>', 0)
                ->count();

            $this->info("🛒 Products now available online: {$newAvailable}");

            if ($newAvailable === 0) {
                $this->warn('⚠️  No products available online yet. Make sure they have stock > 0');
            }
        } else {
            $this->info('❌ Operation cancelled');
        }

        return 0;
    }
}
