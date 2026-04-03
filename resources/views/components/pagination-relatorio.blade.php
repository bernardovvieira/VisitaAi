@props(['paginator', 'itemLabel' => 'registros'])

@if($paginator->total() > 0)
    <div class="mt-4 flex flex-wrap items-center justify-between gap-4 border-t border-gray-200 dark:border-gray-600 pt-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} {{ $itemLabel }}
            <span class="font-medium text-gray-800 dark:text-gray-200 ml-1">— Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>
        </p>
        <nav class="flex items-center gap-2 flex-wrap" aria-label="Paginação">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-400 dark:text-gray-500 text-sm cursor-not-allowed">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition">Anterior</a>
            @endif
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp
            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}" class="min-w-[2.25rem] px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium text-center hover:bg-gray-300 dark:hover:bg-gray-500 transition">1</a>
                @if ($start > 2) <span class="px-1 text-gray-400 dark:text-gray-500">…</span> @endif
            @endif
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $current)
                    <span class="min-w-[2.25rem] px-3 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold text-center" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="min-w-[2.25rem] px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium text-center hover:bg-gray-300 dark:hover:bg-gray-500 transition">{{ $page }}</a>
                @endif
            @endfor
            @if ($end < $last)
                @if ($end < $last - 1) <span class="px-1 text-gray-400 dark:text-gray-500">…</span> @endif
                <a href="{{ $paginator->url($last) }}" class="min-w-[2.25rem] px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium text-center hover:bg-gray-300 dark:hover:bg-gray-500 transition">{{ $last }}</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition">Próxima</a>
            @else
                <span class="px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-400 dark:text-gray-500 text-sm cursor-not-allowed">Próxima</span>
            @endif
        </nav>
    </div>
@endif
