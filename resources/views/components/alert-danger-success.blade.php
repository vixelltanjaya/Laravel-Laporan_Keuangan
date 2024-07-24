@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@if (session('berhasil'))
<div class="alert alert-success">
    <ul>
        <li>{{ session('berhasil') }}</li>
    </ul>
</div>
@endif