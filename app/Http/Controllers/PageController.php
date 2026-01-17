<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PageController extends Controller
{
    // Список всех страниц
// app/Http/Controllers/PageController.php
    public function index()
    {
        // Загружаем дерево: главы -> пункты -> подпункты
        $tree = Page::where('parent_id', null)
                    ->with(['children' => function($query) {
                        $query->orderBy('sort')
                            ->with(['children' => function($q) {
                                $q->orderBy('sort');
                            }]);
                    }])
                    ->orderBy('sort')
                    ->get();
        
        return Inertia::render('Admin/Pages/Index', [
            'tree' => $tree
        ]);
    }

    public function create(Request $request)
    {
        // Получаем возможных родителей для выпадающего списка
        $chapters = Page::where('parent_id', null)
                        ->orderBy('sort')
                        ->get();
        
        $points = Page::whereIn('parent_id', $chapters->pluck('id'))
                    ->orderBy('sort')
                    ->get();
        
        return Inertia::render('Admin/Pages/Create', [
            'chapters' => $chapters,
            'points' => $points,
            'parent_id' => $request->input('parent_id') // Для предвыбора родителя
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:pages,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:pages|max:255',
            'content' => 'required|string',
            'menu_html' => 'nullable|string',
            'sort' => 'integer|min:0',
            'is_published' => 'boolean'
        ]);
        
        // Автогенерация slug если не указан
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Автоматический sort если не указан
        if (!isset($validated['sort'])) {
            $lastSort = Page::where('parent_id', $validated['parent_id'])
                            ->max('sort') ?? 0;
            $validated['sort'] = $lastSort + 10; // +10 для возможности вставки между
        }
        
        Page::create($validated);
        
        return redirect()->route('admin.pages.index')
            ->with('success', 'Страница успешно создана!');
    }

    // Форма редактирования
    public function edit(Page $page)
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page
        ]);
    }

    // Обновление страницы
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:pages,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'menu_html' => 'nullable|string',
            'sort' => 'integer|min:0',
            'is_published' => 'boolean'
        ]);

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Страница успешно обновлена!');
    }

    // Удаление страницы
    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Страница успешно удалена!');
    }

    // Публичный просмотр (для /{slug})
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->where('is_published', true)->firstOrFail();
        
        return Inertia::render('Page', [
            'page' => $page
        ]);
    }
}