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
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id('id_pemasukan');
            $table->date('tanggal');
            $table->string('uraian', 255);
            $table->decimal('nominal', 12, 2);

            $table->string('foto_bukti')->nullable();

            $table->unsignedBigInteger('id_jenis_penerimaan');
            $table->unsignedBigInteger('id_user');

            $table->foreign('id_jenis_penerimaan')
                ->references('id_jenis_penerimaan')
                ->on('jenis_penerimaan');

            $table->foreign('id_user')
                ->references('id')
                ->on('users');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};
