@extends('layout')

@section('content')
<div class="page">
    <a class="back-to-site" href="/">
        <span class="back-to-site__text">{{ __('manage.login_to_site') }}</span>
    </a>

    <div class="locale-selector" onclick="this.classList.toggle('is-open')">
        <span class="locale-selector__current">{{ \Core\Facades\Site::getLocaleTitle() }}</span>
        {!! \Core\Facades\Site::localeSelectorHtml() !!}
    </div>

    <form class="form rise-6dp" method="POST" action="{{ route('manage_login_post') }}">
        <div class="form__title">{{ __('manage.login_title') }}</div>
        <div class="form__subtitle"></div>
        @csrf
        <div class="text-field">
            <label class="text-field__label" for="email">{{ __('manage.login_email') }}</label>
            <input class="text-field__input{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @if ($errors->has('email'))
                <span class="text-field__error">{{ $errors->first('email') }}</span>
            @endif
        </div>
        <div class="text-field">
            <label class="text-field__label" for="password">{{ __('manage.login_password') }}</label>
            <input class="text-field__input{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" type="password" name="password" required>
            @if ($errors->has('password'))
                <span class="text-field__error">{{ $errors->first('password') }}</span>
            @endif
        </div>
        <div class="checkbox-field">
            <input class="checkbox-field__input" type="checkbox" id="as_admin" name="as_admin" {{ old('as_admin') ? 'checked' : '' }}>
            <label class="checkbox-field__label" for="as_admin"><i class="checkbox-field__placeholder"></i>{{ __('manage.login_as_admin') }}</label>
        </div>
        <div class="checkbox-field">
            <input class="checkbox-field__input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="checkbox-field__label" for="remember"><i class="checkbox-field__placeholder"></i>{{ __('manage.login_remember') }}</label>
        </div>
        <div class="form__actions">
            <button class="button" type="submit">{{ __('manage.login_submit') }}</button>
        </div>
    </form>

    <div class="opx-copy">
        <a class="opx-copy__text" href="https://opxcms.com/">©{{date('Y')}} opxcms.com</a>
        <span class="opx-copy__text">Opx {{ app()->opxVersion() }}</span>
        <span class="opx-copy__text">Laravel {{ app()->version() }}</span>
    </div>
</div>
@endsection
