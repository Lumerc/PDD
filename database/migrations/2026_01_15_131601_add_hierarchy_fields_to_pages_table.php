<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            // Родительская страница (null = глава, ID главы = пункт, ID пункта = подпункт)
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            
            // Порядок сортировки внутри родителя
            $table->integer('sort')->default(0)->after('parent_id');
            
            // HTML меню (резервное поле, можно null)
            $table->text('menu_html')->nullable()->after('content');
            
            // Индексы для быстрого поиска
            $table->index('parent_id');
            $table->index('sort');
            
            // Внешний ключ
            $table->foreign('parent_id')
                ->references('id')
                ->on('pages')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'sort', 'menu_html']);
        });
    }
};
