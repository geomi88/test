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
                                '<div style="text-align:center;"><h1>Ledger List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;"> Sl.No.</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Ledger Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Budget Start Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Budget End Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Budget Amount </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.item_requests')[0].outerHTML +'</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });
    });
    
    function getData(page) {

         /* var sorting = $('#sortsimp').val();
        var searchkey = $('#searchkey').val();

        var pagelimit = $('#page-limit').val();

        // alert(startdate)alert(enddate);
        
        
        var status= $('#status').val();
        var branch= $('#branch').val();
        var warehouse= $('#warehouse').val();
        
        var startdatefrom = $('#start_date').val();
        var enddatefrom = $('#end_date').val(); */
        
        
        var searchkey = $('#searchkey').val();

        var pagelimit = $('#page-limit').val();

        // alert(startdate)alert(enddate);
        
        
        var status= $('#status').val();
        var branch= $('#branch').val();
        var warehouse= $('#warehouse').val();
        var startdatefrom = $('#start_date').val();
        var enddatefrom = $('#end_date').val();
        var sortorderrequesteddate = $('#sortorderrequesteddate').val();
        var sortorderbranch = $('#sortorderbranch').val();
        var sortorderwarehouse =  $('#sortorderwarehouse').val();
        var sorting = $('#sortsimp').val();
        

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
              //      data: {searchkey: searchkey, sorting: sorting, pagelimit: pagelimit,status:status,branch:branch,warehouse:warehouse,startdatefrom:startdatefrom,enddatefrom:enddatefrom},
                    data: {searchkey: searchkey, sorting: sorting, pagelimit: pagelimit,status:status,branch:branch,warehouse:warehouse,startdatefrom:startdatefrom,enddatefrom:enddatefrom,sortorderrequesteddate:sortorderrequesteddate,sortorderbranch:sortorderbranch,sortorderwarehouse:sortorderwarehouse},
              })
                .done(function (data)
                {
                    console.log(data);

                    $(".item_requests").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>

<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('warehouse/received_inventory_request/exporttopdf') }}" method="post">
        <header class="pageTitle">
            <h1>Received Requests <span>List</span></h1>
        </header>	
        
        <a class="btnAction refresh bgRed" id="reset" href="javascript:void(0)">Refresh</a>
        <!--<a class="btnAction print bgGreen" href="javascript:void(0)">Print</a>-->
        <a class="btnAction saveDoc bgBlue" href="javascript:void(0)" onclick="savetopdf()">Save</a>
        <div class="fieldGroup" id="fieldSet1">

            <div class="customClear"></div>
        </div>
        <div class="listHolderType1">
            <div class="custRow">
            </div>
            <div class="listerType1 reportLister"> 

                <input type="hidden" value="" id="sortsimp" name="sortsimp">
                <input type="hidden" value="" id="sortorderrequesteddate" name="sortorderrequesteddate">
                <input type="hidden" value="" id="sortorderbranch" name="sortorderbranch">            
                <input type="hidden" value="" id="sortorderwarehouse" name="sortorderwarehouse">            
              
                <div id="postable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                 <td>
                                    Requested Date
                                      <div class="sort">
                                         <a href="javascript:void(0)" class="btnUp requestedup"></a>
                                         <a href="javascript:void(0)" class="btnDown requesteddown"></a>
                                      </div>
                                </td>
                                <td>
                                    Request Id
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp sortup"></a>
                                        <a href="javascript:void(0)" class="btnDown sortdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Status
                                </td>
                                <td>
                                    Branch
                                     <div class="sort">
                                         <a href="javascript:void(0)" class="btnUp branchup"></a>
                                         <a href="javascript:void(0)" class="btnDown branchdown"></a>
                                      </div>
                                </td>
                                <td>
                                    Warehouse
                                     <div class="sort">
                                         <a href="javascript:void(0)" class="btnUp warehouseup"></a>
                                         <a href="javascript:void(0)" class="btnDown warehousedown"></a>
                                      </div>
                                </td>
                               
                                <td>
                                    Action
                                </td>
                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">
                                <td class="filterFields"> 
                                    <div class="custRow">
                                    <div class="custCol-6" >
                                        <input type="text" id="start_date" name="start_date" value="" placeholder="From ">
                                    </div>
                                    <div class="custCol-6">
                                        <input type="text" id="end_date" name="end_date" value="" placeholder="To ">
                                    </div>

                                </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchkey" name="searchkey">
                                            <input type="text" id="" name="" style="display: none;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                     <div class="custRow">
                                        <select class="status" name="status" id="status">
                                    <option value="">All</option>
                                    @foreach ($status as $status)
                                        <option value="{{$status->request_status}}" >
                                            <?php echo str_replace('_',' ',$status->request_status);?>
                                        </option>
                                    @endforeach
                                </select>
                                    </div>
                                 </td>
                                <td>
                                    <div class="custRow">
                                        <select class="branch" name="branch" id="branch">
                                    <option value="">All</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{$branch->id}}" >
                                            <?php echo str_replace('_',' ',$branch->name);?>
                                        </option>
                                    @endforeach
                                </select>
                                    </div>
                                </td>
                                <td>
                                     <div class="custRow">
                                        <select class="warehouse" name="warehouse" id="warehouse">
                                    <option value="">All</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{$warehouse->id}}" >
                                            <?php echo str_replace('_',' ',$warehouse->name);?>
                                        </option>
                                    @endforeach
                                </select>
                                    </div>
                                </td>
                               
                                <td></td>
                            </tr>

                        </thead>

                        <tbody class="item_requests" id='item_requests'>                  
                            @include('warehouse/received_inventory_request/results')
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

    $('#searchkey').bind('keyup', function () {
        search();
    });


    $(".sortup").on('click', function () {
        $('#sortsimp').val('ASC');
        $('#sortorderrequesteddate').val('');
        $('#sortorderbranch').val('');
        $('#sortorderwarehouse').val('');
        $('#sortordercreated').val('');
        search();
    });
    $(".sortdown").on('click', function () {
        $('#sortsimp').val('DESC');
        $('#sortorderrequesteddate').val('');
        $('#sortorderbranch').val('');
        $('#sortorderwarehouse').val('');
        $('#sortordercreated').val('');
        search();
    });
    
     $(".requestedup").on('click', function () {
        $('#sortorderrequesteddate').val('ASC');
        $('#sortsimp').val('');
        $('#sortorderbranch').val('');
        $('#sortorderwarehouse').val('');
        $('#sortordercreated').val('');
        search();
    });
    
    $(".requesteddown").on('click', function () {
        $('#sortorderrequesteddate').val('DESC');
        $('#sortsimp').val('');
        $('#sortorderbranch').val('');
        $('#sortorderwarehouse').val('');
        $('#sortordercreated').val('');
        search();
    });
    
    $(".branchup").on('click', function () {
        $('#sortorderbranch').val('ASC');
        $('#sortorderrequesteddate').val('');
        $('#sortsimp').val('');
        $('#sortorderwarehouse').val('');
        $('#sortordercreated').val('');
        search();
    });
    
    $(".branchdown").on('click', function () {
        $('#sortorderbranch').val('DESC');
        $('#sortorderrequesteddate').val('');
        $('#sortsimp').val('');
        $('#sortorderwarehouse').val('');
        $('#sortordercreated').val('');
        search();
    });
    
    $(".warehouseup").on('click', function () {
        $('#sortorderwarehouse').val('ASC');
        $('#sortorderrequesteddate').val('');
        $('#sortsimp').val('');
        $('#sortorderbranch').val('');
        $('#sortordercreated').val('');
        search();
    });
    
    $(".warehousedown").on('click', function () {
        $('#sortorderwarehouse').val('DESC');
        $('#sortorderrequesteddate').val('');
        $('#sortsimp').val('');
        $('#sortorderbranch').val('');
        $('#sortordercreated').val('');
        search();
    });
    
   
    
    
    $('#status').on('change', function () {
        search();
    });
    $('#branch').on('change', function () {
        search();
    });
    $('#warehouse').on('change', function () {
        search();
    });
    
     $('#start_date').on("change", function () {
        if ($('#start_date').val() !== '') {
            search();
        }
    });
    
    $('#end_date').on("change", function () {
        if ($('#end_date').val() !== '') {
            search();
        }
    });

    $('#page-limit').on("change", function () {

        search();

    });

  /*  function search()
    {
       
        var sorting = $('#sortsimp').val();
        var searchkey = $('#searchkey').val();

        var pagelimit = $('#page-limit').val();

        // alert(startdate)alert(enddate);
        
        
        var status= $('#status').val();
        var branch= $('#branch').val();
        var warehouse= $('#warehouse').val();
        var startdatefrom = $('#start_date').val();
        var enddatefrom = $('#end_date').val();

        $.ajax({
            type: 'POST',
            url: 'received_inventory_request',
         //   data: {searchkey: searchkey, sorting: sorting, pagelimit: pagelimit},
            data: {searchkey: searchkey, sorting: sorting, pagelimit: pagelimit,status:status,branch:branch,warehouse:warehouse,startdatefrom:startdatefrom,enddatefrom:enddatefrom},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {

                    $('.item_requests').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.item_requests').html('<p class="noData">No Records Found</p>');
                }
            }
        });



    }*/
    
    
    
    function search()
    {
       
      
        var searchkey = $('#searchkey').val();

        var pagelimit = $('#page-limit').val();

        // alert(startdate)alert(enddate);
        
        
        var status= $('#status').val();
        var branch= $('#branch').val();
        var warehouse= $('#warehouse').val();
        var startdatefrom = $('#start_date').val();
        var enddatefrom = $('#end_date').val();
        var sortorderrequesteddate = $('#sortorderrequesteddate').val();
        var sortorderbranch = $('#sortorderbranch').val();
        var sortorderwarehouse =  $('#sortorderwarehouse').val();
        var sorting = $('#sortsimp').val();
        
      
   /*     alert("searchkey"+searchkey); 
        alert("sorting"+sorting);
        alert("pagelimit"+pagelimit);
        alert("status"+status);
        alert("branch"+branch);
        alert("warehouse"+warehouse);
        alert("startdatefrom"+startdatefrom);
        alert("enddatefrom"+enddatefrom);
        alert("sortorderrequesteddate"+sortorderrequesteddate);*/
                   

        $.ajax({
            type: 'POST',
            url: 'received_inventory_request',
         //   data: {searchkey: searchkey, sorting: sorting, pagelimit: pagelimit},
            data: {searchkey: searchkey, sorting: sorting, pagelimit: pagelimit,status:status,branch:branch,warehouse:warehouse,startdatefrom:startdatefrom,enddatefrom:enddatefrom,sortorderrequesteddate:sortorderrequesteddate,sortorderbranch:sortorderbranch,sortorderwarehouse:sortorderwarehouse},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {

                    $('.item_requests').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.item_requests').html('<p class="noData">No Records Found</p>');
                }
            }
        });



    }
    

    $(function () {
        $('.commonLoaderV1').hide();

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');

            $('#sortsimp').val('');
            $('#searchkey').val('');

            search();
        });
    });
    function savetopdf()
    {

        document.getElementById("pdfgenerator").submit();


    }

    
    $(function () {
    
        $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $("#end_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
       
  
       
    });
</script>


@endsection
