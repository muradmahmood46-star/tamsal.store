@extends('master.back')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h3 class="mb-0 bc-title"><b><i class="fas fa-envelope-open-text text-primary mr-2"></i>{{ __('Email Settings & Notifications') }}</b></h3>
                </div>
            </div>
        </div>

        @include('alerts.alerts')

        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="card o-hidden border-0 shadow-lg">
                    <div class="card-body p-4">
                        <div id="tabs">
                            <ul class="nav nav-pills nav-secondary mb-4" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active font-weight-bold" data-toggle="pill" href="#conf">
                                        <i class="fas fa-cogs mr-1"></i> {{ __('SMTP Configuration') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold" data-toggle="pill" href="#test">
                                        <i class="fas fa-paper-plane mr-1"></i> {{ __('Test Email') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold" data-toggle="pill" href="#template">
                                        <i class="fas fa-file-alt mr-1"></i> {{ __('Email Templates') }}
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <!-- CONFIGURATION TAB -->
                                <div id="conf" class="tab-pane active">
                                    <div class="row">
                                        <!-- Left Column: Settings Form -->
                                        <div class="col-lg-8">
                                            <form class="admin-form" action="{{ route('back.email.update') }}" method="POST">
                                                @csrf

                                                <!-- Gmail Quick Helper Banner -->
                                                <div class="alert alert-info border-left-info shadow-sm mb-4">
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <h6 class="font-weight-bold text-dark mb-1"><i class="fab fa-google text-danger mr-1"></i> {{ __('Using Gmail for Email Notifications?') }}</h6>
                                                            <p class="mb-0 text-muted" style="font-size: 13px;">
                                                                {{ __('You can auto-fill Gmail SMTP credentials with 1 click.') }}
                                                            </p>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 mt-sm-0" id="btn-fill-gmail">
                                                            <i class="fas fa-magic mr-1"></i> {{ __('Fill Gmail Defaults') }}
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="card border mb-4">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-sliders-h mr-1"></i> {{ __('Notification Toggles') }}</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group mb-3">
                                                            <label class="switch-primary d-flex align-items-center">
                                                                <input type="checkbox" class="switch switch-bootstrap status radio-check" name="smtp_check" value="1" {{ $setting->smtp_check == 1 ? 'checked' : '' }}>
                                                                <span class="switch-body mr-2"></span>
                                                                <span class="switch-text font-weight-bold">{{ __('Enable SMTP Service') }}</span>
                                                            </label>
                                                            <small class="text-muted d-block mt-1">{{ __('Turn this ON to send emails via Gmail or your custom SMTP server.') }}</small>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label class="switch-primary d-flex align-items-center">
                                                                <input type="checkbox" class="switch switch-bootstrap" name="order_mail" value="1" {{ $setting->order_mail == 1 ? 'checked' : '' }}>
                                                                <span class="switch-body mr-2"></span>
                                                                <span class="switch-text font-weight-bold">{{ __('Admin Order Notification Email') }}</span>
                                                            </label>
                                                            <small class="text-muted d-block mt-1">{{ __('When a customer places an order, notify store admin via email.') }}</small>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label class="switch-primary d-flex align-items-center">
                                                                <input type="checkbox" class="switch switch-bootstrap" name="ticket_mail" value="1" {{ $setting->ticket_mail == 1 ? 'checked' : '' }}>
                                                                <span class="switch-body mr-2"></span>
                                                                <span class="switch-text font-weight-bold">{{ __('Support Ticket Reply Email') }}</span>
                                                            </label>
                                                            <small class="text-muted d-block mt-1">{{ __('Send email notifications when support ticket is replied.') }}</small>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label class="switch-primary d-flex align-items-center">
                                                                <input type="checkbox" class="switch switch-bootstrap status" name="is_mail_verify" value="1" {{ $setting->is_mail_verify == 1 ? 'checked' : '' }}>
                                                                <span class="switch-body mr-2"></span>
                                                                <span class="switch-text font-weight-bold">{{ __('Enable User Email Verification') }}</span>
                                                            </label>
                                                            <small class="text-muted d-block mt-1">{{ __('Require customers to verify their email address upon registration.') }}</small>
                                                        </div>

                                                        <div class="form-group mb-0">
                                                            <label class="switch-primary d-flex align-items-center">
                                                                <input type="checkbox" class="switch switch-bootstrap" name="is_queue_enabled" value="1" {{ $setting->is_queue_enabled == 1 ? 'checked' : '' }}>
                                                                <span class="switch-body mr-2"></span>
                                                                <span class="switch-text font-weight-bold">{{ __('Send Mail via Background Queue') }}</span>
                                                            </label>
                                                            <small class="text-muted d-block mt-1">{{ __('Keep disabled (recommended on shared hosting) for immediate direct email sending.') }}</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- SMTP CREDENTIALS -->
                                                <div class="radio-show {{ $setting->smtp_check == 0 ? 'd-none' : '' }}">
                                                    <div class="card border mb-4">
                                                        <div class="card-header bg-light py-2">
                                                            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-server mr-1"></i> {{ __('SMTP Server Details') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6 form-group">
                                                                    <label for="email_host" class="font-weight-bold">{{ __('SMTP Host') }} <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" id="email_host" name="email_host" placeholder="smtp.gmail.com" value="{{ $setting->email_host }}">
                                                                    <small class="text-muted">{{ __('For Gmail: smtp.gmail.com') }}</small>
                                                                </div>

                                                                <div class="col-md-3 form-group">
                                                                    <label for="email_port" class="font-weight-bold">{{ __('SMTP Port') }} <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" id="email_port" name="email_port" placeholder="587" value="{{ $setting->email_port }}">
                                                                    <small class="text-muted">{{ __('587 (TLS) or 465 (SSL)') }}</small>
                                                                </div>

                                                                <div class="col-md-3 form-group">
                                                                    <label for="email_encryption" class="font-weight-bold">{{ __('SMTP Encryption') }} <span class="text-danger">*</span></label>
                                                                    <select class="form-control" id="email_encryption" name="email_encryption">
                                                                        <option value="tls" {{ strtolower($setting->email_encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                                                        <option value="ssl" {{ strtolower($setting->email_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6 form-group">
                                                                    <label for="email_user" class="font-weight-bold">{{ __('SMTP Username (Email)') }} <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" id="email_user" name="email_user" placeholder="yourstore@gmail.com" value="{{ $setting->email_user }}">
                                                                    <small class="text-muted">{{ __('Your full Gmail or SMTP email address') }}</small>
                                                                </div>

                                                                <div class="col-md-6 form-group">
                                                                    <label for="email_pass" class="font-weight-bold">{{ __('SMTP Password / App Password') }} <span class="text-danger">*</span></label>
                                                                    <input type="password" class="form-control" id="email_pass" name="email_pass" placeholder="16-character App Password" value="{{ $setting->email_pass }}">
                                                                    <small class="text-muted">{{ __('For Gmail, use a 16-character Google App Password') }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- SENDER & CONTACT INFO -->
                                                <div class="card border mb-4">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-id-card mr-1"></i> {{ __('Sender & Store Contact Info') }}</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label for="email_from" class="font-weight-bold">{{ __('Email From (Sender Address)') }} <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control" id="email_from" name="email_from" placeholder="noreply@namartzone.store" value="{{ $setting->email_from }}">
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="email_from_name" class="font-weight-bold">{{ __('Email From Name (Brand Name)') }} <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="email_from_name" name="email_from_name" placeholder="Namartzone" value="{{ $setting->email_from_name }}">
                                                            </div>

                                                            <div class="col-md-12 form-group mb-0">
                                                                <label for="contact_email" class="font-weight-bold">{{ __('Admin Contact / Notification Email') }} <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control" id="contact_email" name="contact_email" placeholder="admin@namartzone.store" value="{{ $setting->contact_email }}">
                                                                <small class="text-muted">{{ __('Admin will receive order alerts, contact inquiries, and ticket alerts at this email.') }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-0">
                                                    <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow-sm">
                                                        <i class="fas fa-save mr-1"></i> {{ __('Save Settings') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Right Column: Gmail Setup Guide -->
                                        <div class="col-lg-4">
                                            <div class="card shadow-sm border-0 bg-light">
                                                <div class="card-header bg-primary text-white py-3">
                                                    <h6 class="m-0 font-weight-bold"><i class="fab fa-google mr-1"></i> {{ __('How to setup Gmail SMTP') }}</h6>
                                                </div>
                                                <div class="card-body" style="font-size: 13px; line-height: 1.6;">
                                                    <p class="mb-2"><strong>Gmail requires an "App Password"</strong> instead of your normal account password:</p>
                                                    <ol class="pl-3 mb-3 text-dark">
                                                        <li class="mb-2">Open your <a href="https://myaccount.google.com/security" target="_blank" class="text-primary font-weight-bold">Google Account Security <i class="fas fa-external-link-alt" style="font-size: 10px;"></i></a>.</li>
                                                        <li class="mb-2">Ensure <strong>2-Step Verification</strong> is turned <strong>ON</strong>.</li>
                                                        <li class="mb-2">Search for <strong>"App Passwords"</strong> or visit <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-primary font-weight-bold">App Passwords <i class="fas fa-external-link-alt" style="font-size: 10px;"></i></a>.</li>
                                                        <li class="mb-2">Enter an app name like <code>Namartzone Store</code> and click <strong>Create</strong>.</li>
                                                        <li class="mb-2">Copy the generated <strong>16-letter password</strong> (e.g. <code>abcd efgh ijkl mnop</code>).</li>
                                                        <li>Paste it into the <strong>SMTP Password</strong> field on this page (spaces will be automatically cleaned).</li>
                                                    </ol>

                                                    <div class="alert alert-secondary p-2 mb-0" style="font-size: 12px;">
                                                        <strong><i class="fas fa-info-circle text-info"></i> {{ __('Standard Gmail Values:') }}</strong><br>
                                                        • Host: <code>smtp.gmail.com</code><br>
                                                        • Port: <code>587</code> (or <code>465</code>)<br>
                                                        • Encryption: <code>TLS</code> (or <code>SSL</code>)
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TEST EMAIL TAB -->
                                <div id="test" class="tab-pane">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <div class="card border">
                                                <div class="card-header bg-light py-3">
                                                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-vial text-primary mr-1"></i> {{ __('Send a Test Email') }}</h6>
                                                </div>
                                                <div class="card-body p-4">
                                                    <p class="text-muted mb-4">
                                                        {{ __('Verify that your SMTP configuration is connected and functioning properly. Enter your email below to send a live test message.') }}
                                                    </p>
                                                    <form action="{{ route('back.email.test') }}" method="POST">
                                                        @csrf
                                                        <div class="form-group mb-3">
                                                            <label for="test_email" class="font-weight-bold">{{ __('Recipient Email Address') }} <span class="text-danger">*</span></label>
                                                            <input type="email" class="form-control form-control-lg" id="test_email" name="test_email" placeholder="your-email@gmail.com" required>
                                                            <small class="text-muted">{{ __('Enter any email address where you want to receive the test message.') }}</small>
                                                        </div>
                                                        <button type="submit" class="btn btn-success btn-block btn-lg font-weight-bold shadow-sm">
                                                            <i class="fas fa-paper-plane mr-1"></i> {{ __('Send Test Email Now') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TEMPLATES TAB -->
                                <div id="template" class="tab-pane">
                                    <div class="card shadow mb-4">
                                        <div class="card-body">
                                            <div class="gd-responsive-table">
                                                <table class="table table-bordered table-striped" id="admin-table" width="100%" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th width="25%">{{ __('Template Type') }}</th>
                                                            <th width="55%">{{ __('Subject') }}</th>
                                                            <th width="20%">{{ __('Actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($datas as $data)
                                                            <tr>
                                                                <td class="font-weight-bold">{{ $data->type }}</td>
                                                                <td>{{ $data->subject }}</td>
                                                                <td>
                                                                    <div class="action-list">
                                                                        <a class="btn btn-secondary btn-sm" href="{{ route('back.template.edit', $data->id) }}">
                                                                            <i class="fas fa-edit mr-1"></i> {{ __('Edit Template') }}
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Gmail Auto-fill Helper
            $('#btn-fill-gmail').on('click', function() {
                $('#email_host').val('smtp.gmail.com');
                $('#email_port').val('587');
                $('#email_encryption').val('tls');
                if (!$('input[name="smtp_check"]').is(':checked')) {
                    $('input[name="smtp_check"]').prop('checked', true).trigger('change');
                    $('.radio-show').removeClass('d-none');
                }
                var user = $('#email_user').val();
                if (user && !$('#email_from').val()) {
                    $('#email_from').val(user);
                }
                alert('Gmail SMTP defaults (Host: smtp.gmail.com, Port: 587, Encryption: TLS) filled! Please enter your Gmail address and 16-character Google App Password.');
            });
        });
    </script>
    @endpush
@endsection
