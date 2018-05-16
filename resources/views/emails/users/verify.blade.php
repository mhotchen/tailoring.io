@component('mail::message')
# @lang('user_verification.title')

@lang('user_verification.body')

@component('mail::button', ['url' => $verifyLink])
@lang('user_verification.button')
@endcomponent

@endcomponent
