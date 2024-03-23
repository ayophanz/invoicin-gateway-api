@component('mail::message')
To reset your password, please click the following link:

<a href="{{ $reset_link }}" target="_blank">Click here to reset</a>

If you don't wish to reset your password, ignore this.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
