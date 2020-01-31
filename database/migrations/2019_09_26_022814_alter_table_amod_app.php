<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAmodApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('amod_app')) {
            Schema::table('amod_app', function (Blueprint $table) {
                $table->string('head_content', 4000)->default('')->comment('头部内容')->change();
                $table->string('foot_content', 4000)->default('')->comment('尾部内容')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
