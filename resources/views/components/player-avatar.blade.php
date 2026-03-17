@php
    $initial = strtoupper(substr($player->name ?? '?', 0, 1));
    $colors = [
        'A' => '#e74c3c',
        'B' => '#e67e22',
        'C' => '#f39c12',
        'D' => '#27ae60',
        'E' => '#1abc9c',
        'F' => '#2980b9',
        'G' => '#8e44ad',
        'H' => '#c0392b',
        'I' => '#d35400',
        'J' => '#16a085',
        'K' => '#2c3e50',
        'L' => '#7f8c8d',
        'M' => '#e67e22',
        'N' => '#27ae60',
        'O' => '#2980b9',
        'P' => '#8e44ad',
        'Q' => '#c0392b',
        'R' => '#1abc9c',
        'S' => '#2c3e50',
        'T' => '#27ae60',
        'U' => '#f39c12',
        'V' => '#e74c3c',
        'W' => '#8e44ad',
        'X' => '#2980b9',
        'Y' => '#16a085',
        'Z' => '#d35400',
    ];
    $bg = $colors[$initial] ?? '#6c3fc5';
    $src = null;
    if (!empty($player->photo)) {
        $src = Storage::url($player->photo);
    } elseif (!empty($player->image_url)) {
        $src = $player->image_url;
    }
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $player->name }}"
        style="width:40px;height:40px;border-radius:50%;
                object-fit:cover;flex-shrink:0;
                border:2px solid #e8eaf0;"
        onerror="this.style.display='none';
                  this.nextElementSibling.style.display='flex';">
    <div
        style="display:none;width:40px;height:40px;
                border-radius:50%;background:{{ $bg }};
                color:#fff;font-weight:700;font-size:0.875rem;
                align-items:center;justify-content:center;
                flex-shrink:0;">
        {{ $initial }}
    </div>
@else
    <div
        style="width:40px;height:40px;border-radius:50%;
                background:{{ $bg }};color:#fff;font-weight:700;
                font-size:0.875rem;display:flex;align-items:center;
                justify-content:center;flex-shrink:0;">
        {{ $initial }}
    </div>
@endif
