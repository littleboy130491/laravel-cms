<x-frontend.base>
    @if (view()->exists("components.{$componentName}"))
        <x-dynamic-component :component="$componentName" :content="$content ?? '{Content from page will be rendered here}'" />
    @else
        <div>Component is not found</div>
    @endif
</x-frontend.base>
