<?php

namespace App\Observers;

use App\Models\ProductVariant;
use App\Models\Product;

class ProductVariantObserver
{
    /**
     * Sincronizar el stock del producto padre con la suma de stocks de sus variantes
     */
    private function syncParentStock(ProductVariant $productVariant): void
    {
        if ($productVariant->product_id) {
            $totalStock = ProductVariant::where('product_id', $productVariant->product_id)
                ->sum('stock');

            Product::where('id', $productVariant->product_id)
                ->update(['current_stock' => $totalStock]);
        }
    }

    /**
     * Handle the ProductVariant "created" event.
     */
    public function created(ProductVariant $productVariant): void
    {
        $this->syncParentStock($productVariant);
    }

    /**
     * Handle the ProductVariant "updated" event.
     */
    public function updated(ProductVariant $productVariant): void
    {
        // Solo sincronizar si cambió el stock
        if ($productVariant->wasChanged('stock')) {
            $this->syncParentStock($productVariant);
        }
    }

    /**
     * Handle the ProductVariant "deleted" event.
     */
    public function deleted(ProductVariant $productVariant): void
    {
        $this->syncParentStock($productVariant);
    }

    /**
     * Handle the ProductVariant "restored" event.
     */
    public function restored(ProductVariant $productVariant): void
    {
        $this->syncParentStock($productVariant);
    }

    /**
     * Handle the ProductVariant "force deleted" event.
     */
    public function forceDeleted(ProductVariant $productVariant): void
    {
        // Para force deleted, necesitamos el product_id antes de que se borre
        if ($productVariant->product_id) {
            $totalStock = ProductVariant::where('product_id', $productVariant->product_id)
                ->where('id', '!=', $productVariant->id)
                ->sum('stock');

            Product::where('id', $productVariant->product_id)
                ->update(['current_stock' => $totalStock]);
        }
    }
}
