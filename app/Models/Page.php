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
        return $this->children()->with('allChildren');
    }

    // Получаем уровень вложенности (0 = глава, 1 = пункт, 2 = подпункт)
    public function getLevelAttribute(): int
    {
        if ($this->parent_id === null) return 0;
        if ($this->parent->parent_id === null) return 1;
        return 2;
    }

    // Автоматически генерируемый номер (например: "4.1.2")
    public function getAutoNumberAttribute(): string
    {
        if ($this->level === 0) {
            // Для глав: порядковый номер среди глав
            $chapterNumber = Page::where('parent_id', null)
                ->where('sort', '<', $this->sort)
                ->count() + 1;
            return (string) $chapterNumber;
        }
        
        if ($this->level === 1) {
            // Для пунктов: номер главы + порядковый номер пункта
            $chapterNumber = Page::where('parent_id', null)
                ->where('sort', '<', $this->parent->sort)
                ->count() + 1;
            
            $pointNumber = $this->parent->children()
                ->where('sort', '<', $this->sort)
                ->count() + 1;
                
            return $chapterNumber . '.' . $pointNumber;
        }
        
        if ($this->level === 2) {
            // Для подпунктов: номер главы.пункта + порядковый номер подпункта
            $chapterNumber = Page::where('parent_id', null)
                ->where('sort', '<', $this->parent->parent->sort)
                ->count() + 1;
            
            $pointNumber = $this->parent->parent->children()
                ->where('sort', '<', $this->parent->sort)
                ->count() + 1;
                
            $subpointNumber = $this->parent->children()
                ->where('sort', '<', $this->sort)
                ->count() + 1;
                
            return $chapterNumber . '.' . $pointNumber . '.' . $subpointNumber;
        }
        
        return '';
    }

    // Полное название с автонумерацией
    public function getFullTitleAttribute(): string
    {
        return $this->auto_number . '. ' . $this->title;
    }
}