<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
              $table->boolean('family_friendly')->default(false)->change();
        });
    }

  
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
             $table->string('family_friendly')->change();
        });
    }
};