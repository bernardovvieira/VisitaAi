@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-700'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative dropdown-js" data-dropdown>
    <div class="cursor-pointer dropdown-trigger" role="button" tabindex="0" aria-haspopup="true">
        {{ $trigger }}
    </div>
    <div class="dropdown-panel absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}" style="display: none;">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}" data-dropdown-close>
            {{ $content }}
        </div>
    </div>
</div>

<script>
(function() {
    if (window._dropdownJsInit) return;
    window._dropdownJsInit = true;
    function closePanel(p) {
        p.classList.remove('dropdown-open');
        function hide() {
            p.style.display = 'none';
            p.removeEventListener('transitionend', onEnd);
            clearTimeout(fb);
        }
        function onEnd() { hide(); }
        p.addEventListener('transitionend', onEnd);
        var fb = setTimeout(hide, 250);
    }
    function openPanel(p) {
        p.style.display = 'block';
        p.classList.remove('dropdown-open');
        p.offsetHeight;
        p.classList.add('dropdown-open');
    }
    function closeAllPanels() {
        document.querySelectorAll('.dropdown-panel').forEach(function(p) {
            p.classList.remove('dropdown-open');
            p.style.display = 'none';
        });
    }
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-dropdown]')) return;
        closeAllPanels();
    });
    function init() {
        document.querySelectorAll('[data-dropdown]').forEach(function(wrap) {
            if (wrap._dropdownInit) return;
            wrap._dropdownInit = true;
            var trigger = wrap.querySelector('.dropdown-trigger');
            var panel = wrap.querySelector('.dropdown-panel');
            if (!trigger || !panel) return;
            panel.style.display = 'none';
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var isOpen = panel.style.display === 'block' && panel.classList.contains('dropdown-open');
                closeAllPanels();
                if (!isOpen) openPanel(panel);
            });
            var closer = panel.querySelector('[data-dropdown-close]');
            if (closer) closer.addEventListener('click', function() {
                closePanel(panel);
            });
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
</script>
