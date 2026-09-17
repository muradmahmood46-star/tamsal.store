@extends('master.back')
@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <h3 class="mb-0 px-3 py-4"><b>{{ __('Checkout Popup Message') }}</b></h3>
    </div>
    @include('alerts.alerts')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('back.checkout.message.update') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label><b>Message</b></label>
                    <textarea name="message" class="form-control" rows="5" required>{{ $message->message ?? '' }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save Message</button>
            </form>
        </div>
    </div>
    <div class="card mt-4">
        <h3 class="mb-0 px-3 py-4"><b>{{ __('Global Popup Message and Links') }}</b></h3>
        <div class="card-body">
            <form action="{{ route('back.global.popup.update') }}" method="POST">
                @csrf
                <div class="form-group mb-4">
                    <label class="switch-primary">
                        <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_enabled" value="1" {{ isset($global_popup) && $global_popup->is_enabled ? 'checked' : '' }}>
                        <span class="switch-body"></span>
                        <span class="switch-text"><b>{{ __('Enable Global Popup') }}</b></span>
                    </label>
                </div>

                <div class="form-group">
                    <label><b>Message Content</b></label>
                    <textarea name="message" class="form-control" rows="5" style="resize: none;">{{ $global_popup->message ?? '' }}</textarea>
                </div>

                <!-- WhatsApp -->
                <div class="form-group mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="mb-0"><b>WhatsApp Channel Link</b></label>
                        <label class="switch-primary mb-0">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_whatsapp_enabled" value="1" {{ !isset($global_popup) || $global_popup->is_whatsapp_enabled ? 'checked' : '' }}>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Show/Hide') }}</span>
                        </label>
                    </div>
                    <input type="url" name="whatsapp_link" class="form-control" value="{{ $global_popup->whatsapp_link ?? '' }}">
                </div>

                <!-- Support -->
                <div class="form-group mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="mb-0"><b>Customer Support Link</b></label>
                        <label class="switch-primary mb-0">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_support_enabled" value="1" {{ !isset($global_popup) || $global_popup->is_support_enabled ? 'checked' : '' }}>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Show/Hide') }}</span>
                        </label>
                    </div>
                    <input type="url" name="support_link" class="form-control" value="{{ $global_popup->support_link ?? '' }}">
                </div>

                <!-- TikTok -->
                <div class="form-group mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="mb-0"><b>TikTok Link</b></label>
                        <label class="switch-primary mb-0">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_tiktok_enabled" value="1" {{ !isset($global_popup) || $global_popup->is_tiktok_enabled ? 'checked' : '' }}>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Show/Hide') }}</span>
                        </label>
                    </div>
                    <input type="url" name="tiktok_link" class="form-control" value="{{ $global_popup->tiktok_link ?? '' }}">
                </div>

                <!-- YouTube -->
                <div class="form-group mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="mb-0"><b>YouTube Link</b></label>
                        <label class="switch-primary mb-0">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_youtube_enabled" value="1" {{ !isset($global_popup) || $global_popup->is_youtube_enabled ? 'checked' : '' }}>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Show/Hide') }}</span>
                        </label>
                    </div>
                    <input type="url" name="youtube_link" class="form-control" value="{{ $global_popup->youtube_link ?? '' }}">
                </div>

                <!-- Facebook -->
                <div class="form-group mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="mb-0"><b>Facebook Link</b></label>
                        <label class="switch-primary mb-0">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_facebook_enabled" value="1" {{ !isset($global_popup) || $global_popup->is_facebook_enabled ? 'checked' : '' }}>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Show/Hide') }}</span>
                        </label>
                    </div>
                    <input type="url" name="facebook_link" class="form-control" value="{{ $global_popup->facebook_link ?? '' }}">
                </div>

                <!-- Telegram -->
                <div class="form-group mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="mb-0"><b>Telegram Link</b></label>
                        <label class="switch-primary mb-0">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_telegram_enabled" value="1" {{ !isset($global_popup) || $global_popup->is_telegram_enabled ? 'checked' : '' }}>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Show/Hide') }}</span>
                        </label>
                    </div>
                    <input type="url" name="telegram_link" class="form-control" value="{{ $global_popup->telegram_link ?? '' }}">
                </div>

                <button type="submit" class="btn btn-primary mt-4">Save Global Popup Settings</button>
            </form>
        </div>
    </div>
</div>
@endsection