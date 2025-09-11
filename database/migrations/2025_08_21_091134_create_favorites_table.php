<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();

            // المستخدم
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // morphs = بعمل عمودين:
            // favoritable_id (رقم العنصر)
            // favoritable_type (نوع العنصر: Destination أو Hotel)
            $table->morphs('favoritable');

            // حتى ما يقدر نفس اليوزر يضيف نفس الشي مرتين
            $table->unique(['user_id', 'favoritable_type', 'favoritable_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
