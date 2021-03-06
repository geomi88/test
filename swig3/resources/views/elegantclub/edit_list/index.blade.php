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
            
        if(!$("#slNo").is(':checked')&&!$("#supplierCode").is(':checked')&&!$("#supplierName").is(':checked')&&!$("#mailId").is(':checked')&&!$("#phNo").is(':checked')&&!$("#statuss").is(':checked')&&!$("#countryy").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        
        win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#slNo").is(':checked')){
                strStyle+=' .sl_no {display:none;}';
         }
         if(!$("#supplierCode").is(':checked')){
                strStyle+=' .supplier_code {display:none;}';
         }
         if(!$("#supplierName").is(':checked')){
               strStyle+=' .supplier_name {display:none;}';
         }
         if(!$("#countryy").is(':checked')){
              strStyle+=' .country {display:none;}';  
         } 
         if(!$("#mailId").is(':checked')){
              strStyle+=' .mailid {display:none;}';   
         }
         if(!$("#phNo").is(':checked')){
               strStyle+=' .phno {display:none;}';   
         }
         if(!$("#statuss").is(':checked')){
             strStyle+=' .status {display:none;}';     
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Corporate Customer List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="sl_no"> Sl No.</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="supplier_code"> Customer Code</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="supplier_name"> Customer Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="country"> City</td>'+
                                           '<td style="padding:10px 0;color:#fff;" class="mailid"> Mail ID</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="phno"> Phone Number</td>'+
//                                            '<td style="padding:10px 0;color:#fff;" class="status"> Status </td>'+
                                            '<td style="padding:10px 0;color:#fff;"></td>'+
                                            
                                        '</tr>'+
				'</thead>'+ $('.supplier_list')[0].outerHTML +'</table>');    
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
   
});

function getData(page){
    
    var search_name = $('#search_name').val();
    var searchbyph = $('#searchbyph').val();
    var searchbycode = $('#searchbycode').val();
    var searchbyempcode = $('#searchbyempcode').val();
    var searchbyemail = $('#searchbyemail').val();
    var country = $('#country').val();

    var sortordname=$('#sortordname').val();
    var sortordcode =$('#sortordcode').val();
    var sortordcountry =$('#sortordcountry').val();

    var pagelimit = $('#page-limit').val();
    var status = $('#status').val();


    if(searchbyph=='0'){
        searchbyph=';';
    }

    $.ajax(
    {
        url: '?page=' + page,
        type: "get",
        datatype: "html",
        data: {country: country,sortordcountry: sortordcountry,search_name: search_name,searchbyph:searchbyph,searchbycode:searchbycode,searchbyempcode:searchbyempcode, searchbyemail:searchbyemail,sortordname:sortordname,sortordcode:sortordcode,pagelimit: pagelimit,status:status},

        // {
        //     you can show your loader 
        // }
    })
    .done(function(data)
    {
        console.log(data);

        $(".supplier_list").empty().html(data);
        location.hash = page;
    })
    .fail(function(jqXHR, ajaxOptions, thrownError)
    {
          alert('No response from server');
    });
}



  </script>

<div class="innerContent">
    
 <form  id="pdfgenerator" action="">
    <header class="pageTitle">
        <h1>Edit Corporate Customers <span>List</span></h1>
    </header>

    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <!--<a class="btnAction print bgGreen" href="#">Print</a>-->
<!--    <a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>
    <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>-->

                         <div class="printChoose">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="slNo" checked="" type="checkbox">
                                        <span></span>
                                        <em>Sl No.</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="supplierCode" checked="" type="checkbox">
                                        <span></span>
                                        <em>Customer Code</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="supplierName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Customer Name</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="countryy" checked="" type="checkbox">
                                        <span></span>
                                        <em>City</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="mailId" checked="" type="checkbox">
                                        <span></span>
                                        <em>Mail ID</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="phNo" checked="" type="checkbox">
                                        <span></span>
                                        <em>Phone Number</em>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
<!--                         <div class="custRow">
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="statuss" checked="" type="checkbox">
                                        <span></span>
                                        <em>Status</em>
                                    </label>
                                </div>
                            </div>
                         </div>-->
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
        <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
        <input type="hidden" value="" id="sortordname" name="sortordname">
        <input type="hidden" value="" id="sortordcode" name="sortordcode">
        <input type="hidden" value="" id="sortordcountry" name="sortordcountry">
        <div class="listerType1 reportLister"> 

            <div id="postable">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        <td>
                            Sl No.
                        </td> 
                        <td>
                            Sales By
                        </td> 
                        <td>
                            Customer Code
                            <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp codeup"></a>
                                    <a href="javascript:void(0)" class="btnDown codedown"></a>
                                </div>
                        </td> 
                        <td>
                            Customer Name
                             <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp nameup"></a>
                                    <a href="javascript:void(0)" class="btnDown namedown"></a>
                                </div>
                        </td>
                        
                        
                        <td>
                            City
                            <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp countryup"></a>
                                    <a href="javascript:void(0)" class="btnDown countrydown"></a>
                                </div>
                        </td>
                        <td>
                            Mail ID
                        </td>
                        <td>
                            Phone Number
                            
                        </td>

                       <td>
                           
                        </td>
                    </tr>
                </thead>

                <thead class="listHeaderBottom">
                
                    <tr class="headingHolder">
                        <td class="filterFields" style="min-width: 20px;">
                        </td>
                        <td class="filterFields" style="min-width: 120px;">
                            <div class="custRow">
                                <div class="custCol-12" >
                                    <input type="text" id="searchbyempcode" name="searchbyempcode" placeholder="Enter Code">
                                </div>
                            </div>
                        </td>
                        <td class="filterFields" style="min-width: 120px;">
                            <div class="custRow">
                                <div class="custCol-12" >
                                    <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Code">
                                </div>
                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search_name" name="search_name" placeholder="Enter Name">
                                </div>
                            </div>
                        </td>
                        
                        <td class="filterFields">
                                <div class="custCol-12">
                                    <input type="text" id="country" name="country" placeholder="Enter City">
                                </div>
                        </td>
                        
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="searchbyemail" name="searchbyemail" placeholder="Enter Mail ID">
                                </div>
                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="searchbyph" name="searchbyph" placeholder="Enter Phone">
                                </div>
                            </div>
                        </td>
                        
                        <td>
                        </td>

                    </tr>
                      
                </thead>
              
                <tbody class="supplier_list" id='supplier_list'>
                   @include('elegantclub/edit_list/searchresults')
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
    $('#search_name').bind('keyup', function () {
        search();
    });
    $('#searchbyph').bind('keyup', function () {
        search();
    });
    $('#searchbycode').bind('keyup', function () {
        search();
    });
    $('#searchbyempcode').bind('keyup', function () {
        search();
    });
    $('#searchbyemail').bind('keyup', function () {
        search();
    });
    
    $('#country').bind('keyup', function () {
        search();
    });
    
    $('#status').on("change", function () {
        search();

    });
    
    $(".nameup").on('click', function () {
        $('#sortordname').val('ASC');
        $('#sortordcountry').val('');
        $('#sortordcode').val('');

        search();
    });

    $(".namedown").on('click', function () {
        $('#sortordname').val('DESC');
        $('#sortordcountry').val('');
        $('#sortordcode').val('');
        
        search();
    });
    
    $(".codeup").on('click', function () {
        $('#sortordcode').val('ASC');
        $('#sortordname').val('');
        $('#sortordcountry').val('');

        search();
    });

    $(".codedown").on('click', function () {
        $('#sortordcode').val('DESC');
        $('#sortordname').val('');
        $('#sortordcountry').val('');
        
        search();
    });
    
    $(".countryup").on('click', function () {
        $('#sortordcode').val('');
        $('#sortordname').val('');
        $('#sortordcountry').val('ASC');

        search();
    });

    $(".countrydown").on('click', function () {
        $('#sortordcode').val('');
        $('#sortordname').val('');
        $('#sortordcountry').val('DESC');
        
        search();
    });
    
    $('#page-limit').on("change", function () {
        search();
    });
    
    function search()
    {
        var search_name = $('#search_name').val();
        var searchbyph = $('#searchbyph').val();
        var searchbycode = $('#searchbycode').val();
        var searchbyempcode = $('#searchbyempcode').val();
        var searchbyemail = $('#searchbyemail').val();
        var country = $('#country').val();
        
        var sortordname=$('#sortordname').val();
        var sortordcode =$('#sortordcode').val();
        var sortordcountry =$('#sortordcountry').val();
        
        var pagelimit = $('#page-limit').val();
        var status = $('#status').val();
        
        
        if(searchbyph=='0'){
            searchbyph=';';
        }
        
        $.ajax({
            type: 'POST',
            url: 'edit_corporate_customer',
            data: {country: country,sortordcountry: sortordcountry,search_name: search_name,searchbyph:searchbyph,searchbycode:searchbycode,searchbyempcode:searchbyempcode, searchbyemail:searchbyemail,sortordname:sortordname,sortordcode:sortordcode,pagelimit: pagelimit,status:status},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.supplier_list').html('');
                    $('.supplier_list').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.supplier_list').html('<tr><td colspan="2">No Records Found</td></tr>');
                }
            }
        });



    }
    
    $('#reset').click(function () {
        $(':input')
                .not(':button, :submit, :reset, :hidden')
                .val('')
                .removeAttr('checked')
                .removeAttr('selected');

        $('#search_name').val('');
        $('#searchbycode').val('');
        $('#searchbyempcode').val('');
        $('#country').val('');
        
        $('#page-limit').val(10);
        //search();
        window.location.href = '{{url("elegantclub/edit_corporate_customer")}}';
    });
    
    function funExportData(strType)
    {
        if(strType=="PDF"){
            $('#excelorpdf').val('PDF');
        }else{
            $('#excelorpdf').val('Excel');
        }    
        
        document.getElementById("pdfgenerator").submit();
    }
</script>



@endsection