<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table = 'settings';

    public function up(): void
    {
        if (!Schema::hasTable($this->table))
        {
            Schema::create($this->table, function (Blueprint $table) {
                $table->string('key')->unique()->primary()->index();
                $table->string('type');
                $table->string('string')->nullable();
                $table->boolean('boolean')->nullable();
                $table->unsignedBigInteger('integer')->nullable();
                $table->json('json')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
