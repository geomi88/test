@extends('layout.web.website')
@section('content')
@section('bannerClass','innerBanner')
@section('title', 'Profile Update')
@include('web.includes.banner_main')

<section class="ourProperties">
    <div class="row">
        <h2><span>{{ __('discover_our') }}</span>{{ __('properties') }}</h2>
        <div class="tabWrapper">
            <ul class="tabMenu">
                <li data-tab="Houses" id="{{ \Crypt::encrypt(1)}}" class="selected">{{__('houses')}}</li>
                <li data-tab="Apartments" id="{{ \Crypt::encrypt(2)}}" >{{__('apartments')}}</li>
                <li data-tab="Commercial"  id="{{ \Crypt::encrypt(3)}}">{{__('commercials')}}</li>
                <li data-tab="Office" id="{{ \Crypt::encrypt(4)}}">{{__('office')}}</li>
            </ul>
            <div class="tabContent" id="Houses">
            </div>
            <div class="tabContent" id="Apartments">
            </div>
            <div class="tabContent" id="Commercial">
            </div>
            <div class="tabContent" id="Office">
            </div>
        </div>
    </div>
</section>
<section class="bottomSection">
    <div class="row">
        <h2><span>RECENT</span>CONSTRUCTION</h2>
        @include('web.includes.recent_construction')
    </div>
    @include('web.includes.our_services')

</div>

</section>
<script>
    $(document).ready(function ($) {
        triggerGridLi(0);
    });
    function triggerGridLi(num) {
        $('.tabMenu li').eq(num).trigger('click');
    }
</script>

@endsection