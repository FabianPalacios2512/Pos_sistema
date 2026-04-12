<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tablas de seguridad centrales:
     * - login_attempts: registro de intentos de login
     * - blocked_ips: IPs bloqueadas temporalmente
     * - security_events: eventos de seguridad detectados
     */
    public function up(): void
    {
        // Intentos de login (central - aplica a todos los tenants)
        Schema::connection('mysql')->create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->index();
            $table->string('ip_address', 45)->index();
            $table->string('user_agent', 500)->nullable();
            $table->string('tenant_id', 191)->nullable()->index();
            $table->enum('result', ['success', 'fail'])->default('fail');
            $table->string('fail_reason', 100)->nullable(); // wrong_password, user_not_found, account_blocked, ip_blocked
            $table->timestamp('attempted_at')->useCurrent()->index();

            $table->index(['email', 'result', 'attempted_at']);
            $table->index(['ip_address', 'attempted_at']);
        });

        // IPs bloqueadas
        Schema::connection('mysql')->create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->string('reason', 255);
            $table->unsignedInteger('attempt_count')->default(0);
            $table->timestamp('blocked_at')->useCurrent();
            $table->timestamp('expires_at')->nullable()->index();
            $table->boolean('is_permanent')->default(false);
            $table->timestamps();
        });

        // Eventos de seguridad
        Schema::connection('mysql')->create('security_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 50)->index(); // brute_force, ip_sweep, unusual_hour, geo_change, account_blocked, ip_blocked
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('email', 191)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('tenant_id', 191)->nullable();
            $table->json('details')->nullable();
            $table->string('action_taken', 100)->nullable(); // account_locked, ip_blocked, alert_raised
            $table->boolean('resolved')->default(false);
            $table->timestamp('created_at')->useCurrent()->index();
        });

        // Scores de riesgo por usuario (acumulativo)
        Schema::connection('mysql')->create('user_risk_scores', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->unique();
            $table->string('tenant_id', 191)->nullable();
            $table->unsignedInteger('risk_score')->default(0);
            $table->enum('status', ['normal', 'suspicious', 'blocked'])->default('normal');
            $table->string('block_reason', 255)->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->timestamp('last_scored_at')->nullable();
            $table->json('score_history')->nullable(); // últimos eventos que sumaron score
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('user_risk_scores');
        Schema::connection('mysql')->dropIfExists('security_events');
        Schema::connection('mysql')->dropIfExists('blocked_ips');
        Schema::connection('mysql')->dropIfExists('login_attempts');
    }
};
