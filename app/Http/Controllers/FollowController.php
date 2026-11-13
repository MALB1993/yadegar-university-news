<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Follow or unfollow a news category. Every signed in user can follow categories.
     */
    public function toggle(Category $category, Request $request): RedirectResponse
    {
        $changes = $request->user()->followedCategories()->toggle($category->id);

        $message = $changes['attached'] !== []
            ? 'دسته‌بندی «'.$category->name.'» به فهرست دنبال‌شده‌های شما اضافه شد.'
            : 'دسته‌بندی «'.$category->name.'» از فهرست دنبال‌شده‌ها حذف شد.';

        return back()->with('status', $message);
    }
}
