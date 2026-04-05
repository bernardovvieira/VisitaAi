@props(['items' => []])

@if(count($items) > 0)
    @php
        $valid = true;
        foreach ($items as $i => $item) {
            if (! is_array($item) || empty($item['label'] ?? null)) {
                $valid = false;
                break;
            }
        }
        $lastIndex = count($items) - 1;
    @endphp
    @if($valid)
        <nav aria-label="{{ __('Navegação estrutural') }}" class="v-breadcrumbs">
            <ol class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-[11px] tracking-wide sm:text-xs">
                @foreach($items as $i => $item)
                    @if($i > 0)
                        <li class="flex items-center text-slate-300 dark:text-slate-600" aria-hidden="true">
                            <x-heroicon-o-chevron-right class="h-3 w-3 shrink-0 opacity-90" />
                        </li>
                    @endif
                    <li class="min-w-0">
                        @if(! empty($item['url']) && $i < $lastIndex)
                            <a href="{{ $item['url'] }}"
                               class="truncate font-medium text-slate-500 underline-offset-4 transition hover:text-slate-800 hover:underline dark:text-slate-400 dark:hover:text-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 focus-visible:ring-offset-1 rounded-sm dark:focus-visible:ring-offset-slate-900">
                                {{ $item['label'] }}
                            </a>
                        @else
                            <span class="truncate font-medium text-slate-700 dark:text-slate-300" @if($i === $lastIndex) aria-current="page" @endif>
                                {{ $item['label'] }}
                            </span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif
@endif
