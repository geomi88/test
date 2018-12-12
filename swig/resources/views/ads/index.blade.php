@extends('layouts.main')
@section('content')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>


<div class="contentHolderV1">
    <h2>Ads Management</h2>
        <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
    <div class="tableHolder">    
    <table style="width: 100%;" class="tableV1" cellpadding="0" cellspacing="0">
        <thead class="tableHeader">
            <tr>
                <td>#</td>
                <td>Ad Location</td>
                <td>Url</td>
                <td>Date</td>
                <td class="actionHolder">Actions</td>
            </tr>
        </thead>
        <tbody>

            <?php $n = $ads->perPage() * ($ads->currentPage() - 1); ?>
                <?php
                foreach ($ads as $ad) {
                $n++;
                ?>
                <tr>
                    <td>{{$n}}</td>
                    <td>{{$ad->adLocation}}</td>
                    <td>{{$ad->url}}</td>
                    <td>{{$ad->created_at}}</td>
                    
                    <td class="actionHolder">
                        <a class="actionBtn view" href="{{ URL::to('ads/view', ['id' => ($ad->id)]) }}" title="Edit"></a>
                        <a class="actionBtn edit" href="{{ URL::to('ads/edit', ['id' => ($ad->id)]) }}" title="Delete"></a>
                    </td>

                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    </div>
    <ul class="pagination">
         {!! $ads->render() !!}
    </ul>
</div>
<script>
    $(document).ready(function () {
        $('.productVerified').change(function () {
            var verified_status = $(this).val();
            var prod_id = $(this).attr("id");
            var base_path = $('#base_path').val();
            
            $.ajax({
                url: base_path + "/products/verify",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {"product_id": prod_id, "is_verified": verified_status},
                success: function (result) {
                    
                }
            });
        });
    });
</script>

@endsection
