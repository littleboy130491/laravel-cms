<x-frontend.base :$page :class="$dataClass ?? ''">
    <x-dynamic-component :component="'templates.' . $template" :$page :$content />
</x-frontend.base>
