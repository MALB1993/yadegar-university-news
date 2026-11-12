<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Category::class);

        return view('categories.index', [
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        Category::create($request->validated());

        return redirect()->route('categories.index')->with('status', 'دسته‌بندی ساخته شد.');
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('categories.edit', ['category' => $category]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $category->update($request->validated());

        return redirect()->route('categories.index')->with('status', 'دسته‌بندی ویرایش شد.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        // The database would refuse it anyway (restrictOnDelete), this gives a readable message.
        if ($category->posts()->exists()) {
            return redirect()->route('categories.index')->with('error', 'این دسته‌بندی خبر دارد و قابل حذف نیست.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('status', 'دسته‌بندی حذف شد.');
    }
}
