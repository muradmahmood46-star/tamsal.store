<div class="copy-share-link mt-3 pt-3 border-top" style="min-width: 0;">
    <label for="{{ $shareInputId }}" class="d-block mb-2 font-weight-600 text-dark" style="font-size: 13px;">
        <i class="fas fa-link text-primary mr-1"></i> {{ $shareLabel }}
    </label>
    <div class="input-group" style="max-width: 100%;">
        <input id="{{ $shareInputId }}" type="text" class="form-control bg-white" value="{{ trim($shareUrl) }}" readonly aria-label="{{ $shareLabel }}" style="min-width: 0; font-size: 12.5px; text-align: left; direction: ltr; padding-left: 10px;">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-primary copy-share-link-button" data-copy-target="{{ $shareInputId }}" aria-label="{{ __('Copy link') }}">
                <i class="far fa-copy mr-1"></i><span>{{ __('Copy') }}</span>
            </button>
        </div>
    </div>
    <small class="text-muted d-block mt-1" style="font-size: 11.5px;">{{ __('Copy and share this link. Anyone who opens it will go directly to this page.') }}</small>
</div>

<script>
    (function () {
        if (window.copyShareLinkReady) return;
        window.copyShareLinkReady = true;

        function showCopied(button) {
            var label = button.querySelector('span');
            var icon = button.querySelector('i');
            if (!label || button.dataset.copying) return;
            button.dataset.copying = '1';
            var originalLabel = label.textContent;
            label.textContent = '{{ __('Copied!') }}';
            if (icon) icon.className = 'fas fa-check mr-1';
            setTimeout(function () {
                label.textContent = originalLabel;
                if (icon) icon.className = 'far fa-copy mr-1';
                delete button.dataset.copying;
            }, 1800);
        }

        document.addEventListener('click', function (event) {
            var button = event.target.closest('.copy-share-link-button');
            if (!button) return;
            var input = document.getElementById(button.dataset.copyTarget);
            if (!input) return;
            var text = input.value;
            var fallback = function () {
                input.focus();
                input.select();
                document.execCommand('copy');
                showCopied(button);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function () {
                    showCopied(button);
                }).catch(fallback);
            } else {
                fallback();
            }
        });
    })();
</script>
