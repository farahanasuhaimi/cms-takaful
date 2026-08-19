{{--
    Wraps PDPA-sensitive content (client names, commission, payment amounts).
    When Privacy Mode is on ($store.privacy.enabled), the content is blurred
    and unselectable — safe to screenshot for social media without exposing
    personal data. Toggle lives in the topbar (layouts/app.blade.php).
--}}
<span x-data
      :class="$store.privacy.enabled ? 'blur-sm select-none' : ''"
      class="transition-all duration-150">{{ $slot }}</span>
