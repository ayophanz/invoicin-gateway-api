@component('mail::message')
Please click the link below to confirm that you are the owner of the email address.

@component('mail::button', ['url' => $verify_link])
Verify
@endcomponent

Please disregard if you did not request it.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
