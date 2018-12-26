@extends('layouts.main')
@section('content')


<div class="contentHolderV1">
    <h2>User Management</h2>
    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
    <div class="searchWrapper">
        <form class="searchArea" action="{{ action('Users\UsersController@search') }}" method="post">
            {{ csrf_field() }}
            <input type="text" id="search_key" placeholder="Search Name Here" name="search_key" value="{{ $query }}">
            <input type="submit" value="Search" class="commonButton search">
            <div class="customClear"></div>
        </form>
        <a  href="{{ URL::to('users') }}" title="SetUp" class="btnReset">RESET</a>
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
                <td>User Code</td>
                <td>Joined On</td>
                <td class="actionHolder">Actions</td>
            </tr>
        </thead>
        <tbody>

            @if(isset($details))
            <?php $n = $details->perPage() * ($details->currentPage() - 1); ?>
            <?php
            foreach ($details as $user) {
                $n++;
                ?>
                <tr>
                    <td>{{$n}}</td>
                    <td>{{$user->firstName}}</td>
                    <td>{{$user->lastName}}</td>
                    <td>{{$user->phoneNumber}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->userCode}}</td>
                    <td>{{$user->created_at}}</td>
                    <td class="actionHolder">
                        <a class="actionBtn edit" href="{{ URL::to('users/edit', ['id' => ($user->id)]) }}" title="Edit"></a>
                        <a class="actionBtn delete" href="{{ URL::to('users/delete', ['id' => ($user->id)]) }}" title="Delete" onclick="return confirm('Are you sure?')"></a>
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