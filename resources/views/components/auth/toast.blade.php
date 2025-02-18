@props(['status'])
@if ($status)
<div 
  class="fixed z-[9999] text-sm w-full text-white font-muoto-medium top-80 left-0"
  data-toast>
  <div
    @class([
      '!bg-flame text-center min-h-32 flex items-center justify-center max-w-[1600px] mx-auto',
    ])>
    {{ $status }}
  </div>
</div>
@endif
