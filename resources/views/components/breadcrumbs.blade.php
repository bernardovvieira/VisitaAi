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
        <nav aria-label="{{ __('Navegação estrutural') }}" class="mb-6">
            <ol class="flex flex-wrap items-center gap-x-1 gap-y-1 text-sm">
                @foreach($items as $i => $item)
                    @if($i > 0)
                        <li class="flex items-center text-slate-300 dark:text-slate-600" aria-hidden="true">
                            <x-heroicon-o-chevron-right class="h-4 w-4 shrink-0" />
                        </li>
                    @endif
                    <li class="min-w-0">
                        @if(! empty($item['url']) && $i < $lastIndex)
                            <a href="{{ $item['url'] }}"
                               class="truncate font-medium text-slate-600 underline-offset-4 transition hover:text-emerald-700 hover:underline dark:text-slate-400 dark:hover:text-emerald-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 rounded-sm">
                                {{ $item['label'] }}
                            </a>
                        @else
                            <span class="truncate font-semibold text-slate-900 dark:text-slate-100" @if($i === $lastIndex) aria-current="page" @endif>
                                {{ $item['label'] }}
                            </span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif
@endif
