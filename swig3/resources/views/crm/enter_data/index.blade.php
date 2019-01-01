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
        //var myurl = $(this).attr('href');
       var page=$(this).attr('href').split('page=')[1];
       getData(page);
    });

     $('.takePrint').click(function () {
            
        if(!$("#employee_code").is(':checked')&&!$("#warehouse_name").is(':checked')&&!$("#status").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#employee_code").is(':checked')){
                strStyle+=' .inve_code {display:none;}';
         }
         if(!$("#warehouse_name").is(':checked')){
                strStyle+=' .inve_name {display:none;}';
         }
         if(!$("#status").is(':checked')){
               strStyle+=' .inve_group {display:none;}';
         }
         
        strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Inventory List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
						'<td style="padding:10px 0;color:#fff;" class="inve_code"> Employee</td>'+
						'<td style="padding:10px 0;color:#fff;" class="inve_name"> Warehouse Name</td>'+
						'<td style="padding:10px 0;color:#fff;" class="inve_group"> Status</td>'+
						'</tr>'+
				'</thead>'+ $('.tblinventorylist')[0].outerHTML +'</table>');    
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });    
       $('.saveDoc').click(function () {
      
//       svepdf();
       
        
    });
});

function getData(page){

         var searchbyemployee = $('#searchbyemployee').val();
        var searchbywarehouse = $('#searchbywarehouse').val();
        var searchbystatus = $('#searchbystatus').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {searchbyemployee: searchbyemployee,searchbywarehouse:searchbywarehouse,searchbystatus:searchbystatus, pagelimit: pagelimit, searchable: searchable},
            
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
    <form id="pdfgenerator" action="" method="post">
    <header class="pageTitle">
        <h1>CRM <span>Customers</span></h1>
    </header>
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
            <div class="custCol-12">
                <a href="{{ action('Crm\EnterDataController@add') }}" id="btnNew" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
            </div>
        </div>
        <div class="customClear"></div>
    </div>
    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <!--<a class="btnAction print bgGreen" href="#">Print</a>-->
    <!--<a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>-->
                     <div class="printChoose">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="customer_name" checked="" type="checkbox">
                                        <span></span>
                                        <em>Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="mobile_number" checked="" type="checkbox">
                                        <span></span>
                                        <em>Mobile Number</em>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="status" checked="" type="checkbox">
                                        <span></span>
                                        <em>Status</em>
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
                                Sl No
                            </td>
                            <td>
                                Name
                            </td>
                            <td>
                                Mobile Number
                            </td>
                            <td>
                                Repeat
                            </td>
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">

<td></td>
<td></td>
                            <td class="filterFields">
                                <div class="">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbymobile" name="searchbymobile" placeholder="Enter Mobile Number">
                                    </div>
                                </div>
                            </td>
                            
<td></td>
                        </tr>

                    </thead>

                    <tbody class="tblinventorylist" id='tblinventorylist'>
                        @include('crm/enter_data/result')
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
    
    $('#searchbymobile').bind('keyup', function () {
        search();
    });


    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {

        var searchbymobile = $('#searchbymobile').val();
        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'enter_data',
            data: {searchbymobile: searchbymobile, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
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


            $('#searchbyemployee').val('');
            $('#searchbywarehouse').val('');
            $('#searchbystatus').val('');
           


            //search();
            window.location.href = '{{url("crm/enter_data")}}';
        });
    });

    function savetopdf()
    {

        document.getElementById("pdfgenerator").submit();


    }
</script>
@endsection
