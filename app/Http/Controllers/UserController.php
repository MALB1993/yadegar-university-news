<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * List the accounts with optional search and role filter.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $search = $request->string('q')->trim()->toString();
        $role = $request->string('role')->toString();

        $users = User::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $term = '%'.addcslashes($search, '%_\\').'%';

                $query->where(function (Builder $query) use ($term): void {
                    $query->where('fullname', 'like', $term)
                        ->orWhere('identification_code', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when(
                in_array($role, ['student', 'professor', 'employee'], true),
                fn (Builder $query) => $query->where('role', $role)
            )
            ->orderBy('fullname')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users', 'search', 'role'));
    }

    /**
     * Change the role and the status of an account.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'role' => ['required', Rule::in(['student', 'professor', 'employee'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user->update($data);

        return back()->with('status', 'اطلاعات «'.$user->fullname.'» به‌روزرسانی شد.');
    }
}
