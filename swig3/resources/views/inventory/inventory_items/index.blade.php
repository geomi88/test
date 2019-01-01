@extends('layouts.main')
@section('content')
<script>
 $(window).on('hashchange', function() {
        if (window.location.hash) {
            var page = window.location.hash.replace('#', '');
            if (page == Number.NaN || page <= 0) {
                return false;
            }else{
                getData(page);
            }
        }
    });
$(document).ready(function()
{
     $(document).on('click', '.pagination a',function(event)
    {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
       var page=$(this).attr('href').split('page=')[1];
       getData(page);
    });

     $('.takePrint').click(function () {
            
        if(!$("#productCode").is(':checked')&&!$("#altunit3").is(':checked')&&!$("#altunit2").is(':checked')&&!$("#altunit1").is(':checked')&&!$("#primaryunit").is(':checked')&&!$("#supplierCode").is(':checked')&&!$("#productName").is(':checked')&&!$("#group").is(':checked')&&!$("#category").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
       
        win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#productCode").is(':checked')){
                strStyle+=' .inve_code {display:none;}';
         }
         
         if(!$("#supplierCode").is(':checked')){
                strStyle+=' .sup_code {display:none;}';
         }
         if(!$("#productName").is(':checked')){
                strStyle+=' .inve_name {display:none;}';
         }
         if(!$("#group").is(':checked')){
               strStyle+=' .inve_group {display:none;}';
         }
         if(!$("#category").is(':checked')){
              strStyle+=' .inve_cat {display:none;}';  
         } 
         if(!$("#primaryunit").is(':checked')){
              strStyle+=' .primary {display:none;}';  
         } 
         if(!$("#altunit1").is(':checked')){
              strStyle+=' .altunit1 {display:none;}';  
         } 
         if(!$("#altunit2").is(':checked')){
              strStyle+=' .altunit2 {display:none;}';  
         } 
         if(!$("#altunit3").is(':checked')){
              strStyle+=' .altunit3 {display:none;}';  
         } 
        
        strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Inventory List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
						'<td style="padding:10px 0;color:#fff;" class="inve_code"> Product Code</td>'+
						'<td style="padding:10px 0;color:#fff;" class="inve_code"> Supplier Code</td>'+
						'<td style="padding:10px 0;color:#fff;" class="inve_name"> Product Name</td>'+
						'<td style="padding:10px 0;color:#fff;" class="inve_group"> Group</td>'+
						'<td style="padding:10px 0;color:#fff;" class="inve_cat"> Category </td>'+
						'<td style="padding:10px 0;color:#fff;" class="primary"> Primary Unit </td>'+
						'<td style="padding:10px 0;color:#fff;" class="altunit1"> Alternate Unit 1 </td>'+
						'<td style="padding:10px 0;color:#fff;" class="altunit2"> Alternate Unit 2 </td>'+
						'<td style="padding:10px 0;color:#fff;" class="altunit3"> Alternate Unit 3 </td>'+
						
						'</tr>'+
				'</thead>'+ $('.tblinventorylist')[0].outerHTML +'</table>');    
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });    

});

function getData(page){

        var searchbyname = $('#searchbyname').val();
        var searchbycategory = $('.searchbycategory').val();
        var searchbygroup = $('.searchbygroup').val();
        var searchbypcode = $('#searchbypcode').val();
        var searchbyscode = $('#searchbyscode').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {searchbypcode:searchbypcode,searchbyscode:searchbyscode,searchbyname: searchbyname,searchbycategory:searchbycategory,searchbygroup:searchbygroup, pagelimit: pagelimit, searchable: searchable},
            
            // {
            //     you can show your loader 
            // }
        })
        .done(function(data)
        {
            $(".tblinventorylist").empty().html(data);
            location.hash = page;
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
              alert('No response from server');
        });
}


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('inventory/inventory_items/exporttopdf') }}" method="post">
    <header class="pageTitle">
        <h1>Inventory <span>List</span></h1>
    </header>
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
            <div class="custCol-12">
                <a href="{{ action('Inventory\InventoryController@add') }}" id="btnNew" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
            </div>
        </div>
        <div class="customClear"></div>
    </div>
    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <!--<a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>-->
                     <div class="printChoose">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="productCode" checked="" type="checkbox">
                                        <span></span>
                                        <em>Product Code</em>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="supplierCode" checked="" type="checkbox">
                                        <span></span>
                                        <em>Supplier Code</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="productName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Product Name</em>
                                    </label>
                                </div>
                            </div>

                           
                        </div>

                        <div class="custRow">
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="group" checked="" type="checkbox">
                                        <span></span>
                                        <em>Group</em>
                                    </label>
                                </div>
                            </div>
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="category" checked="" type="checkbox">
                                        <span></span>
                                        <em>Category</em>
                                    </label>
                                </div>
                            </div>
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="primaryunit" checked="" type="checkbox">
                                        <span></span>
                                        <em>Primary Unit</em>
                                    </label>
                                </div>
                            </div>

                         </div>
                         
                        <div class="custRow">
                            <div class="custCol-4">
                               <div class="commonCheckHolder checkRender">
                                   <label>
                                       <input id="altunit1" checked="" type="checkbox">
                                       <span></span>
                                       <em>Alternate Unit 1</em>
                                   </label>
                               </div>
                           </div>
                            <div class="custCol-4">
                               <div class="commonCheckHolder checkRender">
                                   <label>
                                       <input id="altunit2" checked="" type="checkbox">
                                       <span></span>
                                       <em>Alternate Unit 2</em>
                                   </label>
                               </div>
                           </div>
                            <div class="custCol-4">
                               <div class="commonCheckHolder checkRender">
                                   <label>
                                       <input id="altunit3" checked="" type="checkbox">
                                       <span></span>
                                       <em>Alternate Unit 3</em>
                                   </label>
                               </div>
                           </div>

                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <a class="commonBtn takePrint bgDarkGreen">Print</a>
                            </div>
                        </div>
                    </div>
    
    <div class="fieldGroup" id="fieldSet1">
        <div class="customClear"></div>
    </div>
    <div class="listHolderType1">

        <div class="listerType1 reportLister"> 
            <div id="">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">

                            <td>
                                Product Code
                            </td>
                            <td>
                                Supplier Code
                            </td>
                            <td>
                                Product Name
                            </td>
                            <td>
                                Group
                            </td>
                            <td>
                                Category
                            </td>
                            
                            <td>
                                Primary Unit
                            </td>
                            
                            <td>
                                Alternate unit 1
                            </td>
                            
                            <td>
                                Alternate unit 2
                            </td>
                            
                            <td>
                                Alternate unit 3
                            </td>

                            <td>
                                Action
                            </td>
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">


                            <td class="filterFields" style="min-width: 120px !important;">
                                <div class="">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbypcode" name="searchbypcode" placeholder="Product Code">
                                        
                                    </div>
                                </div>
                            </td>
                            
                            <td class="filterFields" style="min-width: 120px !important;">
                                <div class="">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbyscode" name="searchbyscode" placeholder="Supplier Code">
                                        
                                    </div>
                                </div>
                            </td>
                            
                            <td class="filterFields">
                                <div class="">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbyname" name="searchbyname" placeholder="Enter Product Name">
                                        <input type="text" id="" name="" style="display: none;">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="custCol-12">
                                    <select class="searchbygroup" name="searchbygroup">
                                        <option value="">All</option>
                                        @foreach ($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td >
                                <div class="custCol-12">
                                    <select class="searchbycategory" name="searchbycategory">
                                        <option value="">All</option>
                                        @foreach ($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>


                            <td style="min-width: 100px !important;">
                            </td>
                            
                            <td style="min-width: 130px !important;">
                            </td>
                            
                            <td style="min-width: 130px !important;">
                            </td>
                            
                            <td style="min-width: 130px !important;">
                            </td>
                            
                            <td>
                            </td>
                            
                        </tr>

                    </thead>

                    <tbody class="tblinventorylist" id='tblinventorylist'>
                        @include('inventory/inventory_items/result')
                    </tbody>

                </table>
            </div>
            <div class="commonLoaderV1"></div>
        </div>

    </div>
    <div class="pagesShow">
        <span>Showing 10 of 20</span>
        <select id="page-limit">

            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>
</form>
</div>

<script>
    
    $('#searchbyname').bind('keyup', function () {
        search();
    });
    $('#searchbypcode').bind('keyup', function () {
        search();
    });
    $('#searchbyscode').bind('keyup', function () {
        search();
    });

    $('.searchbygroup').on("change", function () {
        
            search();

    });
    
    $('.searchbycategory').on("change", function () {
       
            search();
        
    });
    
//    $('.searchbywarehouse').on("change", function () {
//       
//            search();
//       
//    });

    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {

        var searchbyname = $('#searchbyname').val();
        var searchbypcode = $('#searchbypcode').val();
        var searchbyscode = $('#searchbyscode').val();
        var searchbycategory = $('.searchbycategory').val();
        var searchbygroup = $('.searchbygroup').val();
        //var searchbywarehouse = $('.searchbywarehouse').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'inventory_items',
            data: {searchbypcode:searchbypcode,searchbyscode:searchbyscode,searchbyname: searchbyname, searchbycategory: searchbycategory,searchbygroup:searchbygroup, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.tblinventorylist').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.tblinventorylist').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }

    $(function () {

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');


            $('#searchbyname').val('');
            $('#searchbycategory').val('');
            $('#searchbygroup').val('');
           // $('#searchbywarehouse').val('');


            //search();
            window.location.href = '{{url("inventory/inventory_items")}}';
        });
    });

    function savetopdf()
    {

        document.getElementById("pdfgenerator").submit();


    }
</script>
@endsection
