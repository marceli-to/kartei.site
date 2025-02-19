@props(['status', 'type' => 'success'])
@if ($status)
<div 

  x-data="{ show: true }"
  @if ($type !== 'error')
  x-init="setTimeout(() => show = false, 3000)"
  @endif
  x-on:click="show = false"
  x-show="show"
  class="fixed z-[9999] text-sm w-full text-white font-muoto-medium top-80 left-0 px-16 cursor-pointer"
  data-toast>
  <div
    @class([
      'text-center min-h-32 flex items-center justify-center max-w-page mx-auto',
      'bg-lime' => $type == 'success',
      'bg-flame' => $type == 'error',
      'bg-ice' => $type == 'info',
    ])>
    {{ $status }}
  </div>
</div>
@endif
