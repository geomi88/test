@extends('layouts.main')
@section('content')
<div class="contentArea">
<a class="btnAction action bgBlue" href="{{ URL::to('dashboard')}}">Back</a>
    <div class="innerContent">
        <h1>You have no permission to view this module</h1>
    </div>
</div>
@endsection