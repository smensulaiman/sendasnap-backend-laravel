<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('make');
            $table->string('model');
            $table->string('chassis_model');
            $table->integer('cc');
            $table->year('year');
            $table->string('color');
            $table->date('vehicle_buy_date');
            $table->string('auction_ship_number');
            $table->decimal('net_weight', 8, 2);
            $table->string('area');
            $table->decimal('length', 8, 2);
            $table->decimal('width', 8, 2);
            $table->decimal('height', 8, 2);
            $table->string('plate_number')->nullable();
            $table->decimal('buying_price', 10, 2);
            $table->date('expected_yard_date');
            $table->string('rikso_from')->nullable();
            $table->string('rikso_to')->nullable();
            $table->decimal('rikso_cost', 8, 2)->nullable();
            $table->string('rikso_company')->nullable();
            $table->string('auction_sheet')->nullable();
            $table->string('tohon_copy')->nullable();
            $table->enum('status', ['pending', 'in_yard', 'ready', 'sold'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};