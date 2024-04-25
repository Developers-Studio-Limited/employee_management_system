@component('mail::message')
<h1>We have received your request to reset your account password</h1>
<p>You can use the following code to recover your account:</p>

@component('mail::panel')
<b>Email :</b> {{ $email }}  <br/>
<b>OTP Code:</b> {{ $data['token'] }}
@endcomponent

<p>The allowed duration of the code is one hour from the time the message was sent</p>
@endcomponent