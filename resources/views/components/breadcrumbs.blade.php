@props(['items' => []])

@if(count($items) > 0)
    <nav aria-label="Breadcrumb" class="mb-4">
        <ol class="flex flex-wrap items-center gap-1.5 text-sm text-gray-600 dark:text-gray-400">
            @foreach($items as $i => $item)
                @if($i > 0)
                    <li aria-hidden="true" class="text-gray-400 dark:text-gray-500">/</li>
                @endif
                <li class="flex items-center gap-1.5">
                    @if(!empty($item['url']))
                        <a href="{{ $item['url'] }}" class="hover:text-gray-900 dark:hover:text-gray-200 transition">{{ $item['label'] }}</a>
                    @else
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
