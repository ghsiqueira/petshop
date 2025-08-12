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
        Schema::table('users', function (Blueprint $table) {
            // Verificar se as colunas já existem antes de adicionar
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture')->nullable();
            }
            
            // Adicionar novas colunas de perfil
            $table->text('bio')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            
            // Endereço completo
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            
            // Redes sociais
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();
            
            // Configurações
            $table->enum('preferred_theme', ['light', 'dark', 'auto'])->default('light');
            $table->string('preferred_language', 10)->default('pt-BR');
            
            // Notificações
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('marketing_emails')->default(false);
            
            // Configurações específicas (JSON)
            $table->json('profile_settings')->nullable();
            
            // Atividade
            $table->timestamp('last_activity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'birth_date',
                'gender',
                'city',
                'state',
                'zip_code',
                'country',
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'linkedin_url',
                'preferred_theme',
                'preferred_language',
                'email_notifications',
                'sms_notifications',
                'marketing_emails',
                'profile_settings',
                'last_activity'
            ]);
        });
    }
};