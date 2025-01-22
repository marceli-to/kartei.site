@props(['disabled' => false])
<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'text-black  rounded-sm p-10 w-full !ring-0 focus:ring-0 border-black border-2']) !!}>
