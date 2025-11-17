<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('btab_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('aimeos_code')->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('btab_mappings');
    }
};
