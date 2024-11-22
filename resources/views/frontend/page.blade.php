<x-frontend.base :$page :class="$dataClass ?? ''">

    @if (view()->exists('components.templates.' . $componentName))
        <x-dynamic-component :component="'templates.' . $componentName" :$page :$content />
    @elseif (view()->exists('components.templates.' . $modelName))
        <x-dynamic-component :component="'templates.' . $modelName" :$page :$content />
    @elseif (view()->exists('components.frontend.default'))
        <x-dynamic-component component="frontend.default" :$page :$content />
    @else
        <div>Assigned and default template are not found. Create default template on
            views.components.frontend.default.blade.php</div>
    @endif
</x-frontend.base>
