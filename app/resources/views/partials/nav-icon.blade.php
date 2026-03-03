@php($iconClass = trim('nav-icon-svg '.($class ?? '')))

@switch($name)
    @case('home')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3.5 10.5L12 3.5L20.5 10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6.5 9.8V19.5H17.5V9.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('catalog')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4.5 5.5H10.5V10.5H4.5V5.5Z" stroke="currentColor" stroke-width="1.8" rx="1"/>
            <path d="M13.5 5.5H19.5V10.5H13.5V5.5Z" stroke="currentColor" stroke-width="1.8" rx="1"/>
            <path d="M4.5 13.5H10.5V18.5H4.5V13.5Z" stroke="currentColor" stroke-width="1.8" rx="1"/>
            <path d="M13.5 13.5H19.5V18.5H13.5V13.5Z" stroke="currentColor" stroke-width="1.8" rx="1"/>
        </svg>
        @break
    @case('about')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="8.3" r="3.1" stroke="currentColor" stroke-width="1.8"/>
            <path d="M5.4 18.3C6.6 15.9 9 14.6 12 14.6C15 14.6 17.4 15.9 18.6 18.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('services')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M10 5.5L8.9 7.8L6.5 8.9L8.9 10L10 12.3L11.1 10L13.5 8.9L11.1 7.8L10 5.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M16 11L14.7 13.8L12 15.1L14.7 16.4L16 19.2L17.3 16.4L20 15.1L17.3 13.8L16 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
        @break
    @case('portfolio')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="4.5" y="5.5" width="15" height="13" rx="2" stroke="currentColor" stroke-width="1.8"/>
            <path d="M8 10.2C8.7 10.2 9.2 9.7 9.2 9C9.2 8.3 8.7 7.8 8 7.8C7.3 7.8 6.8 8.3 6.8 9C6.8 9.7 7.3 10.2 8 10.2Z" fill="currentColor"/>
            <path d="M6.5 16.5L10.2 12.8L12.8 15.4L14.8 13.4L17.5 16.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('blog')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6.5 5.5H17.5C18.1 5.5 18.5 5.9 18.5 6.5V17.5C18.5 18.1 18.1 18.5 17.5 18.5H6.5C5.9 18.5 5.5 18.1 5.5 17.5V6.5C5.5 5.9 5.9 5.5 6.5 5.5Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M8.8 9.2H15.2M8.8 12H15.2M8.8 14.8H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('quote')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="5" y="4.8" width="14" height="14.4" rx="2" stroke="currentColor" stroke-width="1.8"/>
            <path d="M8.3 8.7H15.7M8.3 11.7H15.7M8.3 14.7H12.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M12.2 4.8V19.2" stroke="currentColor" stroke-width="1.2" opacity=".18"/>
        </svg>
        @break
    @case('contact')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6.5 6.5H17.5C18.6 6.5 19.5 7.4 19.5 8.5V15.5C19.5 16.6 18.6 17.5 17.5 17.5H10L6.5 20V17.5C5.4 17.5 4.5 16.6 4.5 15.5V8.5C4.5 7.4 5.4 6.5 6.5 6.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M8.5 10.3H15.5M8.5 13.2H13.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('cart')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3.5 5.5H6L7.8 15.2H18.2L20.2 8.2H7.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="10" cy="19" r="1.2" fill="currentColor"/>
            <circle cx="17" cy="19" r="1.2" fill="currentColor"/>
        </svg>
        @break
    @case('panel')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="4" y="4" width="7.5" height="7.5" rx="1.8" stroke="currentColor" stroke-width="1.8"/>
            <rect x="12.5" y="4" width="7.5" height="4.8" rx="1.8" stroke="currentColor" stroke-width="1.8"/>
            <rect x="12.5" y="10.8" width="7.5" height="9.2" rx="1.8" stroke="currentColor" stroke-width="1.8"/>
            <rect x="4" y="13.5" width="7.5" height="6.5" rx="1.8" stroke="currentColor" stroke-width="1.8"/>
        </svg>
        @break
    @case('orders')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M7 4.5H17L19.5 7V19.5H7V4.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M17 4.5V7H19.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 11H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M10 14.5H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M4.5 8.5L5.8 9.8L8.5 7.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('cadastros')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4.5 7.5C4.5 6.4 5.4 5.5 6.5 5.5H11.2C12.3 5.5 13.2 6.4 13.2 7.5V11.2C13.2 12.3 12.3 13.2 11.2 13.2H6.5C5.4 13.2 4.5 12.3 4.5 11.2V7.5Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M4.5 16.2C4.5 15.1 5.4 14.2 6.5 14.2H11.2C12.3 14.2 13.2 15.1 13.2 16.2V17.5C13.2 18.6 12.3 19.5 11.2 19.5H6.5C5.4 19.5 4.5 18.6 4.5 17.5V16.2Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M15.5 7.5C15.5 6.4 16.4 5.5 17.5 5.5H18.5C19.6 5.5 20.5 6.4 20.5 7.5V17.5C20.5 18.6 19.6 19.5 18.5 19.5H17.5C16.4 19.5 15.5 18.6 15.5 17.5V7.5Z" stroke="currentColor" stroke-width="1.8"/>
        </svg>
        @break
    @case('store')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5 9.2L6.3 5.5H17.7L19 9.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4.5 9.2H19.5V12C19.5 13.1 18.6 14 17.5 14C16.4 14 15.5 13.1 15.5 12C15.5 13.1 14.6 14 13.5 14C12.4 14 11.5 13.1 11.5 12C11.5 13.1 10.6 14 9.5 14C8.4 14 7.5 13.1 7.5 12C7.5 13.1 6.6 14 5.5 14C4.9 14 4.5 13.6 4.5 13V9.2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6.5 14V19.5H17.5V14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('account')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="8.5" r="3.2" stroke="currentColor" stroke-width="1.8"/>
            <path d="M5.2 18.5C6.4 15.9 8.8 14.5 12 14.5C15.2 14.5 17.6 15.9 18.8 18.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('login')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M10 6.5H7C5.9 6.5 5 7.4 5 8.5V15.5C5 16.6 5.9 17.5 7 17.5H10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M13 8L17.5 12L13 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 12H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('logout')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M14 6.5H17C18.1 6.5 19 7.4 19 8.5V15.5C19 16.6 18.1 17.5 17 17.5H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M11 8L6.5 12L11 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7 12H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('checkout')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5.5 8.5C5.5 6.8 6.8 5.5 8.5 5.5H15.5C17.2 5.5 18.5 6.8 18.5 8.5V15.5C18.5 17.2 17.2 18.5 15.5 18.5H8.5C6.8 18.5 5.5 17.2 5.5 15.5V8.5Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M8.8 12.3L11 14.5L15.4 10.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('status-generic')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="7.2" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="12" cy="12" r="1.3" fill="currentColor"/>
        </svg>
        @break
    @case('status-draft')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5.5 18.5L8.2 17.9L17.7 8.4C18.4 7.7 18.4 6.6 17.7 5.9V5.9C17 5.2 15.9 5.2 15.2 5.9L5.7 15.4L5.5 18.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M14.3 6.8L16.8 9.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('status-pending')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 7.8V12.4L14.8 14.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('status-authorized')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 4.5L18.5 7.2V11.8C18.5 15.6 15.9 18.4 12 19.5C8.1 18.4 5.5 15.6 5.5 11.8V7.2L12 4.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9.4 12.2L11.2 14L14.8 10.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('status-paid')
    @case('status-approved')
    @case('status-delivered')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
            <path d="M8.6 12.3L10.9 14.6L15.4 10.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break
    @case('status-production')
    @case('status-prepress')
    @case('status-printing')
    @case('status-finishing')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M7 8V5.5H17V8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M6.5 10H17.5C18.6 10 19.5 10.9 19.5 12V15.5H4.5V12C4.5 10.9 5.4 10 6.5 10Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M7 15.5H17V19H7V15.5Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M16.2 12.7H16.2" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"/>
        </svg>
        @break
    @case('status-shipped')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4.5 8.5H14.5V16.5H4.5V8.5Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M14.5 10.5H17.3L19.5 12.7V16.5H14.5V10.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <circle cx="8.3" cy="17.8" r="1.3" fill="currentColor"/>
            <circle cx="16.8" cy="17.8" r="1.3" fill="currentColor"/>
        </svg>
        @break
    @case('status-canceled')
    @case('status-failed')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
            <path d="M9.4 9.4L14.6 14.6M14.6 9.4L9.4 14.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('status-refunded')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M8 8.5H17.5V16.5H8V8.5Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M6.8 11.5L4.5 13.8L6.8 16.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4.8 13.8H10.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M11.5 11.6H14.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        @break
    @case('status-uploaded')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 15.5V6.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M8.8 9.6L12 6.5L15.2 9.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5.5 17.5C5.5 16.4 6.4 15.5 7.5 15.5H16.5C17.6 15.5 18.5 16.4 18.5 17.5V18.5H5.5V17.5Z" stroke="currentColor" stroke-width="1.8"/>
        </svg>
        @break
    @case('status-review')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3.8 12C5.7 8.7 8.6 7 12 7C15.4 7 18.3 8.7 20.2 12C18.3 15.3 15.4 17 12 17C8.6 17 5.7 15.3 3.8 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="2.4" stroke="currentColor" stroke-width="1.8"/>
        </svg>
        @break
    @case('status-adjustment')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5.5 18.5L8.1 18L16.8 9.3C17.5 8.6 17.5 7.4 16.8 6.7V6.7C16.1 6 14.9 6 14.2 6.7L5.5 15.4V18.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M13.3 7.6L15.9 10.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M12.7 17.8H18.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('status-pix')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M9.2 7.2C10.2 6.2 11.8 6.2 12.8 7.2L14.1 8.5C14.8 9.2 15.9 9.2 16.6 8.5L17.8 7.3C18.8 6.3 20.4 6.3 21.4 7.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M2.6 16.7C3.6 17.7 5.2 17.7 6.2 16.7L7.4 15.5C8.1 14.8 9.2 14.8 9.9 15.5L11.2 16.8C12.2 17.8 13.8 17.8 14.8 16.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M8.4 12L12 8.4L15.6 12L12 15.6L8.4 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
        @break
    @case('status-card')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="4.5" y="6.5" width="15" height="11" rx="2" stroke="currentColor" stroke-width="1.8"/>
            <path d="M4.5 10.2H19.5" stroke="currentColor" stroke-width="1.8"/>
            <path d="M8.2 14.2H11.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('status-billing')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6.5 4.5H17.5V19.5L15.5 18L13.5 19.5L11.5 18L9.5 19.5L7.5 18L6.5 19.5V4.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 9H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M9 12H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M9 15H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @case('status-bank')
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4.5 9L12 4.5L19.5 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6.5 10.5V17.5M10.5 10.5V17.5M14.5 10.5V17.5M18.5 10.5V17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M4.5 19.5H19.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break
    @default
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
        </svg>
@endswitch
