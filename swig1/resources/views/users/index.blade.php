@extends('layouts.main')
@section('content')

<div class="contentHolderV1">
    <h2>User Management</h2>
    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
    <div class="searchWrapper">
        <form class="searchArea" id="productSearch" action="{{ action('Users\UsersController@search') }}" method="post">
            {{ csrf_field() }}
            <input type="text" id="search_key" placeholder="Search by Name,Price or Seller Name" name="search_key" >
            <input type="submit" value="Search" class="commonButton search">
            <div class="customClear"></div>

        </form>
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

                <?php $n = $users->perPage() * ($users->currentPage() - 1); ?>
                <?php
                foreach ($users as $user) {
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
        {!! $users->render() !!}
    </ul>
</div>



@endsection