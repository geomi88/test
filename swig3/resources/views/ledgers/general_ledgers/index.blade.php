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
                win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;} .right {float: right;margin-right:20px;}</style>'+
                                '<div style="text-align:center;"><h1>General Ledgers List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;"> Sl No. </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Code </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Ledger Group</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Type</td>'+
                                            '<td style="padding:10px 0;color:#fff;text-align:right;padding-right:20px"> Opening Balance </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Alias Name </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.gl_list')[0].outerHTML +'</table>');
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
       // console.log(data);

        $(".gl_list").empty().html(data);
        location.hash = page;
    })
    .fail(function(jqXHR, ajaxOptions, thrownError)
    {
          alert('No response from server');
    });
}



  </script>

<div class="innerContent">
    
 <form  id="pdfgenerator" action="{{ url('ledgers/general_ledgers/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>General Ledgers <span>List</span></h1>
    </header>
     
    <div class="fieldGroup" id="fieldSet1">
       <div class="custRow">
           <div class="custCol-12">
                <a href="{{ action('Ledgers\GeneralledgersController@add') }}" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
           </div>
       </div>
        <div class="customClear"></div>
    </div>
     
    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <!--<a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>-->
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
                            Code
                            <div class="sort">
                                <a href="javascript:void(0)" class="btnUp codeup"></a>
                                <a href="javascript:void(0)" class="btnDown codedown"></a>
                            </div>
                        </td> 
                        <td>
                            Name
                             <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp nameup"></a>
                                    <a href="javascript:void(0)" class="btnDown namedown"></a>
                                </div>
                        </td>
                       
                        <td>
                            Ledger Group
                        </td>
                        
<!--                        <td>
                            Type
                        </td>
                        -->
                        <td class="right">
                            Opening Balance
                        </td>
                        <td>
                            Alias Name
                        </td>

                       <td>
                            Action
                        </td>
                    </tr>
                </thead>

                <thead class="listHeaderBottom">
                
                    <tr class="headingHolder">
                        <td class="filterFields" style="min-width: 15px;">
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
                        
                        <td class="filterFields" style="min-width: 140px;">
                            <div class="custCol-12">
                                <select class="country" name="groups" id="groups">
                                    <option value="">All</option>
                                    @foreach ($groups as $group)
                                        <option value="{{$group->id}}" >{{$group->name}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </td>
                        
<!--                        <td class="filterFields" style="min-width: 120px;">
                            <div class="custCol-12">
                                <select name="type" id="type">
                                    <option value="">All</option>
                                    <option value="Income">Income</option>
                                    <option value="Expense">Expense</option>
                                    
                                </select>
                            </div>
                        </td>
                        -->
                        <td class="filterFields" style="min-width: 120px;">
                            
                        </td >
                        
                        <td style="min-width: 80px;">
                            
                        </td>
                        
                        <td>
                            
                        </td>
                    </tr>
                      
                </thead>
              
                <tbody class="gl_list" id='gl_list'>
                   @include('ledgers/general_ledgers/result')
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
    
    $('#groups').on("change", function () {
        search();
    });
    
    $('#type').on("change", function () {
        search();
    });
    
    $(".codeup").on('click', function () {
        $('#sortordcode').val('ASC');
        $('#sortordname').val('');
        search();
    });

    $(".codedown").on('click', function () {
        $('#sortordcode').val('DESC');
        $('#sortordname').val('');
        search();
    });
    
    $(".nameup").on('click', function () {
        $('#sortordname').val('ASC');
        $('#sortordcode').val('');
        search();
    });

    $(".namedown").on('click', function () {
        $('#sortordname').val('DESC');
        $('#sortordcode').val('');
        search();
    });
    
    $('#page-limit').on("change", function () {
        search();
    });
    
    function search()
    {
        var searchbycode = $('#searchbycode').val();
        var search_name = $('#search_name').val();
        var searchbygroup = $('#groups').val();
        var searchbytype = $('#type').val();
        
        var sortordname=$('#sortordname').val();
        var sortordcode =$('#sortordcode').val();
        
        var pagelimit = $('#page-limit').val();
        
        $.ajax({
            type: 'POST',
            url: 'general_ledgers',
            data: {searchbycode:searchbycode,search_name: search_name,sortordname:sortordname,sortordcode:sortordcode,searchbygroup:searchbygroup,searchbytype:searchbytype,pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.gl_list').html('');
                    $('.gl_list').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.gl_list').html('<tr><td colspan="2">No Records Found</td></tr>');
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
        
        $('#sortordname').val('');
        $('#sortordcode').val('');
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
