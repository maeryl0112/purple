@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-darkPurple text-lg font-medium leading-5 text-darkPurple focus:outline-none focus:border-purple-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-lg font-medium leading-5 text-salonPurple hover:text-darkPurple hover:border-darkPurple focus:outline-none focus:text-darkPurple focus:border-darkPurple transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
