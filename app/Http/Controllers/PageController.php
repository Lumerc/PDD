<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PageController extends Controller
{
    // Список всех страниц
    public function index()
    {
        $pages = Page::orderBy('order')->get();
        
        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages
        ]);
    }

    // Форма создания
    public function create()
    {
        return Inertia::render('Admin/Pages/Create');
    }

    // Сохранение новой страницы
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:pages|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_published' => 'boolean'
        ]);

        // Автогенерация slug если не указан
        if (empty($validated['slug'])) 
        {
            $validated['slug'] = Str::slug($validated['title']);
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
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
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