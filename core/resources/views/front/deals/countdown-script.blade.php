<script>
function updateDealCountdowns() {
    document.querySelectorAll('[data-deal-end]').forEach(function (card) {
        if (!card.dataset.dealEnd) return;
        var endMs = new Date(card.dataset.dealEnd).getTime();
        if (isNaN(endMs)) return;
        var seconds = Math.max(0, Math.floor((endMs - Date.now()) / 1000));
        var days = Math.floor(seconds / 86400); seconds %= 86400;
        var hours = Math.floor(seconds / 3600); seconds %= 3600;
        var minutes = Math.floor(seconds / 60); seconds %= 60;
        var value = (days ? days + 'd ' : '') + hours + 'h ' + minutes + 'm ' + seconds + 's';
        card.querySelectorAll('.deal-countdown').forEach(function (target) { target.textContent = value; });
    });
}
updateDealCountdowns(); setInterval(updateDealCountdowns, 1000);
</script>
