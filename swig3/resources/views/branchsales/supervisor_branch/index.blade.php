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

            if (!$("#employeeCode").is(':checked') && !$("#employeeName").is(':checked') && !$("#branchName").is(':checked')) {
                   toastr.error('Select required fields to print!');
               } else {
                   var pageTitle = 'Page Title',
                           stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
                           win = window.open('', 'Print', 'width=500,height=300');

                   var strStyle = '<style>.paginationHolder {display:none;}';

                   if (!$("#employeeCode").is(':checked')) {
                       strStyle += ' .employee_code {display:none;}';
                   }
                   if (!$("#employeeName").is(':checked')) {
                       strStyle += ' .employee_name {display:none;}';
                   }
                   if (!$("#branchName").is(':checked')) {
                       strStyle += ' .branch {display:none;}';
                   }
                   strStyle += '.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';

                   win.document.write(strStyle + '<div style="text-align:center;"><h1>Supervisor Branch List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                           '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                           '<tr class="headingHolder">' +
                           '<td style="padding:10px 0;color:#fff;" class="employee_code"> Employee Code</td>' +
                           '<td style="padding:10px 0;color:#fff;" class="employee_name"> Employee Name</td>' +
                           '<td style="padding:10px 0;color:#fff;" class="branch"> Branch</td>' +
                           '</tr>' +
                           '</thead>' + $('.employee_list')[0].outerHTML + '</table>');
                   win.document.close();
                   win.print();
                   win.close();
                   return false;
               }
           });
   
});
function getData(page){
    
    
        
        var search_key = $('#search_key').val();
        var searchbycode =$('#searchbycode').val();
        var branch = $('#branch').val();
        var pagelimit = $('#page-limit').val();
        
        
        
        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
             data: {search_key: search_key,searchbycode:searchbycode,branch: branch, pagelimit: pagelimit},
           
            // {
            //     you can show your loader 
            // }
        })
        .done(function(data)
        {
           // console.log(data);
            
            $(".employee_list").empty().html(data);
            location.hash = page;
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
              alert('No response from server');
        });
}



  </script>

<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('branchsales/exportdatasupervisor') }}" method="post">
    <header class="pageTitle">
        <h1>Supervisor<span> Branch  List</span></h1>
    </header>
    <div class="fieldGroup" id="fieldSet1">
<!--            <div class="custRow">
                <div class="custCol-12">
                     <a href="{{ action('Employee\EmployeeController@add') }}" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>-->
            <div class="customClear"></div>
    </div>
    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
 <a class="btnAction print bgGreen" href="#">Print</a>
       <a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>
    <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>

    <div class="printChoose">
        <div class="custRow">
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="employeeCode" checked="" type="checkbox">
                        <span></span>
                        <em>Employee Code</em>
                    </label>
                </div>
            </div>

            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="employeeName" checked="" type="checkbox">
                        <span></span>
                        <em>Employee Name</em>
                    </label>
                </div>
            </div>

            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="branchName" checked="" type="checkbox">
                        <span></span>
                        <em>branch</em>
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

            <div id="postable">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                         <td>
                            Employee Code
                            
                        </td>
                        <td>
                            Employee Name
                            
                        </td>
                        
                       
                        <td>
                             Branch
                        </td>
                        
                    </tr>
                </thead>

                <thead class="listHeaderBottom">
                
                    <tr class="headingHolder">
                         <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Employee Code">
                                </div>
                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search_key" name="search_key" placeholder="Enter Employee Name">
                                </div>
                            </div>
                        </td> 
                       
                        <td>
                            <div class="">
                            <div class="custCol-12">
                                <select class="branch" name="branch" id="branch">
                                    <option value="">All</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{$branch->id}}" <?php if($branch->id==$branch_id){ echo "selected";}?>>
                                            <?php echo $branch->name.'-'.$branch->branch_code;?>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                                

                        </td>
                        
                    </tr>
                      
                </thead>
              
                <tbody class="employee_list" id='employee_list'>
                           @include('branchsales/supervisor_branch/searchresults')
                </tbody>

            </table>
            </div>
            <div class="commonLoaderV1"></div>
        </div>
<input type="hidden" id="excelorpdf" name="excelorpdf">
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
   
    $('#searchbycode').bind('keyup', function () {
        search();
    });
    $('#searchbyemail').bind('keyup', function () {
        search();
    });
    $('#branch').on('change', function () {
        search();
    });
   
    $('#page-limit').on("change", function () {
        search();

    });
    
     
    function search()
    {
       
        var search_key = $('#search_key').val();
       
        var searchbycode = $('#searchbycode').val();
       
        var branch = $('#branch').val();
       
        var pagelimit = $('#page-limit').val();
        
        
      
        $.ajax({
            type: 'POST',
            url: 'supervisor_branch_list',
            data: {search_key: search_key,searchbycode:searchbycode,branch: branch, pagelimit: pagelimit},
            beforeSend: function () {
             $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                //console.log("cvh");
               // console.log(return_data);
                if (return_data != '')
                {
                  //  $('.employee_list').html('');
                    $('.employee_list').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.employee_list').html('<p class="noData">No Records Found</p>');
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
            $('#search_key').val('');
            $('#job_position').val('');
            $('#country').val('');
            
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

</script>



@endsection