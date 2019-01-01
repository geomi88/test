@extends('layouts.main')
@section('content')


<div class="contentHolderV1">
    <h2>Category Management</h2>
    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
    <a href="{{ URL::to('category/add') }}" class="searchbtn">Add Category</a>
    <div class="tableHolder">
        <table style="width: 100%;" class="tableV1" cellpadding="0" cellspacing="0">
            <thead class="tableHeader">
                <tr>
                    <td>#</td>
                    <td>Category Name</td>
                    <td>Status</td>
                    <td>Added On</td>
                    <td class="actionHolder">Actions</td>
                </tr>
            </thead>
            <tbody>

                <?php $n = $categories->perPage() * ($categories->currentPage() - 1); ?>
                <?php
                foreach ($categories as $category) {
                    $n++;
                    ?>
                    <tr>
                        <td>{{$n}}</td>
                        <td>{{$category->name}}</td>
                        <td><?php
                            if ($category->status == 1) {
                                echo "Active";
                            } else {
                                echo "Inactive";
                            }
                            ?></td>
                        <td>{{$category->created_at}}</td>

                        <td class="actionHolder">
                            <a class="actionBtn edit" href="{{ URL::to('category/edit', ['id' => ($category->id)]) }}" title="Edit"></a>
                            <a class="actionBtn delete" href="{{ URL::to('category/delete', ['id' => ($category->id)]) }}" title="Delete" onclick="return confirm('Are you sure?')"></a>
                        </td>

                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <ul class="pagination">
        {!! $categories->render() !!}
    </ul>
</div>
@endsection