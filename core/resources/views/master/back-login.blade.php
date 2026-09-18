
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ $setting->title }}</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	@php
		$favPath = $setting->favicon ?? '';
		$favUrl = !empty($favPath)
			? (\Illuminate\Support\Str::startsWith($favPath, 'images/') ? url('/core/public/storage/' . $favPath) : url('/core/public/storage/images/' . $favPath))
			: asset('favicon.ico');
		$favVersion = !empty($favPath) ? md5($favPath) : time();
	@endphp
	<link rel="icon" href="{{ $favUrl }}?v={{ $favVersion }}" type="image/x-icon"/>

	<!-- Fonts and icons -->
	<script src="{{ asset('assets/back/js/plugin/webfont/webfont.min.js') }}"></script>
	<script id="setFont" data-src="{{ asset("assets/back/css/fonts.css") }}" src="{{ asset('assets/back/js/plugin/webfont/setfont.js') }}"></script>


	<!-- CSS Files -->
	<link rel="stylesheet" href="{{ asset('assets/back/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/back/css/azzara.min.css') }}">

	@if(DB::table('languages')->where('type', 'Dashboard')->where('is_default',1)->first()->rtl == 1)
    <link rel="stylesheet" href="{{ asset('assets/back/css/rtl.css') }}">
    @endif
</head>

<body class="login">

        @yield('content')

    @php
        $mainbs = [];
        $mainbs['is_announcement'] = $setting->is_announcement;
        $mainbs['announcement_delay'] = $setting->announcement_delay;
        $mainbs['overlay'] = $setting->overlay;
        $mainbs = json_encode($mainbs);
    @endphp

	<script src="{{ asset('assets/back/js/core/jquery.3.2.1.min.js') }}"></script>
	<script src="{{ asset('assets/back/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
	<script src="{{ asset('assets/back/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/back/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/back/js/ready.min.js') }}"></script>
</body>
</html>
