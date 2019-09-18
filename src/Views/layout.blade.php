<!DOCTYPE html>
<html{!! \Core\Facades\Site::localeHtml() !!}>
<head>
{!! \Core\Facades\Site::metadata() !!}
{!! \Core\Facades\Site::styles() !!}
@stack('styles')
</head>
<body>
@yield('content')
{!! \Core\Facades\Site::scripts() !!}
@stack('scripts')
</body>
</html>