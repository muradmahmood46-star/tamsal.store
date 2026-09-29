<script>
(function() {
    function checkEmptyDealContainers() {
        // 1. Homepage & Widget sections
        document.querySelectorAll('.flash-sell-new-section, .flash-deals-widget').forEach(function(section) {
            var allCols = section.querySelectorAll('.deal-card');
            var anyVisible = false;
            allCols.forEach(function(c) {
                var col = c.closest('.col-lg-3, .col-md-4, .col-sm-6, .col-6, [class*="col-"]');
                if (col && col.style.display !== 'none') {
                    anyVisible = true;
                }
            });
            if (allCols.length > 0 && !anyVisible) {
                section.style.display = 'none';
            }
        });

        // 2. All Bundles listing page container
        var indexContainer = document.querySelector('.deals-index-container');
        if (indexContainer) {
            var indexCards = indexContainer.querySelectorAll('.deal-card');
            var hasActiveCard = false;
            indexCards.forEach(function(c) {
                var col = c.closest('.col-lg-3, .col-md-4, .col-sm-6, .col-6, [class*="col-"]');
                if (col && col.style.display !== 'none') {
                    hasActiveCard = true;
                }
            });
            if (indexCards.length > 0 && !hasActiveCard) {
                var emptyAlert = indexContainer.querySelector('.empty-deals-alert');
                if (emptyAlert) emptyAlert.style.display = 'block';
                var dealsRow = indexContainer.querySelector('.deals-row');
                if (dealsRow) dealsRow.style.display = 'none';
            }
        }
    }

    function updateDealCountdowns() {
        document.querySelectorAll('[data-deal-end]').forEach(function (card) {
            if (!card.dataset.dealEnd) return;
            var endMs = new Date(card.dataset.dealEnd).getTime();
            if (isNaN(endMs)) return;
            var diff = endMs - Date.now();

            if (diff <= 0) {
                // Expired!
                if (card.classList.contains('container')) {
                    // Single deal detail page
                    var cdTarget = card.querySelector('.deal-countdown');
                    if (cdTarget) cdTarget.textContent = 'Expired';
                    var buyForms = card.querySelectorAll('form[action*="add_to_cart"], button[type="submit"]');
                    buyForms.forEach(function(el) {
                        if (el.tagName === 'BUTTON') {
                            el.disabled = true;
                            el.classList.add('disabled');
                        }
                    });
                    if (!card.querySelector('.deal-expired-banner')) {
                        var banner = document.createElement('div');
                        banner.className = 'deal-expired-banner alert alert-warning font-weight-bold text-center mt-3';
                        banner.innerHTML = '<i class="icon-alert-circle"></i> This bundle offer has expired and is no longer available.';
                        var cardBody = card.querySelector('.card-body');
                        if (cardBody) cardBody.appendChild(banner);
                    }
                } else {
                    // Product / Deal Card in a grid
                    var col = card.closest('.col-lg-3, .col-md-4, .col-sm-6, .col-6, [class*="col-"]');
                    if (col) {
                        col.style.display = 'none';
                        checkEmptyDealContainers();
                    } else {
                        card.style.display = 'none';
                        checkEmptyDealContainers();
                    }
                }
                return;
            }

            var seconds = Math.floor(diff / 1000);
            var days = Math.floor(seconds / 86400); seconds %= 86400;
            var hours = Math.floor(seconds / 3600); seconds %= 3600;
            var minutes = Math.floor(seconds / 60); seconds %= 60;
            
            var timeParts = [];
            if (days > 0) {
                timeParts.push(days + 'd');
            }
            timeParts.push((hours < 10 ? '0' + hours : hours) + 'h');
            timeParts.push((minutes < 10 ? '0' + minutes : minutes) + 'm');
            timeParts.push((seconds < 10 ? '0' + seconds : seconds) + 's');
            
            var value = timeParts.join(' ');
            card.querySelectorAll('.deal-countdown').forEach(function (target) { target.textContent = value; });
        });
    }

    updateDealCountdowns();
    setInterval(updateDealCountdowns, 1000);
})();
</script>
