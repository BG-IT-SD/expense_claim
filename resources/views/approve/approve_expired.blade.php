@if (session('message'))
    {{ session('message') }}
@else
    <h1>Session หมดอายุ</h1>
@endif
