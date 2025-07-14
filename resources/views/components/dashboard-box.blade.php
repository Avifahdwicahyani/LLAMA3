@props(['title', 'value', 'icon' => 'bi-graph-up', 'color' => 'primary'])

@php
    $colorClass = match($color) {
        'primary' => 'linear-gradient(135deg, rgba(0, 123, 255, 0.8), rgba(0, 86, 179, 0.8))',
        'success' => 'linear-gradient(135deg, rgba(40, 167, 69, 0.8), rgba(25, 135, 56, 0.8))',
        'danger'  => 'linear-gradient(135deg, rgba(220, 53, 69, 0.8), rgba(200, 35, 51, 0.8))',
        'warning' => 'linear-gradient(135deg, rgba(255, 193, 7, 0.8), rgba(255, 160, 0, 0.8))',
        'info'    => 'linear-gradient(135deg, rgba(23, 162, 184, 0.8), rgba(13, 110, 253, 0.8))',
        default   => 'linear-gradient(135deg, rgba(108, 117, 125, 0.8), rgba(73, 80, 87, 0.8))', // secondary
    };
@endphp

<div class="col-md-3 col-6 mb-4">
    <div class="dashboard-box p-3 h-100" style="background: {{ $colorClass }};">
        <div class="icon">
            <i class="bi {{ $icon }}"></i>
        </div>
        <div class="content">
            <div class="title">{{ $title }}</div>
            <div class="value">{{ $value }}</div>
        </div>
    </div>
</div>
