
@php
    $links = json_decode($menus->menus, true) ?: [];
@endphp

<nav class="site-menu">
    <ul>
        @foreach ($links as $link)
            @php
                $href = Helper::getHref($link); 
                $linkText = ($link["type"] == 'blog' || strtolower(trim($link["text"] ?? '')) == 'blog') ? __('Bundles') : ($link["text"] ?? '');
                $isBundleLink = in_array($link["type"] ?? '', ['bundles', 'bundle', 'deals', 'blog']) || strtolower(trim($link["text"] ?? '')) == 'bundles' || strtolower(trim($link["text"] ?? '')) == 'blog';
                $isBundleActive = $isBundleLink && (request()->routeIs('front.bundles*') || request()->routeIs('front.deal*') || request()->is('bundles*') || request()->is('f*') || request()->is('flash-deals*'));
                $isActive = $isBundleActive || ($href == URL::current());
            @endphp

            @if (!array_key_exists("children",$link))
                <li class="@if($isActive) active @endif">
                    <a href="{{ empty($link["href"]) ? $href : $link["href"] }}" target="{{$link["target"] ?? '_self'}}">{{ $linkText }}</a>
                </li>
            @else
                <li class="t-h-dropdown">
                    <a class="main-link" href="{{$href}}" target="{{$link["target"] ?? '_self'}}">{{ $linkText }}<i class="icon-chevron-down"></i></a>

                    <div class="t-h-dropdown-menu">
                        @foreach ($link["children"] as $level2)
                            @php
                                $l2Href = Helper::getHref($level2);
                                $l2Text = ($level2["type"] == 'blog' || strtolower(trim($level2["text"] ?? '')) == 'blog') ? __('Bundles') : ($level2["text"] ?? '');
                                $isL2Bundle = in_array($level2["type"] ?? '', ['bundles', 'bundle', 'deals', 'blog']) || strtolower(trim($level2["text"] ?? '')) == 'bundles' || strtolower(trim($level2["text"] ?? '')) == 'blog';
                                $isL2BundleActive = $isL2Bundle && (request()->routeIs('front.bundles*') || request()->routeIs('front.deal*') || request()->is('bundles*') || request()->is('f*') || request()->is('flash-deals*'));
                                $isL2Active = $isL2BundleActive || ($l2Href == URL::current());
                            @endphp
                            
                            <a class="@if($isL2Active) active @endif" href="{{$l2Href}}" target="{{$level2["target"] ?? '_self'}}">
                                <i class="icon-chevron-right pr-2"></i>
                                {{ $l2Text }}
                            </a>
                        @endforeach
                    </div>

                </li>
            @endif

        @endforeach
    </ul>
</nav>