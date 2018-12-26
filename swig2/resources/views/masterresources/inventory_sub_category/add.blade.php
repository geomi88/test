@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Sub Category</span></h1>
    </header>	

    <form action="{{ action('Masterresources\InventorysubcategoryController@store') }}" method="post" id="inventorysubcatinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder inventorysubcatName">
                        <label>Name</label>
                       <input type="text" name="sub_cat_name" id="sub_cat_name" onpaste="return false;" autocomplete="off" placeholder="Enter Name">
                                <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias Name">
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Choose Category</label>
                        <select class="commoSelect" name="inventory_category_id" id="inventory_category_id">
                            <option >Select Category</option>
                            @foreach ($inventory_categories as $inventory_category)
-                           <option value='{{ $inventory_category->id }}'>{{ $inventory_category->name}}</option>
-                           @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn addinventorysubcat">
                </div>
            </div>

        </div>
    </form>	
</div>

@endsection
