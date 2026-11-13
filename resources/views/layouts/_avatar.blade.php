@php($avatarSize = $size ?? 40)
@if ($user->profile_picture)
    <img src="{{ asset('storage/'.$user->profile_picture) }}" alt="{{ $user->fullname }}"
         class="rounded-circle object-fit-cover flex-shrink-0" style="width: {{ $avatarSize }}px; height: {{ $avatarSize }}px;">
@else
    <span class="avatar-initial rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0"
          style="width: {{ $avatarSize }}px; height: {{ $avatarSize }}px; font-size: {{ (int) round($avatarSize / 2.5) }}px;">
        {{ mb_substr($user->fullname, 0, 1) }}
    </span>
@endif
