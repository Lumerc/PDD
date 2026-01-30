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
                    ->select('id', 'sort', 'slug', 'title', 'is_published')
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
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'is_published' => 'boolean'
        ]);
        
        // Автогенерация slug если не указан
        if (empty($validated['slug']) || $validated['slug'] == '-') {
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
        // Получаем возможных родителей для выпадающего списка
        $chapters = Page::where('parent_id', null)
                        ->orderBy('sort')
                        ->get();
        
        $points = Page::whereIn('parent_id', $chapters->pluck('id'))
                    ->orderBy('sort')
                    ->get();
        
        return Inertia::render('Admin/Pages/Edit', [
            'chapters' => $chapters,
            'points' => $points,
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

        // Автогенерация slug если не указан
        if (empty($validated['slug']) || $validated['slug'] == '-') {
            $validated['slug'] = Str::slug($validated['title']);
        }

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

    public function show($slug)
    {
        $allPages = Page::where('is_published', true)
            ->select('sort', 'slug', 'title', 'parent_id', 'id')
            ->orderBy('parent_id')
            ->orderBy( 'sort')
            ->get()
            ->keyBy('id');

        $pagesIndex = array();
        // Получаем все разделы (корневые страницы)
        $chapters = array();

        /*
            создаём индексную карту
            XXYYZZ XX - это номер главы от 1 до 99
            YY - это номер пункта, от 1 до 99
            ZZ - это номер подпункта, от 1 до 99
            а нужно это для того, чтобы легко узнать как вложенные статьи,
            так и смежные, которые были бы в иерархии очень далеко,
            и которые без перебора невозможно было бы найти
        */

        foreach($allPages as $thisPage)
        {
            if(!$thisPage['parent_id'])
            {
                //это разделы
                $pagesIndex[($thisPage['sort'] / 10) * 10000] = array('id' => $thisPage['id'], 'type' => 'chapter');
                $chapters[] = $thisPage;
                continue;
            }

            if(!$allPages[$thisPage['parent_id']]['parent_id'])
            {
                //это пункты
                $index = ($allPages[$thisPage['parent_id']]['sort'] / 10) * 10000;
                $index += ($thisPage['sort'] / 10) * 100;
                $pagesIndex[$index] = array('id' => $thisPage['id'], 'type' => 'point');
                continue;
            }
            else
            {
                //это подпункты
                $index = ($allPages[$allPages[$thisPage['parent_id']]['parent_id']]['sort'] / 10) * 10000;
                $index += ($allPages[$thisPage['parent_id']]['sort'] / 10) * 100;
                $index += $thisPage['sort'] / 10;
                $pagesIndex[$index] = array('id' => $thisPage['id'], 'type' => 'subpoint');
                continue;
            }
        }

        ksort($pagesIndex);

        //узнаём текущий slug
        $currentSlug = '';
        foreach($pagesIndex as $index => $indexInfo)
        {
            if($allPages[$indexInfo['id']]['slug'] == $slug)
            {
                $currentSlug = $slug;
                if($indexInfo['type'] == 'chapter')
                {   
                    if(isset($pagesIndex[$index + 101])) //сначала проверяем подпункт
                    {
                        $currentSlug = $allPages[$pagesIndex[$index + 101]['id']]['slug'];
                    }
                    else if(isset($pagesIndex[$index + 100])) //здесь проверяем подпункт
                    {
                        $currentSlug = $allPages[$pagesIndex[$index + 100]['id']]['slug'];
                    }
                    
                    //но если ничего не нашли, то открываем главу
                }
                else if($indexInfo['type'] == 'point')
                {
                    if(isset($pagesIndex[$index + 1])) //проверяем подпункт
                    {
                        $currentSlug = $allPages[$pagesIndex[$index + 1]['id']]['slug'];
                    }
                }
                
                break;
            }
        }

        if(!$currentSlug)
            abort(404);
        
        $page = Page::where('slug', $currentSlug)->firstOrFail();

        //здесь находим предыдущую статью и следующую

        $isPageFound = false;
        $prevPage = null;
        $nextPage = null;
        $currentIndex = null;

        $points = array();
        $subPoints = array();
        $currentPoint = 0;

        $mainTitle = '';
        $subTitle = '';

        foreach($pagesIndex as $index => $thisPage)
        {
            if($page['slug'] == $allPages[$thisPage['id']]['slug'])
            {
                $isPageFound = true;

                $currentIndex = $index;

                if($thisPage['type'] == 'chapter')
                {
                    $currentChapter = $page;
                    $mainTitle = 'Раздел ' . (($currentChapter->sort / 10)) . ' ' . $currentChapter->title;
                    $subTitle = '';

                    for($i = 100; $i < 10000; $i+=100)
                    {
                        if(isset($pagesIndex[$index + $i]))
                        {
                            $points[$pagesIndex[$index + $i]['id']] = $allPages[$pagesIndex[$index + $i]['id']];
                        }
                        else
                            break;
                    }
                }
                else if($thisPage['type'] == 'point')
                {
                    $currentChapter = $allPages[$allPages[$thisPage['id']]['parent_id']];
                    $currentPoint = $page->sort;

                    $mainTitle = 'Раздел ' . ($currentChapter->sort / 10) . ' ' . $currentChapter->title;
                    $subTitle = ($currentChapter->sort/10) . '.' . ($currentPoint / 10) . ' ' . $page->title;

                    $newStringIndex = (string)$index;
                    $newStringIndex[strlen($newStringIndex) - 1] = 0;
                    $newStringIndex[strlen($newStringIndex) - 2] = 0;
                    $newStringIndex[strlen($newStringIndex) - 3] = 0;
                    $newStringIndex[strlen($newStringIndex) - 4] = 0;
                    for($i = 100; $i < 10000; $i += 100)
                    {
                        if(isset($pagesIndex[$newStringIndex + $i]))
                        {
                            $points[] = $allPages[$pagesIndex[$newStringIndex + $i]['id']];
                        }
                        else
                            break;
                    }


                    for($i = 1; $i < 100; $i ++)
                    {
                        if(isset($pagesIndex[$index + $i]))
                        {
                            $subPoints[] = $allPages[$pagesIndex[$index + $i]['id']];
                        }
                        else
                            break;
                    }
                }
                else if($thisPage['type'] == 'subpoint')
                {
                    $currentChapter = $allPages[$allPages[$allPages[$thisPage['id']]['parent_id']]['parent_id']];
                    $currentPoint = $allPages[$page->parent_id]['sort'];

                    $mainTitle = ($currentChapter->sort/10) . '.' . ($currentPoint / 10) . ' ' . $allPages[$page->parent_id]['title'];
                    $subTitle = $page->title;

                    $newStringIndex = (string)$index;
                    $newStringIndex[strlen($newStringIndex) - 1] = 0;
                    $newStringIndex[strlen($newStringIndex) - 2] = 0;
                    for($i = 1; $i < 100; $i ++)
                    {
                        if(isset($pagesIndex[$newStringIndex + $i]))
                        {
                            $subPoints[] = $allPages[$pagesIndex[$newStringIndex + $i]['id']];
                        }
                        else
                            break;
                    }

                    $newStringIndex[strlen($newStringIndex) - 3] = 0;
                    $newStringIndex[strlen($newStringIndex) - 4] = 0;
                    for($i = 100; $i < 10000; $i += 100)
                    {
                        if(isset($pagesIndex[$newStringIndex + $i]))
                        {
                            $points[] = $allPages[$pagesIndex[$newStringIndex + $i]['id']];
                        }
                        else
                            break;
                    }
                }
                continue;
            }
            
            if($isPageFound)
            {
                $nextPage = $index;
                break;
            }

            $prevPage = $index;
        }
        
        if($nextPage)
        {
            $nextPage = $allPages[$pagesIndex[$nextPage]['id']];
        }

        if($prevPage)
        {
            $newStringIndex = (string)$prevPage;
            $newStringcurrentIndex = (string)$currentIndex;

            if($newStringIndex[strlen($newStringIndex) - 1] == 0 && 
                $newStringIndex[strlen($newStringIndex) - 2] == 0 &&
                $newStringcurrentIndex[strlen($newStringcurrentIndex) - 1] == 1 && 
                $newStringcurrentIndex[strlen($newStringcurrentIndex) - 2] == 0 ||
                $newStringIndex[strlen($newStringIndex) - 1] == 0 && 
                $newStringIndex[strlen($newStringIndex) - 2] == 0 &&
                $newStringIndex[strlen($newStringIndex) - 3] == 0 && 
                $newStringIndex[strlen($newStringIndex) - 4] == 0)
            {

                $keys = array_keys($pagesIndex);
                $currentPrevIndex = array_search($prevPage, $keys);

                if(isset($keys[$currentPrevIndex - 1]))
                {
                    $prevPage = $keys[$currentPrevIndex - 1];
                
                    $newStringIndexKey = (string)$prevPage;

                    if($newStringIndexKey[strlen($newStringIndexKey) - 1] == 0 && 
                        $newStringIndexKey[strlen($newStringIndexKey) - 2] == 0 &&
                        $newStringIndexKey[strlen($newStringIndexKey) - 3] == 0 &&
                        $newStringIndexKey[strlen($newStringIndexKey) - 4] == 0)
                    {
                        $anotherOneIndex = array_search($prevPage, $keys);
                        
                        if(isset($keys[$anotherOneIndex - 1]))
                            $prevPage = $keys[$anotherOneIndex - 1];
                        else
                            $prevPage = null;
                        
                    }
                }
                else
                {
                    $prevPage = null;
                }

            }

            if($prevPage)
                $prevPage = $allPages[$pagesIndex[$prevPage]['id']];
        }

        /*
        $newStringIndex = (string)$index;
        if($newStringIndex[strlen($newStringIndex) - 1] == 1 || 
            $newStringIndex[strlen($newStringIndex) - 1] == 0 && $newStringIndex[strlen($newStringIndex) - 3] == 1)
        {
            foreach()
            {

            }
        }
        */


        if($subPoints && count($subPoints) > 9)
        {
            $currentSubPoint = 0;
            foreach($subPoints as $idSubPoint => $subpoint)
            {
                if($slug == $subpoint->slug)
                {
                    $currentSubPoint = $idSubPoint;
                    break;
                }
            }

            if($currentSubPoint <= 4)
            {
                $subPoints = array_slice($subPoints, 0, 9);
            }
            else if($currentSubPoint >= count($subPoints) - 4)
            {
                $subPoints = array_slice($subPoints, count($subPoints) - 8, 9);
            }
            else
            {
                $subPoints = array_slice($subPoints, $currentSubPoint - 4, 9);
            }
        }

        return inertia('Public/Show', [
            'page' => $page,
            'chapters' => $chapters,
            'currentChapter' => $currentChapter,
            'currentPoint' => $currentPoint,
            'points' => $points,
            'subPoints' => $subPoints,
            'prevPage' => $prevPage,
            'nextPage' => $nextPage,
            'mainTitle' => $mainTitle,
            'subTitle' => $subTitle
        ]);
    }

    public function home()
    {
        // Находим первую главу (корневую страницу)
        $firstChapter = Page::whereNull('parent_id')
            ->orderBy('sort')
            ->first();
        
        // Если есть хоть одна страница - редирект на первую
        if ($firstChapter) {
            return redirect()->route('pages.show', $firstChapter->slug);
        }
        
        // Если страниц нет - показываем заглушку
        return inertia('Welcome');
    }
}