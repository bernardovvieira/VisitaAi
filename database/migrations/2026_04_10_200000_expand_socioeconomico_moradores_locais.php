<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moradores', function (Blueprint $table) {
            if (! Schema::hasColumn('moradores', 'mor_sexo')) {
                $table->string('mor_sexo', 25)->nullable()->after('mor_situacao_trabalho');
            }
            if (! Schema::hasColumn('moradores', 'mor_estado_civil')) {
                $table->string('mor_estado_civil', 35)->nullable()->after('mor_sexo');
            }
            if (! Schema::hasColumn('moradores', 'mor_naturalidade')) {
                $table->string('mor_naturalidade', 150)->nullable()->after('mor_estado_civil');
            }
            if (! Schema::hasColumn('moradores', 'mor_profissao')) {
                $table->string('mor_profissao', 150)->nullable()->after('mor_naturalidade');
            }
            if (! Schema::hasColumn('moradores', 'mor_parentesco')) {
                $table->string('mor_parentesco', 45)->nullable()->after('mor_profissao');
            }
            if (! Schema::hasColumn('moradores', 'mor_referencia_familiar')) {
                $table->boolean('mor_referencia_familiar')->default(false)->after('mor_parentesco');
            }
            if (! Schema::hasColumn('moradores', 'mor_telefone')) {
                $table->string('mor_telefone', 40)->nullable()->after('mor_referencia_familiar');
            }
            if (! Schema::hasColumn('moradores', 'mor_rg_numero')) {
                $table->string('mor_rg_numero', 45)->nullable()->after('mor_telefone');
            }
            if (! Schema::hasColumn('moradores', 'mor_rg_orgao')) {
                $table->string('mor_rg_orgao', 60)->nullable()->after('mor_rg_numero');
            }
            if (! Schema::hasColumn('moradores', 'mor_cpf')) {
                $table->string('mor_cpf', 20)->nullable()->after('mor_rg_orgao');
            }
            if (! Schema::hasColumn('moradores', 'mor_tempo_uniao_conjuge')) {
                $table->string('mor_tempo_uniao_conjuge', 120)->nullable()->after('mor_cpf');
            }
            if (! Schema::hasColumn('moradores', 'mor_ajuda_compra_imovel')) {
                $table->string('mor_ajuda_compra_imovel', 255)->nullable()->after('mor_tempo_uniao_conjuge');
            }
            if (! Schema::hasColumn('moradores', 'mor_renda_formal_informal')) {
                $table->string('mor_renda_formal_informal', 25)->nullable()->after('mor_ajuda_compra_imovel');
            }
        });

        if (! Schema::hasTable('locais_socioeconomico')) {
            Schema::create('locais_socioeconomico', function (Blueprint $table) {
                $table->bigIncrements('lse_id');
                $table->unsignedBigInteger('fk_local_id');
                $table->date('lse_data_entrevista')->nullable();
                $table->string('lse_condicao_casa', 40)->nullable();
                $table->string('lse_posicao_entrevistado', 45)->nullable();
                $table->string('lse_telefone_contato', 45)->nullable();
                $table->unsignedSmallInteger('lse_n_moradores_declarado')->nullable();
                $table->string('lse_renda_formal_informal', 25)->nullable();
                $table->text('lse_principal_fonte_renda')->nullable();
                $table->string('lse_renda_familiar_faixa', 50)->nullable();
                $table->unsignedSmallInteger('lse_qtd_contribuintes')->nullable();
                $table->text('lse_beneficios_sociais')->nullable();
                $table->string('lse_gastos_mensais_faixa', 50)->nullable();
                $table->string('lse_proprietario_nome', 255)->nullable();
                $table->string('lse_proprietario_endereco', 255)->nullable();
                $table->string('lse_proprietario_telefone', 45)->nullable();
                $table->string('lse_uso_imovel', 35)->nullable();
                $table->string('lse_situacao_posse', 45)->nullable();
                $table->string('lse_material_predominante', 35)->nullable();
                $table->string('lse_condicao_edificacao', 35)->nullable();
                $table->unsignedSmallInteger('lse_num_comodos')->nullable();
                $table->unsignedSmallInteger('lse_num_quartos')->nullable();
                $table->unsignedSmallInteger('lse_num_banheiros')->nullable();
                $table->string('lse_area_externa', 20)->nullable();
                $table->string('lse_viz_frente', 180)->nullable();
                $table->string('lse_viz_fundos', 180)->nullable();
                $table->string('lse_viz_direita', 180)->nullable();
                $table->string('lse_viz_esquerda', 180)->nullable();
                $table->string('lse_tipologia', 35)->nullable();
                $table->string('lse_tipo_implantacao', 35)->nullable();
                $table->string('lse_posicao_lote', 25)->nullable();
                $table->unsignedSmallInteger('lse_num_pavimentos')->nullable();
                $table->unsignedSmallInteger('lse_banheiro_dentro')->nullable();
                $table->unsignedSmallInteger('lse_banheiro_fora')->nullable();
                $table->boolean('lse_banheiro_compartilha')->nullable();
                $table->string('lse_acesso_imovel', 45)->nullable();
                $table->string('lse_entrada_para', 45)->nullable();
                $table->string('lse_area_livre', 255)->nullable();
                $table->text('lse_observacoes_imovel')->nullable();
                $table->string('lse_abastecimento_agua', 45)->nullable();
                $table->string('lse_energia_eletrica', 45)->nullable();
                $table->string('lse_esgoto', 45)->nullable();
                $table->string('lse_coleta_lixo', 45)->nullable();
                $table->string('lse_pavimentacao', 45)->nullable();
                $table->string('lse_situacao_terreno', 45)->nullable();
                $table->string('lse_posse_area', 45)->nullable();
                $table->string('lse_tempo_residencia_texto', 150)->nullable();
                $table->date('lse_data_ocupacao')->nullable();
                $table->text('lse_forma_aquisicao')->nullable();
                $table->string('lse_houve_compra_venda', 15)->nullable();
                $table->string('lse_escritura', 45)->nullable();
                $table->text('lse_situacao_legal_obs')->nullable();
                $table->string('lse_proprietario_anterior_nome', 255)->nullable();
                $table->string('lse_proprietario_anterior_doc', 30)->nullable();
                $table->text('lse_como_ocupou')->nullable();
                $table->string('lse_contrato_promessa', 15)->nullable();
                $table->string('lse_documento_quitado', 15)->nullable();
                $table->string('lse_sabe_local_vendedor', 15)->nullable();
                $table->string('lse_paga_iptu', 15)->nullable();
                $table->string('lse_iptu_desde', 60)->nullable();
                $table->string('lse_local_data_assinatura', 255)->nullable();
                $table->timestamps();

                $table->foreign('fk_local_id')->references('loc_id')->on('locais')->onDelete('cascade');
                $table->unique('fk_local_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('locais_socioeconomico');

        Schema::table('moradores', function (Blueprint $table) {
            foreach ([
                'mor_renda_formal_informal',
                'mor_ajuda_compra_imovel',
                'mor_tempo_uniao_conjuge',
                'mor_cpf',
                'mor_rg_orgao',
                'mor_rg_numero',
                'mor_telefone',
                'mor_referencia_familiar',
                'mor_parentesco',
                'mor_profissao',
                'mor_naturalidade',
                'mor_estado_civil',
                'mor_sexo',
            ] as $col) {
                if (Schema::hasColumn('moradores', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
