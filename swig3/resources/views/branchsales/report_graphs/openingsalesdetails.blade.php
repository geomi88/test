@extends('layouts.main')
@section('content')
<script>
    $(window).on('hashchange', function () {
        if (window.location.hash) {
            var page = window.location.hash.replace('#', '');
            if (page == Number.NaN || page <= 0) {
                return false;
            } else {
                getData(page);
            }
        }
    });
    $(document).ready(function ()
    {
        $(document).on('click', '.pagination a', function (event)
        {

            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            //var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });

        $('.print').click(function () {
            var pageTitle = 'Page Title',
                stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
                win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);
                win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>'+
                                '<div style="text-align:center;"><h1>Opening Amount Details</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;"> Employee Code </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Employee Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Branch</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Date</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Amount  </td>'+
                                            
                                        '</tr>'+
                                    '</thead>'+ $('.reportbody')[0].outerHTML +'</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });
    });
    
    function getData(page) {
      
      
        
        var searchbycode = $('#searchbycode').val();
        var search_key = $('#search_key').val();
      
        
        var sortordname= $('#sortordname').val();
        var sortordcode =$('#sortordcode').val();
        var sortordbranch = $('#sortordbranch').val();
        var sortorddate = $('#sortorddate').val();
        var sortordamnt = $('#sortordamnt').val();
     
     var empid=$('#empid').val();
     var from_date=$('#from_date').val();
     var to_date=$('#to_date').val();
     var region_id=$('#region_id').val();
     
   
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        
        $.ajax(
            {
                url: '?page=' + page,
                type: "get",
                datatype: "html",
                data: {searchbycode: searchbycode, search_key: search_key, sortordname: sortordname,sortordcode:sortordcode,sortordbranch:sortordbranch,sortorddate:sortorddate,sortordamnt:sortordamnt,pagelimit: pagelimit, searchable: searchable,empid:empid,from_date:from_date,to_date:to_date,region_id:region_id},
           
               // data: {pagelimit:pagelimit,region_id:region_id,to_date:to_date,from_date:from_date,empid:empid},
                // {
                //     you can show your loader 
                // }
            })
            .done(function (data)
            {
                console.log(data);

                $(".reportbody").empty().html(data);
                location.hash = page;
            })
            .fail(function (jqXHR, ajaxOptions, thrownError)
            {console.log(thrownError);
                alert('No response from server');
            });
    }


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('branchsales/exportOpeningDetails') }}" method="post">
    <header class="pageTitle">
        <h1>Opening Amount <span>Details</span></h1>
    </header>
    

    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>
    <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>

    <div class="fieldGroup" id="fieldSet1">
        <div class="customClear"></div>
    </div>
    
    <div class="listHolderType1">
    <div class="listerType1 reportLister"> 
            <input type="hidden" value="{{$empid}}" id="empid" name="empid">
            <input type="hidden" value="{{$from_date}}" id="from_date" name="from_date">
            <input type="hidden" value="{{$to_date}}" id="to_date" name="to_date">
            <input type="hidden" value="{{$region_id}}" id="region_id" name="region_id">
            
        
        
        
            <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
            <input type="hidden" value="" id="sortordname" name="sortordname">
            <input type="hidden" value="" id="sortordcode" name="sortordcode">
            <input type="hidden" value="" id="sortorddate" name="sortorddate">
            <input type="hidden" value="" id="sortordbranch" name="sortordbranch">
            <input type="hidden" value="" id="sortordamnt" name="sortordamnt">
            
            <div id="tblcategorytable">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td>
                                Employee Code
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp codeup"></a>
                                    <a href="javascript:void(0)" class="btnDown codedown"></a>
                                </div>
                            </td>
                            <td>
                                Employee Name
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp nameup"></a>
                                    <a href="javascript:void(0)" class="btnDown namedown"></a>
                                </div>
                            </td>

                            <td>
                                Branch
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp branchup"></a>
                                    <a href="javascript:void(0)" class="btnDown branchdown"></a>
                                </div>
                            </td>
                            <td>
                                Date
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp dateup"></a>
                                    <a href="javascript:void(0)" class="btnDown datedown"></a>
                                </div>
                            </td>
                            
                            <td>
                                Amount 
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp amntup"></a>
                                    <a href="javascript:void(0)" class="btnDown amntdown"></a>
                                </div>
                            </td>
                          
                           
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Employee Code">
                                </div>
                            </td>
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <input type="text" id="search_key" name="search_key" placeholder="Enter Employee Name">
                                </div>
                            </td>
                            
                             <td class="filterFields">
                                <div class="custCol-12">
<!--                                    <select class="searchbybranch" name="searchbybranch">
                                        <option value="">All</option>
                                       
                                    </select>-->
                                </div>
                            </td>
                            <td>
                                <div class="custCol-12">
                                    
                                </div>
                            </td>

                            
                            <td class="filterFields">
                                <div class="custCol-12">
<!--                                    <select class="searchbycategory" name="searchbycategory">
                                        <option value="">All</option>
                                        
                                    </select>-->
                                </div>
                            </td>
                            
                           
                           

                        </tr>

                    </thead>

                    <tbody class="reportbody" id='reportbody'>
                        @include('branchsales/report_graphs/cardsales_result')
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
    
    $('#search_key').bind('keyup', function () {
            search();
    });
    
    $('#job_position').on('change', function () {
        search();
    });

    $('#searchbycode').bind('keyup', function () {
        search();
    });
        
    $('.searchbycategory').on("change", function () {
            search();
    });

    $('#searchbypoint').bind('keyup', function () {
            search();
    });
    
    $('#start_date').on("change", function () {
            search();
    });
    
    $('#end_date').on("change", function () {
            search();
    });
    
    $('.searchbybranch').on("change", function () {
            search();
    });
    
    $('.searchbyrating').on("change", function () {
            search();
    });
    
    $(".nameup").on('click', function () {
        $('#sortordname').val('ASC');
        $('#sortordcode').val('');
        $('#sortordamnt').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
      
        search();
    });

    $(".namedown").on('click', function () {
        $('#sortordname').val('DESC');
        $('#sortordamnt').val('');
        $('#sortordcode').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        
        search();
    });

   

    $(".codeup").on('click', function () {
       $('#sortordname').val('');
        $('#sortordamnt').val('');
        $('#sortordcode').val('ASC');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        search();
    });
        
    $(".codedown").on('click', function () {
       $('#sortordname').val('');
        $('#sortordamnt').val('');
        $('#sortordcode').val('DESC');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
       search();
   });
   
    
    $(".dateup").on('click', function () {
       $('#sortordname').val('');
        $('#sortordamnt').val('');
        $('#sortordcode').val('');
        $('#sortorddate').val('ASC');
        $('#sortordbranch').val('');
        search();
    });
    
    $(".datedown").on('click', function () {
        $('#sortordname').val('');
        $('#sortordamnt').val('');
        $('#sortordcode').val('');
        $('#sortorddate').val('DESC');
        $('#sortordbranch').val('');
        search();
    });
    
    $(".branchup").on('click', function () {
        $('#sortordname').val('');
        $('#sortordamnt').val('');
        $('#sortordcode').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('ASC');
        search();
    });
    
    $(".branchdown").on('click', function () {
        $('#sortordname').val('');
        $('#sortordamnt').val('');
        $('#sortordcode').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('DESC');
        search();
    });
    $(".amntup").on('click', function () {
       $('#sortordname').val('');
        $('#sortordamnt').val('ASC');
        $('#sortordcode').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        search();
    });
    
    $(".amntdown").on('click', function () {
        $('#sortordname').val('');
        $('#sortordamnt').val('DESC');
        $('#sortordcode').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        search();
    });

    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {
        
        var searchbycode = $('#searchbycode').val();
        var search_key = $('#search_key').val();
      
        
        var sortordname= $('#sortordname').val();
        var sortordcode =$('#sortordcode').val();
        var sortordbranch = $('#sortordbranch').val();
        var sortorddate = $('#sortorddate').val();
        var sortordamnt = $('#sortordamnt').val();
     
     var empid=$('#empid').val();
     var from_date=$('#from_date').val();
     var to_date=$('#to_date').val();
     var region_id=$('#region_id').val();
     
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'getOpeningDetails',
            data: {searchbycode: searchbycode, search_key: search_key, sortordname: sortordname,sortordcode:sortordcode,sortordbranch:sortordbranch,sortorddate:sortorddate,sortordamnt:sortordamnt,pagelimit: pagelimit, searchable: searchable,empid:empid,from_date:from_date,to_date:to_date,region_id:region_id},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                
                if (return_data != '')
                {
                    $('.reportbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.reportbody').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }
    
     $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function(selected) {
            $("#end_date").datepicker("option","minDate", selected)
             search();
            }
        });
        $("#end_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy',
        });
        
    $(function () {

        $('#reset').click(function () {
           
            $('#search_key').val('');
           
            $('#searchbycode').val('');
           $('#sortordname').val('');
        $('#sortordamnt').val('');
        $('#sortordcode').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
            
            $('#page-limit').val(10);
            search();
        });
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
