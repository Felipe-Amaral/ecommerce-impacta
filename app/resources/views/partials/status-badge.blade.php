@php
    $statusValue = $status ?? $value ?? null;
    $statusLabel = $label ?? \App\Support\UiStatus::label($statusValue);
    $statusIcon = $icon ?? \App\Support\UiStatus::icon($statusValue);
    $statusTone = $tone ?? \App\Support\UiStatus::tone($statusValue);
    $statusSize = $size ?? 'sm';
    $statusClass = trim('ui-status-badge tone-'.$statusTone.' size-'.$statusSize.' '.($class ?? ''));
@endphp

<span class="{{ $statusClass }}">
    @if(!empty($statusIcon))
        @include('partials.nav-icon', ['name' => $statusIcon, 'class' => 'ui-status-badge-icon'])
    @endif
    <span class="ui-status-badge-label">{{ $statusLabel }}</span>
</span>
