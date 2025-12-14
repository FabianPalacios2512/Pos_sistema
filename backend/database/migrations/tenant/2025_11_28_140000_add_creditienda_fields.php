<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Configuración Global (system_settings)
        if (Schema::hasTable('system_settings')) {
            Schema::table('system_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('system_settings', 'enable_credit_system')) {
                    $table->boolean('enable_credit_system')->default(false)->after('low_stock_threshold');
                }
                if (!Schema::hasColumn('system_settings', 'credit_surcharge_percentage')) {
                    $table->decimal('credit_surcharge_percentage', 5, 2)->default(0)->after('enable_credit_system');
                }
            });
        }

        // 2. Clientes (customers)
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'credit_active')) {
                    $table->boolean('credit_active')->default(false)->after('active');
                }
            });
        }

        // 3. Ventas (sales)
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'surcharge_amount')) {
                    $table->decimal('surcharge_amount', 12, 2)->default(0)->after('total_amount');
                }
                if (!Schema::hasColumn('sales', 'payment_status')) {
                    $table->enum('payment_status', ['paid', 'pending', 'partial'])->default('paid')->after('status');
                }
            });
        }

        // 4. Tabla de Abonos (credit_payments)
        if (!Schema::hasTable('credit_payments')) {
            Schema::create('credit_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained()->onDelete('cascade');
                $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('user_id')->constrained();
                $table->decimal('amount', 12, 2);
                $table->enum('method', ['cash', 'transfer', 'card'])->default('cash');
                $table->text('notes')->nullable();
                $table->timestamp('payment_date')->useCurrent();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_payments');

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['surcharge_amount', 'payment_status']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['credit_active']);
        });

        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['enable_credit_system', 'credit_surcharge_percentage']);
        });
    }
};
