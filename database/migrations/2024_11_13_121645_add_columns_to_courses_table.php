<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCoursesTable extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('price');
            $table->date('start_date')->nullable()->after('is_active');
            $table->date('end_date')->nullable()->after('start_date');
            $table->integer('remaining_slots')->default(0)->after('end_date');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'start_date',
                'end_date',
                'remaining_slots'
            ]);
        });
    }
}
