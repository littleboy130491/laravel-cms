@props(['sticky' => true])
@inject('settings', 'App\Settings\GeneralSettings')

<header @class([
    'w-full bg-white border-b border-gray-200',
    'sticky top-0 z-50' => $sticky,
])>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            @if ($settings->site_logo)
                <x-curator-glider :media="$settings->site_logo" />
            @endif
            <div class="flex-shrink-0">
                <a href="#" class="flex items-center">
                    <img src="" alt="{{ config('app.name') }}" class="h-8 w-auto">
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex space-x-8">
                <a href="#" class="text-gray-500 hover:text-gray-900">Home</a>
                <a href="#" class="text-gray-500 hover:text-gray-900">About</a>
                <a href="#" class="text-gray-500 hover:text-gray-900">Services</a>
                <a href="#" class="text-gray-500 hover:text-gray-900">Contact</a>
            </nav>

            {{-- Mobile menu button --}}
            <div class="md:hidden">
                <button type="button" x-data="{ open: false }" @click="open = !open"
                    class="text-gray-500 hover:text-gray-900 focus:outline-none">
                    <span x-show="!open">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </span>
                    <span x-show="open" style="display: none;">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>

        {{-- Mobile Navigation --}}
        <div x-data="{ open: false }" x-show="open" @click.away="open = false" style="display: none;" class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Home</a>
                <a href="#"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">About</a>
                <a href="#"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Services</a>
                <a href="#"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Contact</a>
            </div>
        </div>
    </div>
</header>
