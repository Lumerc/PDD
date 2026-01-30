<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'slug',
        'title',
        'content',
        'menu_html', // Резервное поле
        'meta_title',
        'meta_description',
        'is_published',
        'sort'
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    // Родительская страница
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    // Дочерние страницы (с сортировкой)
    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('sort');
    }

    // Рекурсивно получаем всех потомков
    public function allChildren()
    {
        return $this->children()
            ->select(['id', 'sort', 'slug', 'title'])
            ->with('allChildren');
    }
}