@component('mail::message')
# New Post on {{ $post['site_url'] }}

{{ date('l jS \of F Y h:i:s A') }}

Post Title: {{ $post['title'] }}

Post Summary: {{ $post['summary'] }}

yours,

{{ config('app.name') }} Team
@endcomponent
