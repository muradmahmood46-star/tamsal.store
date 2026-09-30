@extends('master.front')
@section('meta')
<meta name="keywords" content="{{$setting->meta_keywords}}">
<meta name="description" content="{{$setting->meta_description}}">
@endsection
@section('title')
    {{__('Contact')}}
@endsection

@section('content')
    <!-- Page Title-->
<div class="page-title">
    <div class="container">
      <div class="row">
          <div class="col-lg-12">
            <ul class="breadcrumbs">
                <li><a href="{{route('front.index')}}">{{ __('Home') }}</a> </li>
                <li class="separator"></li>
                <li>{{ __('Contact Us') }}</li>
              </ul>
          </div>
      </div>
    </div>
  </div>
  <!-- Page Content-->
  <div class="container padding-bottom-3x mb-1 contact-page">
    <div class="row">
      <div class="col-lg-4 col-md-5 col-sm-5 order-lg-1 order-md-2 order-sm-2">

        <!-- Widget Contacts-->
        <section class="widget widget-featured-posts card rounded p-4 mb-4 shadow-sm">
          <h3 class="widget-title padding-bottom-1x font-weight-bold">{{__('Working Days')}}</h3>
          <ul class="list-unstyled text-sm">
            <li class="mb-2"><span class="text-muted font-weight-bold">{{__('Monday-Friday')}}: </span>{{$setting->friday_start}} - {{$setting->friday_end}}</li>
            <li><span class="text-muted font-weight-bold">{{__('Saturday')}}: </span>{{$setting->satureday_start}} - {{$setting->satureday_end}}</li>
          </ul>
        </section>

        <!-- Widget Address-->
        <section class="widget widget-featured-posts card rounded p-4 shadow-sm">
          <h3 class="widget-title padding-bottom-1x font-weight-bold">{{__('Store address')}}</h3>
          <p class="text-muted text-sm">{{__('Our address information')}}</p>
          <ul class="list-icon margin-bottom-1x text-sm">
            @if(!empty($setting->footer_address))
            <li class="mb-2"> <i class="icon-map-pin text-primary mr-2"></i>{{$setting->footer_address}}</li>
            @endif
            @if(!empty($setting->footer_phone))
            <li class="mb-2"> <i class="icon-phone text-primary mr-2"></i>{{$setting->footer_phone}}</li>
            @endif
            <li class="mb-2"> 
              <a href="https://api.whatsapp.com/send?phone=923348128646" target="_blank" class="text-success font-weight-bold text-decoration-none d-flex align-items-center">
                <i class="fab fa-whatsapp text-success mr-2" style="font-size: 18px;"></i> 0334 8128646
              </a>
            </li>
          </ul>

          @php
          $links = json_decode($setting->social_link,true)['links'] ?? [];
          $icons = json_decode($setting->social_link,true)['icons'] ?? [];
          @endphp

          @if(!empty($links))
          <div class="mt-3 pt-2 border-top">
            @foreach ($links as $link_key => $link)
            <a class="social-button shape-circle sb-facebook mr-2 mb-2" href="{{$link}}" data-toggle="tooltip" data-placement="top"><i class="{{$icons[$link_key] ?? 'icon-share'}}"></i></a>
            @endforeach
          </div>
          @endif
        </section>
      </div>

      <div class="col-lg-8 col-md-7 col-sm-7 order-lg-2 order-md-1 order-sm-1">
        <div class="contact-form-box card p-4 shadow-sm" style="border-radius: 12px;">

            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                <h2 class="h4 mb-0 font-weight-bold">{{ __('Get In Touch With Us') }}</h2>
                <a href="https://api.whatsapp.com/send?phone=923348128646" target="_blank" class="badge badge-success px-3 py-2 text-decoration-none" style="background-color: #25D366; font-size: 13px;">
                    <i class="fab fa-whatsapp mr-1"></i> WhatsApp: 0334 8128646
                </a>
            </div>

            <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-4" style="background: #e8f5e9; border: 1px solid #c8e6c9; color: #1b5e20; border-radius: 8px; font-size: 13.5px;">
                <i class="fab fa-whatsapp mr-2" style="font-size: 20px; color: #25D366;"></i>
                <span>{{ __('Fill in your details below and click Send Message to instantly connect with us on WhatsApp with all your details auto-filled.') }}</span>
            </div>

            <form class="row" method="Post" action="{{route('front.contact.submit')}}" id="contact-page-form">
                @csrf
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="first-name" class="font-weight-bold text-sm">{{__('First Name')}} <span class="text-danger">*</span></label>
                    <input class="form-control form-control-rounded" name="first_name" type="text" id="first-name" placeholder="{{__('First Name')}}" required value="{{ Auth::check() ? Auth::user()->first_name : '' }}">
                    @error('first_name')
                    <p class="text-danger small mt-1">{{$message}}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="last-name" class="font-weight-bold text-sm">{{__('Last Name')}}</label>
                    <input class="form-control form-control-rounded" name="last_name" type="text" id="last-name" placeholder="{{__('Last Name')}}" value="{{ Auth::check() ? Auth::user()->last_name : '' }}">
                    @error('last_name')
                    <p class="text-danger small mt-1">{{$message}}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="contact-email" class="font-weight-bold text-sm">{{__('E-mail')}} <span class="text-danger">*</span></label>
                    <input class="form-control form-control-rounded" type="email" name="email" id="contact-email" placeholder="{{__('E-mail')}}" required value="{{ Auth::check() ? Auth::user()->email : '' }}">
                    @error('email')
                    <p class="text-danger small mt-1">{{$message}}</p>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="contact-tel" class="font-weight-bold text-sm">{{__('Phone')}} <span class="text-danger">*</span></label>
                    <input class="form-control form-control-rounded" type="text" name="phone" id="contact-tel" placeholder="{{__('Phone Number')}}" required value="{{ Auth::check() ? Auth::user()->phone : '' }}">
                    @error('phone')
                    <p class="text-danger small mt-1">{{$message}}</p>
                    @enderror
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group mb-3">
                    <label for="message-text" class="font-weight-bold text-sm">{{__('Message')}} <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-rounded" rows="6" name="message" id="message-text" placeholder="{{__('Write your message or inquiry here...')}}" required></textarea>
                    @error('message')
                    <p class="text-danger small mt-1">{{$message}}</p>
                    @enderror
                  </div>
                </div>
                <input type="text" name="honeypot" id="honeypot" value="" style="display:none;">
                @if ($setting->recaptcha == 1)
                <div class="col-lg-12 mb-3">
                    {!! NoCaptcha::renderJs() !!}
                    {!! NoCaptcha::display() !!}
                    @if ($errors->has('g-recaptcha-response'))
                    @php
                        $errmsg = $errors->first('g-recaptcha-response');
                    @endphp
                    <p class="text-danger mb-0">{{__("$errmsg")}}</p>
                    @endif
                </div>
                @endif

                <div class="col-12 text-right mt-2">
                  <button class="btn btn-success" type="submit" id="contact-submit-btn" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); border: none; font-weight: bold; padding: 12px 30px; border-radius: 8px; box-shadow: 0 4px 14px rgba(37, 211, 102, 0.4); font-size: 15px; cursor: pointer;">
                    <i class="fab fa-whatsapp mr-2" style="font-size: 18px;"></i>
                    <span id="btn-submit-text">{{ __('Send Message via WhatsApp') }}</span>
                  </button>
                </div>
              </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#contact-page-form').on('submit', function(e) {
        var form = this;
        var firstName = ($('#first-name').val() || '').trim();
        var lastName = ($('#last-name').val() || '').trim();
        var fullName = firstName + (lastName ? ' ' + lastName : '');
        var email = ($('#contact-email').val() || '').trim();
        var phone = ($('#contact-tel').val() || '').trim();
        var message = ($('#message-text').val() || '').trim();

        if (!firstName || !email || !phone || !message) {
            return true; // Let browser HTML5 validation handle empty fields
        }

        e.preventDefault();

        // Construct pre-filled WhatsApp message
        var waText = "👋 *Contact Inquiry - TAMSAL Store*\n\n" +
                     "👤 *Name:* " + fullName + "\n" +
                     "📧 *Email:* " + email + "\n" +
                     "📱 *Phone:* " + phone + "\n\n" +
                     "💬 *Message:*\n" + message;

        var waUrl = "https://api.whatsapp.com/send?phone=923348128646&text=" + encodeURIComponent(waText);

        var submitBtn = $('#contact-submit-btn');
        submitBtn.prop('disabled', true);
        $('#btn-submit-text').text("{{ __('Opening WhatsApp...') }}");

        // Submit form in background to send email & record submission
        var formData = new FormData(form);
        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            complete: function() {
                // Redirect straight to WhatsApp with autofilled details
                window.location.href = waUrl;
            }
        });

        // Fallback timer to guarantee redirect even if network request hangs
        setTimeout(function() {
            window.location.href = waUrl;
        }, 1200);
    });
});
</script>
@endsection
