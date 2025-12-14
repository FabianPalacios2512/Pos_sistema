<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero modificar el enum para agregar 'returned' y mantener 'quotation'
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled', 'quotation', 'returned') DEFAULT 'draft'");

        // Agregar campo para número de devolución de referencia
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'return_reference')) {
                $table->string('return_reference', 50)->nullable()->after('status')->comment('Número de devolución de referencia (RET-XXXXXX)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('return_reference');
        });

        // Revertir el enum al estado original
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft'");
    }
};
