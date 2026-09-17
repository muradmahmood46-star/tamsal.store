@extends('master.back')

@section('content')

@php
    $wa_enabled   = isset($setting->whatsapp_enabled) ? $setting->whatsapp_enabled : 0;
    $wa_phone_id  = isset($setting->whatsapp_phone_number_id) ? $setting->whatsapp_phone_number_id : '';
    $wa_token     = isset($setting->whatsapp_access_token) ? $setting->whatsapp_access_token : '';
    $wa_from      = isset($setting->whatsapp_from_number) ? $setting->whatsapp_from_number : '';
    $wa_tpl_confirmed = isset($setting->whatsapp_template_order_confirmed) ? $setting->whatsapp_template_order_confirmed : 'order_confirmed';
    $wa_tpl_progress  = isset($setting->whatsapp_template_in_progress) ? $setting->whatsapp_template_in_progress : 'order_in_progress';
    $wa_tpl_delivered = isset($setting->whatsapp_template_delivered) ? $setting->whatsapp_template_delivered : 'order_delivered';
    $wa_tpl_canceled  = isset($setting->whatsapp_template_canceled) ? $setting->whatsapp_template_canceled : 'order_canceled';
@endphp

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title">
                    <b>
                        <i class="fab fa-whatsapp text-success mr-2" style="font-size:1.3rem;"></i>
                        {{ __('WhatsApp Notification Setting') }}
                    </b>
                </h3>
                <span class="badge badge-{{ $wa_enabled ? 'success' : 'secondary' }} px-3 py-2" style="font-size:13px;">
                    {{ $wa_enabled ? 'ðŸŸ¢ Enabled' : 'âšª Disabled' }}
                </span>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Main Card -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body p-0">
                    <div class="p-4">

                        <!-- Tabs -->
                        <ul class="nav nav-pills nav-secondary nav-justified mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="pill" href="#wa-config">
                                    <i class="fas fa-cog mr-1"></i> {{ __('API Configuration') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#wa-templates">
                                    <i class="fas fa-comment-dots mr-1"></i> {{ __('Message Templates') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#wa-test">
                                    <i class="fas fa-paper-plane mr-1"></i> {{ __('Test Message') }}
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">

                            {{-- â•â•â•â•â•â•â•â•â•â•â•â• TAB 1: API Configuration â•â•â•â•â•â•â•â•â•â•â•â• --}}
                            <div id="wa-config" class="tab-pane fade show active">
                                <div class="row justify-content-center">
                                    <div class="col-lg-8">

                                        <!-- How to Get Credentials Guide -->
                                        <div class="alert alert-info border-0 mb-4" style="background:linear-gradient(135deg,#e0f4ff,#f0fbff); border-left: 4px solid #0288d1 !important; border-radius:10px;">
                                            <h6 class="font-weight-bold mb-2">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                {{ __('How to Get WhatsApp API Credentials (Free)') }}
                                            </h6>
                                            <ol class="mb-0 pl-3" style="font-size:13px; line-height:1.9;">
                                                <li>Go to <a href="https://developers.facebook.com/" target="_blank" class="font-weight-bold text-primary">developers.facebook.com</a> and create a free developer account</li>
                                                <li>Create a new App â†’ Select <strong>Business</strong> type â†’ Add <strong>WhatsApp</strong> product</li>
                                                <li>In WhatsApp â†’ Getting Started â†’ you'll see your <strong>Phone Number ID</strong> and a temporary <strong>Access Token</strong></li>
                                                <li>For permanent token: Go to <strong>System Users</strong> in Business Manager â†’ Generate token with <code>whatsapp_business_messaging</code> permission</li>
                                                <li>Add test numbers to the <strong>Recipient Phone Numbers</strong> whitelist for free testing</li>
                                            </ol>
                                        </div>

                                        <form action="{{ route('back.whatsapp.update') }}" method="POST">
                                            @csrf

                                            <!-- Enable Toggle -->
                                            <div class="form-group mb-4">
                                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background:#f8f9fa; border:1px solid #dee2e6;">
                                                    <div>
                                                        <strong style="font-size:15px;">{{ __('Enable WhatsApp Notifications') }}</strong>
                                                        <p class="text-muted mb-0" style="font-size:12px;">{{ __('Send automatic WhatsApp messages on order events') }}</p>
                                                    </div>
                                                    <label class="switch-primary mb-0">
                                                        <input type="checkbox" class="switch switch-bootstrap status radio-check"
                                                               name="whatsapp_enabled" value="1"
                                                               {{ $wa_enabled == 1 ? 'checked' : '' }}>
                                                        <span class="switch-body"></span>
                                                        <span class="switch-text"></span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- API Fields -->
                                            <div class="form-group">
                                                <label for="whatsapp_phone_number_id">
                                                    <i class="fas fa-hashtag text-success mr-1"></i>
                                                    {{ __('Phone Number ID') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="whatsapp_phone_number_id"
                                                       name="whatsapp_phone_number_id"
                                                       placeholder="{{ __('e.g. 123456789012345') }}"
                                                       value="{{ old('whatsapp_phone_number_id', $wa_phone_id) }}">
                                                <small class="text-muted">{{ __('Found in Meta Developer Console â†’ WhatsApp â†’ Getting Started') }}</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="whatsapp_access_token">
                                                    <i class="fas fa-key text-warning mr-1"></i>
                                                    {{ __('Access Token') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="whatsapp_access_token"
                                                           name="whatsapp_access_token"
                                                           placeholder="{{ __('Enter your permanent access token') }}"
                                                           value="{{ old('whatsapp_access_token', $wa_token) }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-secondary" onclick="toggleToken(this)" title="Show/Hide">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ __('Use a permanent System User token for production (not the 24h temporary token)') }}</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="whatsapp_from_number">
                                                    <i class="fab fa-whatsapp text-success mr-1"></i>
                                                    {{ __('WhatsApp Business Number') }}
                                                </label>
                                                <input type="text" class="form-control" id="whatsapp_from_number"
                                                       name="whatsapp_from_number"
                                                       placeholder="{{ __('e.g. 923001234567 (with country code, no + sign)') }}"
                                                       value="{{ old('whatsapp_from_number', $wa_from) }}">
                                                <small class="text-muted">{{ __('Your registered WhatsApp Business number (for reference only)') }}</small>
                                            </div>

                                            <div class="form-group d-flex justify-content-end mt-4">
                                                <button type="submit" class="btn btn-success px-5">
                                                    <i class="fas fa-save mr-2"></i>{{ __('Save Configuration') }}
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                            {{-- â•â•â•â•â•â•â•â•â•â•â•â• TAB 2: Message Templates â•â•â•â•â•â•â•â•â•â•â•â• --}}
                            <div id="wa-templates" class="tab-pane fade">
                                <div class="row justify-content-center">
                                    <div class="col-lg-8">

                                        <!-- Template Guide -->
                                        <div class="alert border-0 mb-4" style="background:linear-gradient(135deg,#fff8e1,#fffde7); border-left:4px solid #f9a825 !important; border-radius:10px;">
                                            <h6 class="font-weight-bold mb-2">
                                                <i class="fas fa-lightbulb text-warning mr-1"></i>
                                                {{ __('About WhatsApp Message Templates') }}
                                            </h6>
                                            <p style="font-size:13px; margin-bottom:8px;">
                                                WhatsApp Business API requires <strong>pre-approved templates</strong> for outbound messages. You must create and get these templates approved in <a href="https://business.facebook.com/" target="_blank" class="font-weight-bold">Meta Business Manager</a>.
                                            </p>
                                            <div style="background:#fff; border-radius:8px; padding:12px; font-size:12px; margin-top:8px;">
                                                <strong>Template Variables Used:</strong><br>
                                                <code>&#123;&#123;1&#125;&#125;</code> = Customer Name &nbsp;|&nbsp;
                                                <code>&#123;&#123;2&#125;&#125;</code> = Order Number &nbsp;|&nbsp;
                                                <code>&#123;&#123;3&#125;&#125;</code> = Site Name
                                                <hr style="margin:8px 0;">
                                                <strong>Example template body:</strong><br>
                                                <em>"Assalam-o-Alaikum &#123;&#123;1&#125;&#125;! Your order &#123;&#123;2&#125;&#125; from &#123;&#123;3&#125;&#125; has been confirmed. JazakAllah! ðŸŽ‰"</em>
                                            </div>
                                        </div>

                                        <form action="{{ route('back.whatsapp.update') }}" method="POST">
                                            @csrf
                                            <!-- Hidden fields to preserve other settings -->
                                            <input type="hidden" name="whatsapp_enabled" value="{{ $wa_enabled }}">
                                            <input type="hidden" name="whatsapp_phone_number_id" value="{{ $wa_phone_id }}">
                                            <input type="hidden" name="whatsapp_access_token" value="{{ $wa_token }}">
                                            <input type="hidden" name="whatsapp_from_number" value="{{ $wa_from }}">

                                            <!-- Template event cards -->
                                            @php
                                                $events = [
                                                    [
                                                        'key'     => 'whatsapp_template_order_confirmed',
                                                        'label'   => 'Order Confirmed (Payment Accepted)',
                                                        'trigger' => 'Admin clicks: Accept Payment / Mark as Paid',
                                                        'default' => 'order_confirmed',
                                                        'icon'    => 'fas fa-check-circle',
                                                        'color'   => '#28a745',
                                                        'value'   => $wa_tpl_confirmed,
                                                    ],
                                                    [
                                                        'key'     => 'whatsapp_template_in_progress',
                                                        'label'   => 'Order In Progress (Dispatched)',
                                                        'trigger' => 'Admin clicks: Accept Order & Add to Delivery In Progress',
                                                        'default' => 'order_in_progress',
                                                        'icon'    => 'fas fa-shipping-fast',
                                                        'color'   => '#0284c7',
                                                        'value'   => $wa_tpl_progress,
                                                    ],
                                                    [
                                                        'key'     => 'whatsapp_template_delivered',
                                                        'label'   => 'Order Delivered',
                                                        'trigger' => 'Admin sets order status â†’ Delivered',
                                                        'default' => 'order_delivered',
                                                        'icon'    => 'fas fa-box-open',
                                                        'color'   => '#16a34a',
                                                        'value'   => $wa_tpl_delivered,
                                                    ],
                                                    [
                                                        'key'     => 'whatsapp_template_canceled',
                                                        'label'   => 'Order Canceled',
                                                        'trigger' => 'Admin sets order status â†’ Canceled',
                                                        'default' => 'order_canceled',
                                                        'icon'    => 'fas fa-times-circle',
                                                        'color'   => '#dc2626',
                                                        'value'   => $wa_tpl_canceled,
                                                    ],
                                                ];
                                            @endphp

                                            @foreach($events as $event)
                                            <div class="card mb-3 border-0 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start mb-2">
                                                        <i class="{{ $event['icon'] }} mr-2 mt-1" style="color:{{ $event['color'] }}; font-size:16px;"></i>
                                                        <div>
                                                            <strong style="font-size:14px;">{{ $event['label'] }}</strong>
                                                            <p class="text-muted mb-1" style="font-size:12px;">
                                                                <i class="fas fa-bolt mr-1"></i>Trigger: {{ $event['trigger'] }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-white" style="font-size:12px; color:#6b7280;">Template Name</span>
                                                        </div>
                                                        <input type="text" class="form-control"
                                                               name="{{ $event['key'] }}"
                                                               value="{{ old($event['key'], $event['value']) }}"
                                                               placeholder="{{ $event['default'] }}">
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach

                                            <div class="form-group d-flex justify-content-end mt-3">
                                                <button type="submit" class="btn btn-success px-5">
                                                    <i class="fas fa-save mr-2"></i>{{ __('Save Templates') }}
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                            {{-- â•â•â•â•â•â•â•â•â•â•â•â• TAB 3: Test Message â•â•â•â•â•â•â•â•â•â•â•â• --}}
                            <div id="wa-test" class="tab-pane fade">
                                <div class="row justify-content-center">
                                    <div class="col-lg-7">

                                        <div class="text-center mb-4">
                                            <div style="width:70px; height:70px; background:linear-gradient(135deg,#25D366,#128C7E); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                                                <i class="fab fa-whatsapp text-white" style="font-size:32px;"></i>
                                            </div>
                                            <h5 class="font-weight-bold mb-1">{{ __('Send Test WhatsApp') }}</h5>
                                            <p class="text-muted" style="font-size:13px;">
                                                {{ __('Verify your API is working by sending a test message to any number') }}
                                            </p>
                                        </div>

                                        @if(!$wa_phone_id || !$wa_token)
                                        <div class="alert alert-warning text-center">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            {{ __('Please configure your Phone Number ID and Access Token in the Configuration tab first.') }}
                                        </div>
                                        @else

                                        <div class="alert border-0 mb-4" style="background:#f0fff4; border-left:4px solid #25D366 !important; border-radius:10px;">
                                            <strong style="font-size:13px;"><i class="fas fa-info-circle mr-1 text-success"></i>{{ __('Important Notes:') }}</strong>
                                            <ul class="mb-0 mt-1 pl-3" style="font-size:12px; line-height:1.9;">
                                                <li>{{ __('The test number must be added to your Recipient Phone Numbers whitelist in Meta Developer Console') }}</li>
                                                <li>{{ __('Format: country code + number without + sign (e.g. 923001234567 for Pakistani number)') }}</li>
                                                <li>{{ __('Test sends a free-form text (not a template) â€” works only within 24h window or whitelisted numbers') }}</li>
                                            </ul>
                                        </div>

                                        <form action="{{ route('back.whatsapp.test') }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="test_number">
                                                    <i class="fas fa-mobile-alt mr-1"></i>
                                                    {{ __('WhatsApp Number to Test') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fab fa-whatsapp text-success"></i>
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-lg"
                                                           id="test_number" name="test_number"
                                                           placeholder="{{ __('923001234567') }}"
                                                           value="{{ old('test_number') }}"
                                                           style="font-size:16px; letter-spacing:1px;">
                                                </div>
                                                <small class="text-muted">{{ __('With country code, no + or spaces (e.g. 923001234567)') }}</small>
                                            </div>

                                            <div class="form-group text-center mt-4">
                                                <button type="submit" class="btn btn-success btn-lg px-5">
                                                    <i class="fab fa-whatsapp mr-2"></i>{{ __('Send Test Message') }}
                                                </button>
                                            </div>
                                        </form>

                                        <!-- API Status Card -->
                                        <div class="card border-0 mt-4" style="background:#f8fffe;">
                                            <div class="card-body">
                                                <h6 class="font-weight-bold mb-3">
                                                    <i class="fas fa-plug mr-2 text-success"></i>{{ __('Current API Status') }}
                                                </h6>
                                                <table class="table table-sm table-borderless mb-0" style="font-size:13px;">
                                                    <tr>
                                                        <td class="text-muted" width="40%">{{ __('Service') }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $wa_enabled ? 'success' : 'secondary' }}">
                                                                {{ $wa_enabled ? 'ENABLED' : 'DISABLED' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">{{ __('Phone Number ID') }}</td>
                                                        <td>
                                                            @if($wa_phone_id)
                                                                <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ substr($wa_phone_id, 0, 6) }}...{{ substr($wa_phone_id, -4) }}</span>
                                                            @else
                                                                <span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Not set</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">{{ __('Access Token') }}</td>
                                                        <td>
                                                            @if($wa_token)
                                                                <span class="text-success"><i class="fas fa-check-circle mr-1"></i>Configured</span>
                                                            @else
                                                                <span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Not set</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">{{ __('API Provider') }}</td>
                                                        <td><strong>Meta WhatsApp Cloud API v18.0</strong></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>

                        </div>{{-- end tab-content --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Trigger Reference -->
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-body">
            <h6 class="font-weight-bold mb-3">
                <i class="fas fa-bell mr-2 text-warning"></i>{{ __('When are WhatsApp Notifications Sent?') }}
            </h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm" style="font-size:13px;">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('Admin Action') }}</th>
                            <th class="text-center">{{ __('WhatsApp') }}</th>
                            <th class="text-center">{{ __('Email') }}</th>
                            <th>{{ __('Template Used') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="fas fa-money-check-alt text-success mr-1"></i> {{ __('Accept Payment / Mark as Paid') }}</td>
                            <td class="text-center"><span class="badge badge-success">âœ“ Yes</span></td>
                            <td class="text-center"><span class="badge badge-success">âœ“ Yes</span></td>
                            <td><code>{{ $wa_tpl_confirmed }}</code></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-shipping-fast text-primary mr-1"></i> {{ __('Accept Order â†’ In Progress') }}</td>
                            <td class="text-center"><span class="badge badge-success">âœ“ Yes</span></td>
                            <td class="text-center"><span class="badge badge-success">âœ“ Yes</span></td>
                            <td><code>{{ $wa_tpl_progress }}</code></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-box-open text-success mr-1"></i> {{ __('Mark Order as Delivered') }}</td>
                            <td class="text-center"><span class="badge badge-success">âœ“ Yes</span></td>
                            <td class="text-center"><span class="badge badge-success">âœ“ Yes</span></td>
                            <td><code>{{ $wa_tpl_delivered }}</code></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-times-circle text-danger mr-1"></i> {{ __('Cancel Order') }}</td>
                            <td class="text-center"><span class="badge badge-success">âœ“ Yes</span></td>
                            <td class="text-center"><span class="badge badge-success">âœ“ Yes</span></td>
                            <td><code>{{ $wa_tpl_canceled }}</code></td>
                        </tr>
                        <tr class="table-secondary">
                            <td><i class="fas fa-shopping-cart text-muted mr-1"></i> {{ __('Customer Places Order') }}</td>
                            <td class="text-center"><span class="badge badge-secondary">âœ— No</span></td>
                            <td class="text-center"><span class="badge badge-secondary">âœ— No</span></td>
                            <td class="text-muted">â€”</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function toggleToken(btn) {
    const input = document.getElementById('whatsapp_access_token');
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
@endpush