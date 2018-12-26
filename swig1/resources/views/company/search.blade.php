@extends('layouts.main')
@section('content')


<div class="contentHolderV1">
    <h2>Company Management</h2>
    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
    <div class="searchWrapper">
        <form class="searchArea" action="{{ action('Company\CompanyController@search') }}" method="post">
            {{ csrf_field() }}
            <input type="text" id="search_key" placeholder="Search by Name,Email,Company Name or User Code" name="search_key" >
            <input type="submit" value="Search" class="commonButton search">
            <div class="customClear"></div>
        </form>
        <a  href="{{ URL::to('company') }}" title="SetUp" class="btnReset">RESET</a>
        <div class="customClear"></div>
    </div>
    <div class="tableHolder">
    <table style="width: 100%;" class="tableV1" cellpadding="0" cellspacing="0">
        <thead class="tableHeader">
            <tr>
                <td>#</td>
                <td>First Name</td>
                <td>Last Name</td>
                <td>Phone Number</td>
                <td>Email Address</td>
                <td>Company Name</td>
                <td>Joined On</td>
                <td class="actionHolder">Actions</td>
            </tr>
        </thead>
        <tbody>

            @if(isset($details))
            <?php $n = $details->perPage() * ($details->currentPage() - 1); ?>
            <?php
            foreach ($details as $company) {
                $n++;
                ?>
                <tr>
                    <td>{{$n}}</td>
                    <td>{{$company->firstName}}</td>
                    <td>{{$company->lastName}}</td>
                    <td>{{$company->phoneNumber}}</td>
                    <td>{{$company->email}}</td>
                    <td>{{$company->companyName}}</td>
                    <td>{{$company->created_at}}</td>
                    <td class="actionHolder">
                        <a class="actionBtn edit" href="{{ URL::to('company/edit', ['id' => ($company->id)]) }}" title="Edit"></a>
                        <a class="actionBtn delete" href="{{ URL::to('company/delete', ['id' => ($company->id)]) }}" title="Delete" onclick="return confirm('Are you sure?')"></a>
                    </td>

                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    </div>
    <ul class="pagination">
        @if($details){!! $details->render() !!}@endif
        @elseif(isset($message))
        <p>{{ $message }}</p>
        @endif

    </ul>
</div>
@endsection