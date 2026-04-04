@props(['paginator', 'itemLabel' => 'registros'])

@if($paginator->total() > 0)
    <div class="v-pagination-bar">
        <p class="v-pagination-meta">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} {{ $itemLabel }}
            <strong class="ml-1 font-semibold text-slate-800 dark:text-slate-200">· {{ __('Página') }} {{ $paginator->currentPage() }} {{ __('de') }} {{ $paginator->lastPage() }}</strong>
        </p>
        <nav class="flex flex-wrap items-center gap-2" aria-label="Paginação">
            @if ($paginator->onFirstPage())
                <span class="v-pagination-btn v-pagination-btn--disabled">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="v-pagination-btn">Anterior</a>
            @endif
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp
            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}" class="v-pagination-btn min-w-[2.25rem] text-center">1</a>
                @if ($start > 2) <span class="px-1 text-slate-400 dark:text-slate-500">…</span> @endif
            @endif
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $current)
                    <span class="v-pagination-btn v-pagination-btn--current min-w-[2.25rem] text-center" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="v-pagination-btn min-w-[2.25rem] text-center">{{ $page }}</a>
                @endif
            @endfor
            @if ($end < $last)
                @if ($end < $last - 1) <span class="px-1 text-slate-400 dark:text-slate-500">…</span> @endif
                <a href="{{ $paginator->url($last) }}" class="v-pagination-btn min-w-[2.25rem] text-center">{{ $last }}</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="v-pagination-btn">Próxima</a>
            @else
                <span class="v-pagination-btn v-pagination-btn--disabled">Próxima</span>
            @endif
        </nav>
    </div>
@endif
