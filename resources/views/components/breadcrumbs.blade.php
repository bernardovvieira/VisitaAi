@props(['items' => []])

@if(count($items) > 0)
    <nav aria-label="{{ __('Navegação estrutural') }}" class="mb-5">
        <ol class="flex flex-wrap items-center gap-x-1 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400">
            @foreach($items as $i => $item)
                @if($i > 0)
                    <li aria-hidden="true" class="text-gray-300 dark:text-gray-600">/</li>
                @endif
                <li class="min-w-0">
                    @if(!empty($item['url']))
                        <a href="{{ $item['url'] }}"
                           class="truncate font-medium text-gray-600 underline-offset-2 transition hover:text-blue-600 hover:underline dark:text-gray-400 dark:hover:text-blue-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 rounded">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="truncate font-semibold text-gray-900 dark:text-gray-100">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
