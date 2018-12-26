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
      
        $('.print').click(function () {
            var pageTitle = 'Page Title',
                stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
                win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);
                win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>'+
                                '<div style="text-align:center;"><h1>Assets List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;"> Sl No. </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Asset Code </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Asset Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Supplier Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Purchase Date</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Purchase Value </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Asset Value </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> status </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.asset_list')[0].outerHTML +'</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });
   
});

function getData(page){
    
    var search_name = $('#search_name').val();
    var searchbyph = $('#searchbyph').val();
    var searchbycode = $('#searchbycode').val();
    var searchbyemail = $('#searchbyemail').val();
    var search_sname = $('#search_sname').val();

    var sortordname=$('#sortordname').val();
    var sortordcode =$('#sortordcode').val();
    var sortordsname =$('#sortordsname').val();

    var pagelimit = $('#page-limit').val();
    var status = $('#status').val();


    $.ajax(
    {
        url: '?page=' + page,
        type: "get",
        datatype: "html",
        data: {search_name: search_name,searchbyph:searchbyph,searchbycode:searchbycode, searchbyemail:searchbyemail,search_sname: search_sname,sortordname:sortordname,sortordcode:sortordcode,sortordsname:sortordsname,pagelimit: pagelimit,status:status},

        // {
        //     you can show your loader 
        // }
    })
    .done(function(data)
    {
        console.log(data);

        $(".asset_list").empty().html(data);
        location.hash = page;
    })
    .fail(function(jqXHR, ajaxOptions, thrownError)
    {
          alert('No response from server');
    });
}



  </script>

<div class="innerContent">
    
 <form  id="pdfgenerator" action="{{ url('assets/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>Assets <span>List</span></h1>
    </header>

    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>
    <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>


    <div class="fieldGroup" id="fieldSet1">

        <div class="customClear"></div>
    </div>
    <div class="listHolderType1">
        <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
        <input type="hidden" value="" id="sortordname" name="sortordname">
        <input type="hidden" value="" id="sortordcode" name="sortordcode">
        <input type="hidden" value="" id="sortordsname" name="sortordsname">
        <input type="hidden" value="" id="sortorddate" name="sortorddate">
        <div class="listerType1 reportLister"> 

            <div id="postable">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        <td>
                            Sl No.
                        </td> 
                        
                        <td>
                            Asset Code
                            <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp codeup"></a>
                                    <a href="javascript:void(0)" class="btnDown codedown"></a>
                                </div>
                        </td> 
                        <td>
                            Asset Name
                             <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp nameup"></a>
                                    <a href="javascript:void(0)" class="btnDown namedown"></a>
                                </div>
                        </td>
                       
                        <td>
                            Supplier Name
                            <div class="sort">
                                <a href="javascript:void(0)" class="btnUp snameup"></a>
                                <a href="javascript:void(0)" class="btnDown snamedown"></a>
                            </div>
                        </td>
                        <td>
                            Purchase Date
                            <div class="sort">
                                <a href="javascript:void(0)" class="btnUp dateup"></a>
                                <a href="javascript:void(0)" class="btnDown datedown"></a>
                            </div>
                        </td>
                        <td>
                            Purchase Value
                        </td>
                        <td>
                            Asset Value
                        </td>

                       <td>
                            Status
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
                                    <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Code">
                                </div>
                            </div>
                        </td>
                        
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search_name" name="search_name" placeholder="Enter Asset Name">
                                </div>
                            </div>
                        </td>
                        
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search_sname" name="search_sname" placeholder="Enter Supplier Name">
                                </div>
                            </div>
                        </td>
                        
                       <td class="filterFields">
                            <div class="custRow">
                                 <div class="custCol-6">
                                     <input type="text" id="purchase_from" name="purchase_from" value="" placeholder="From ">
                                 </div>
                                 <div class="custCol-6">
                                     <input type="text" id="purchase_to" name="purchase_to" value="" placeholder="To ">
                                 </div>

                             </div>
                         </td>
                        <td class="filterFields" style="min-width: 120px;">
                            
                        </td>
                        <td class="filterFields" style="min-width: 120px;">
                            
                        </td>
                        
                        <td class="filterFields" style="min-width: 120px;">
                            <div class="custCol-12">
                                <select name="status" id="status">
                                    <option value="">All</option>
                                    <option value="1">Enable</option>
                                    <option value="-1">Disable</option>
                                    
                                </select>
                            </div>
                        </td>
                    </tr>
                      
                </thead>
              
                <tbody class="asset_list" id='asset_list'>
                   @include('ledgers/assets/searchresults')
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
    $('#searchbycode').bind('keyup', function () {
        search();
    });
    
    $('#search_name').bind('keyup', function () {
        search();
    });
    
    $('#search_sname').bind('keyup', function () {
        search();
    });
   
     $('#purchase_from').on("change", function () {
        if ($('#purchase_from').val() !== '') {
            search();
        }
    });
    
    $('#purchase_to').on("change", function () {
        if ($('#purchase_to').val() !== '') {
            search();
        }
    });
    
    $('#status').on("change", function () {
        search();

    });
    
     $(".codeup").on('click', function () {
        $('#sortordcode').val('ASC');
        $('#sortordname').val('');
        $('#sortordsname').val('');
        $('#sortorddate').val('');
        search();
    });

    $(".codedown").on('click', function () {
        $('#sortordcode').val('DESC');
        $('#sortordname').val('');
        $('#sortordsname').val('');
        $('#sortorddate').val('');
        search();
    });
    
    $(".nameup").on('click', function () {
        $('#sortordname').val('ASC');
        $('#sortordsname').val('');
        $('#sortordcode').val('');
        $('#sortorddate').val('');
        search();
    });

    $(".namedown").on('click', function () {
        $('#sortordname').val('DESC');
        $('#sortordsname').val('');
        $('#sortordcode').val('');
        $('#sortorddate').val('');
        search();
    });
    
    $(".snameup").on('click', function () {
        $('#sortordname').val('');
        $('#sortordsname').val('ASC');
        $('#sortordcode').val('');
        $('#sortorddate').val('');
        search();
    });

    $(".snamedown").on('click', function () {
        $('#sortordname').val('');
        $('#sortordsname').val('DESC');
        $('#sortordcode').val('');
        $('#sortorddate').val('');
        search();
    });
    
    $(".dateup").on('click', function () {
        $('#sortordname').val('');
        $('#sortordsname').val('');
        $('#sortordcode').val('');
        $('#sortorddate').val('ASC');

        search();
    });

    $(".datedown").on('click', function () {
        $('#sortordname').val('');
        $('#sortordsname').val('');
        $('#sortordcode').val('');
        $('#sortorddate').val('DESC');
        
        search();
    });
 
    $('#page-limit').on("change", function () {
        search();
    });
    
    function search()
    {
        var searchbycode = $('#searchbycode').val();
        var search_name = $('#search_name').val();
        var search_sname = $('#search_sname').val();
        var purchase_from = $('#purchase_from').val();
        var purchase_to = $('#purchase_to').val();
        
        var sortordname=$('#sortordname').val();
        var sortordcode =$('#sortordcode').val();
        var sortordsname =$('#sortordsname').val();
        var sortorddate =$('#sortorddate').val();
        var status = $('#status').val();
        
        var pagelimit = $('#page-limit').val();
        
        $.ajax({
            type: 'POST',
            url: 'assets',
            data: {searchbycode:searchbycode,search_name: search_name,search_sname:search_sname, purchase_from:purchase_from,purchase_to: purchase_to,sortordname:sortordname,sortordcode:sortordcode,sortordsname:sortordsname,sortorddate:sortorddate,pagelimit: pagelimit,status:status},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.asset_list').html('');
                    $('.asset_list').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.asset_list').html('<tr><td colspan="2">No Records Found</td></tr>');
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
        $('#search_sname').val('');
        
        $('#page-limit').val(10);
        search();
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
    
        $("#purchase_from").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $("#purchase_to").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
</script>



@endsection