@php($hintText = trim((string) ($text ?? '')))

@if($hintText !== '')
    <span class="label-help" tabindex="0" role="note" aria-label="Ajuda: {{ $hintText }}" data-help="{{ $hintText }}">
        @include('partials.nav-icon', ['name' => 'info', 'class' => 'help-icon'])
    </span>
@endif
