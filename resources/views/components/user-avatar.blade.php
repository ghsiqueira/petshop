@props([
    'user' => null,
    'size' => 'md', // xs, sm, md, lg, xl
    'class' => '',
    'showName' => false
])

@php
    $sizes = [
        'xs' => 'width: 24px; height: 24px; font-size: 10px;',
        'sm' => 'width: 32px; height: 32px; font-size: 12px;',
        'md' => 'width: 48px; height: 48px; font-size: 16px;',
        'lg' => 'width: 64px; height: 64px; font-size: 20px;',
        'xl' => 'width: 96px; height: 96px; font-size: 24px;',
    ];
    
    $sizeStyle = $sizes[$size] ?? $sizes['md'];
    $user = $user ?? auth()->user();
@endphp

<div class="d-flex align-items-center {{ $class }}">
    @if($user && $user->hasProfilePhoto())
        <img 
            src="{{ $user->profile_photo_url }}" 
            alt="{{ $user->name }}" 
            class="rounded-circle object-fit-cover"
            style="{{ $sizeStyle }}"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >
        <!-- Fallback para quando a imagem falha ao carregar -->
        <div 
            class="rounded-circle bg-primary text-white d-none align-items-center justify-content-center fw-bold"
            style="{{ $sizeStyle }}"
        >
            {{ $user->initials }}
        </div>
    @else
        <!-- Avatar com iniciais quando não há foto -->
        <div 
            class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
            style="{{ $sizeStyle }}"
            title="{{ $user ? $user->name : 'Usuário' }}"
        >
            {{ $user ? $user->initials : 'U' }}
        </div>
    @endif
    
    @if($showName && $user)
        <span class="ms-2">{{ $user->name }}</span>
    @endif
</div>