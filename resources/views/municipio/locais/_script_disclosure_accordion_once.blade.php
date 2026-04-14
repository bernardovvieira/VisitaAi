@once
<script>
(function () {
    if (window.__visitaaiDisclosureAccordionInit) {
        return;
    }
    window.__visitaaiDisclosureAccordionInit = true;
    document.addEventListener('toggle', function (e) {
        var el = e.target;
        if (!el || el.tagName !== 'DETAILS') {
            return;
        }
        var group = el.getAttribute('data-disclosure-accordion');
        if (!group || !el.open) {
            return;
        }
        document.querySelectorAll('details[data-disclosure-accordion]').forEach(function (other) {
            if (other !== el && other.getAttribute('data-disclosure-accordion') === group) {
                other.removeAttribute('open');
            }
        });
    }, true);
})();
</script>
@endonce
