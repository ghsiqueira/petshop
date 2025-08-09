<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Adicionar índices de busca para produtos
        Schema::table('products', function (Blueprint $table) {
            // Índice no campo name (sempre existe)
            if (!$this->indexExists('products', 'products_name_index')) {
                $table->index(['name']);
            }
            
            // Índice composto apenas se as colunas existirem
            if (Schema::hasColumn('products', 'category') && Schema::hasColumn('products', 'price')) {
                if (!$this->indexExists('products', 'products_category_price_index')) {
                    $table->index(['category', 'price']);
                }
            }
            
            // Índice para is_active se existir
            if (Schema::hasColumn('products', 'is_active')) {
                if (!$this->indexExists('products', 'products_active_created_at_index')) {
                    $table->index(['is_active', 'created_at']);
                }
            }
        });

        // Adicionar índices de busca para serviços
        Schema::table('services', function (Blueprint $table) {
            // Índice no campo name (sempre existe)
            if (!$this->indexExists('services', 'services_name_index')) {
                $table->index(['name']);
            }
            
            // Índice composto apenas se as colunas existirem
            if (Schema::hasColumn('services', 'category') && Schema::hasColumn('services', 'price')) {
                if (!$this->indexExists('services', 'services_category_price_index')) {
                    $table->index(['category', 'price']);
                }
            }
            
            // Índice para is_active se existir
            if (Schema::hasColumn('services', 'is_active')) {
                if (!$this->indexExists('services', 'services_active_created_at_index')) {
                    $table->index(['is_active', 'created_at']);
                }
            }
        });

        // Adicionar índices de busca para petshops
        Schema::table('petshops', function (Blueprint $table) {
            // Índice no campo name (sempre existe)
            if (!$this->indexExists('petshops', 'petshops_name_index')) {
                $table->index(['name']);
            }
            
            // Índice para localização se as colunas existirem
            if (Schema::hasColumn('petshops', 'city') && Schema::hasColumn('petshops', 'state')) {
                if (!$this->indexExists('petshops', 'petshops_city_state_index')) {
                    $table->index(['city', 'state']);
                }
            }
            
            // Índice para rating se existir
            if (Schema::hasColumn('petshops', 'rating')) {
                if (!$this->indexExists('petshops', 'petshops_rating_created_at_index')) {
                    $table->index(['rating', 'created_at']);
                }
            }
        });

        // Adicionar índices de busca para pets (se a tabela existir)
        if (Schema::hasTable('pets')) {
            Schema::table('pets', function (Blueprint $table) {
                if (Schema::hasColumn('pets', 'name') && Schema::hasColumn('pets', 'species')) {
                    if (!$this->indexExists('pets', 'pets_name_species_index')) {
                        $table->index(['name', 'species']);
                    }
                }
                
                if (Schema::hasColumn('pets', 'breed') && Schema::hasColumn('pets', 'gender')) {
                    if (!$this->indexExists('pets', 'pets_breed_gender_index')) {
                        $table->index(['breed', 'gender']);
                    }
                }
                
                if (Schema::hasColumn('pets', 'user_id')) {
                    if (!$this->indexExists('pets', 'pets_user_id_created_at_index')) {
                        $table->index(['user_id', 'created_at']);
                    }
                }
            });
        }

        // Adicionar índices de busca para usuários
        Schema::table('users', function (Blueprint $table) {
            // Índice no name e email (sempre existem)
            if (!$this->indexExists('users', 'users_name_email_index')) {
                $table->index(['name', 'email']);
            }
            
            // Índice para localização se as colunas existirem
            if (Schema::hasColumn('users', 'city') && Schema::hasColumn('users', 'state')) {
                if (!$this->indexExists('users', 'users_city_state_index')) {
                    $table->index(['city', 'state']);
                }
            }
        });
    }

    public function down(): void
    {
        // Remover índices de forma segura
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $this->dropIndexIfExists('products', 'products_name_index');
                $this->dropIndexIfExists('products', 'products_category_price_index');
                $this->dropIndexIfExists('products', 'products_active_created_at_index');
            });
        }

        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                $this->dropIndexIfExists('services', 'services_name_index');
                $this->dropIndexIfExists('services', 'services_category_price_index');
                $this->dropIndexIfExists('services', 'services_active_created_at_index');
            });
        }

        if (Schema::hasTable('petshops')) {
            Schema::table('petshops', function (Blueprint $table) {
                $this->dropIndexIfExists('petshops', 'petshops_name_index');
                $this->dropIndexIfExists('petshops', 'petshops_city_state_index');
                $this->dropIndexIfExists('petshops', 'petshops_rating_created_at_index');
            });
        }

        if (Schema::hasTable('pets')) {
            Schema::table('pets', function (Blueprint $table) {
                $this->dropIndexIfExists('pets', 'pets_name_species_index');
                $this->dropIndexIfExists('pets', 'pets_breed_gender_index');
                $this->dropIndexIfExists('pets', 'pets_user_id_created_at_index');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $this->dropIndexIfExists('users', 'users_name_email_index');
                $this->dropIndexIfExists('users', 'users_city_state_index');
            });
        }
    }

    /**
     * Verificar se um índice existe
     */
    private function indexExists($table, $indexName)
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table}");
            foreach ($indexes as $index) {
                if ($index->Key_name === $indexName) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remover índice se existir
     */
    private function dropIndexIfExists($tableName, $indexName)
    {
        try {
            if ($this->indexExists($tableName, $indexName)) {
                DB::statement("ALTER TABLE {$tableName} DROP INDEX {$indexName}");
            }
        } catch (\Exception $e) {
            // Ignorar erros ao remover índices
        }
    }
};