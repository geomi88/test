@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Check List <span>Categories</span></h1>
    </header>
    @foreach ($checkcategories as $category)
    <a class="checkMainCat" href="{{ URL::to('checklist/checklist_entry/getcheckpoints', ['id' => Crypt::encrypt($category->category_id)]) }}">{{$category->categoryname}} <span style="display: block;font-size: 14px;">{{$category->alias_name}}</span></a><br>
    @endforeach
    
</div>

<script>
</script>
@endsection
