@props(['spa' => false])
{{-- The SPA shell loads only the Vue app; Alpine is for the Blade pages (auth forms, toasts) --}}
@if ($spa)
@vite('resources/js/spa.js')
@else
@vite('resources/js/app.js')
@endif
</body>
</html>
<!-- made with ❤ by wbg.ch & marceli.to -->
