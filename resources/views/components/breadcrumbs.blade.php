@props(['items' => []])

@if(count($items) > 0)
    <nav aria-label="{{ __('Navegação estrutural') }}" class="mb-6">
        <ol class="flex flex-wrap items-center gap-1 text-sm">
            @foreach($items as $i => $item)
                @if($i > 0)
                    <li aria-hidden="true" class="mx-0.5 text-gray-300 dark:text-gray-600">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </li>
                @endif
                <li class="flex min-w-0 items-center">
                    @if(!empty($item['url']))
                        <a href="{{ $item['url'] }}"
                           class="truncate rounded-md px-1.5 py-0.5 text-gray-600 underline-offset-2 transition hover:text-emerald-700 hover:underline dark:text-gray-400 dark:hover:text-emerald-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="truncate rounded-lg bg-gray-100 px-2 py-0.5 font-medium text-gray-900 dark:bg-gray-800 dark:text-gray-100">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
