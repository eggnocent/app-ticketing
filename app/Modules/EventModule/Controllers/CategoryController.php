<?php

namespace App\Modules\EventModule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Requests\StoreCategoryRequest;
use App\Modules\EventModule\Requests\UpdateCategoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount('events')
            ->orderBy('name')
            ->paginate(10);

        return view('event::admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('event::admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category): View
    {
        return view('event::admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->events()->exists()) {
            return back()->with('error', 'Kategori masih dipakai oleh event sehingga tidak bisa dihapus.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
