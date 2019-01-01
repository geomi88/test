@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1><span>Inventory Sub Categories</span></h1>
    </header>
     <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-12">
                     <a href="{{ action('Masterresources\InventorysubcategoryController@add') }}" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>
             <div class="customClear"></div>
        </div>
    <div class="listerType1"> 
        <div class="headingHolder deskCont">
            <ul class="custRow">
                <li class="custCol-1">No</li>
                <li class="custCol-4">Name</li>
                <li class="custCol-4">Alias</li>
            </ul>
        </div>
        <div class="listerV1">
            
        <?php $n = $inventory_sub_categories->perPage() * ($inventory_sub_categories->currentPage()-1); ?>           
               @foreach ($inventory_sub_categories as $inventory_sub_category)
               @if($inventory_sub_category->status==0)
               <?php $status='Enable'; ?>
               @else
               <?php $status='Disable'; ?>
               @endif
               <?php $n++; ?>
            <ul class="custRow">
		<li class="custCol-1"><span class="mobileCont">No</span>{{ $n }}</li>
		<li class="custCol-4"><span class="mobileCont">Name</span>{{$inventory_sub_category->name}}</li>
		<li class="custCol-4"><span class="mobileCont">Alias</span>{{$inventory_sub_category->alias_name}}</li>
		<li class="custCol-2 btnHolder">
                <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
		<div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="javascript:void(0)">Edit</a>
		<a class="btnAction disable bgOrange" href="javascript:void(0)"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="javascript:void(0)">Delete</a>
		</div>
		</div>
		</li>
            </ul>           
            @endforeach
        </div>
    </div>
     <?php echo $inventory_sub_categories->render(); ?> 
</div>
 

@endsection
