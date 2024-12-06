<x-frontend.base :$page :class="$dataClass ?? ''">

    @if ($componentName && view()->exists('components.templates.' . $componentName))
        <x-dynamic-component :component="'templates.' . $componentName" :$page :$content />
    @elseif (view()->exists('components.frontend.' . $modelName))
        <x-dynamic-component :component="'frontend.' . $modelName" :$page :$content />
    @elseif (view()->exists('components.frontend.default'))
        <x-dynamic-component component="frontend.default" :$page :$content />
    @else
        <div>Assigned and default template are not found.</div>
    @endif
</x-frontend.base>
