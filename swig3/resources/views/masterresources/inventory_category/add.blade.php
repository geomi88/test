@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function()
{

        $( "#inventorycatinsertion" ).validate({
    
                    errorElement: "span",
                    errorClass: "commonError",
                    highlight: function(element, errorClass){
                                $(element).addClass('valErrorV1');
                            },
                            unhighlight: function(element, errorClass, validClass){
                                $(element).removeClass("valErrorV1");
                            },

                    rules: {

                    name: 
                        {
                             required: {
                                depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                            },
                            remote: 
                                {
                                    url: "../inventory_category/checkcategories",
                                    type: "post",
                                    data: 
                                        {
                                    name: function() {
                                    return $.trim($( "#name" ).val());
                                    }
                                         },
                                    dataFilter: function (data)
                                    {
                                        var json = JSON.parse(data);
                                        if (json.msg == "true") {
                                        //return "\"" + "That Company name is taken" + "\"";

                                        $('.inventorycatName').addClass('ajaxLoaderV1');
                                        $('.inventorycatName').removeClass('validV1');
                                        $('.inventorycatName').addClass('errorV1');
                                        return false;
                                        } 
                                        else 
                                        {
                                        $('.inventorycatName').addClass('ajaxLoaderV1');
                                        $('.inventorycatName').removeClass('errorV1');
                                        $('.inventorycatName').addClass('validV1');
                                        return true;
                                        }
                                    }
                                }

                        }  

                        },
                        messages: {
                            name:{required: "Enter Inventory Category Name",
                            remote: "Category Name Already Exists"}
                             }
                    });
});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Category</span></h1>
    </header>	

    <form action="{{ action('Masterresources\InventorycategoryController@store') }}" method="post" id="inventorycatinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder inventorycatName">
                        <label>Name</label>
                       <input type="text" name="name" id="name" onpaste="return false;" autocomplete="off" placeholder="Enter Name">
                                <span class="commonError"></span>
                        <!--<input type="text" name="name" id="name"  placeholder="Enter Company Name">-->
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
                        <label>Main Category</label>
                        <input type="text" id="txtcategoryname" name="txtcategoryname" placeholder="Select Main Category" readonly="true">
                        <input type="hidden" id="inventory_category_id" name="inventory_category_id">
                        <span class="commonError"></span>
                    </div>
                </div>
                
            </div>
            
            <div class="commonModalHolder" style="display: none;">
                <div class="modalContent folderCategory">
                    <div class="modalTop">
                        <a href="#" class="btnModalClose">Close (X)</a>
                        <h2>Inventory Category</h2>
                    </div>
                    <nav class="innerNavV1 folderOptn">
                        <ul>
                            @foreach ($parentcategory as $parent)
                            <li ><span>+</span><a id="{{$parent->id}}">{{ $parent->name }}</a>
                                <ul>
                                @foreach ($childlevel1 as $child)
                                    @if($parent->id==$child->parentid)
                                    <li>
                                        <span class="clschild" usr-id="{{$child->id}}" usr-count="{{$child->childcount}}">+</span>
                                        <a id="{{$child->id}}">{{$child->name}}</a>
                                        <ul id="ul-{{$child->id}}"></ul>
                                    </li>
                                    @endif
                                @endforeach
                                </ul>
                            </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn addinventorycat">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>
    $('.clschild').click(function(){
       var parentid=$(this).attr('usr-id');
       var count=$(this).attr('usr-count');
       
       
       if(count>0 && $("#ul-"+parentid+" li").length==0){
            $.ajax({
            type: 'POST',
            url: '../inventory_items/getchilds',
            data: '&parentid=' + parentid,
            success: function (return_data) {

                $("#ul-"+parentid).html(return_data);
                
            }
        });
       }
       
    });    
    
    $("#txtcategoryname").click(function(){
        $(".commonModalHolder").show();
    });    
    
    $(".btnModalClose").click(function(){
        $(".commonModalHolder").hide();
    });    
    
</script>

@endsection
