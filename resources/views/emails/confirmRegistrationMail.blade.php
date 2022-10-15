@component('mail::message')
Please click the link below to confirm that you are the owner of the email address.

<a href="{{ $verify_link }}" target="_blank">Verify</a>

Please disregard if you did not request it.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
