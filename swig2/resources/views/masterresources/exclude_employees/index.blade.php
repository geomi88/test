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
   
     $('.selected_emp_section').hide();
   
    
     $(document).on('click', '.pagination a',function(event)
        {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            //var myurl = $(this).attr('href');
           var page=$(this).attr('href').split('page=')[1];
           //alert(page);
           getData(page);
        });
      
    
    
});
function getData(page){
    
        var search_key = $('#search_key').val();
        var job_position = $('#job_position').val();
       
        var searchbycode = $('#searchbycode').val();
       
        var division = $('#division').val();
        
        var pagelimit = $('#page-limit').val();
       
        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {search_key: search_key, searchbycode:searchbycode,job_position: job_position, division:division, pagelimit: pagelimit},
            
            // {
            //     you can show your loader 
            // }
        })
        .done(function(data)
        {
         //   console.log(data);
            
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
    <header class="pageTitle">
        <h1>Employee <span>List</span></h1>
    </header>
<!--    <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-12">
                     <a href="{{ action('Employee\EmployeeController@add') }}" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>
            <div class="customClear"></div>
    </div>-->
    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>

    <div class="fieldGroup" id="fieldSet1">

        <div class="customClear"></div>
    </div>
    <div class="listHolderType1">
       
        <div class="listerType1 reportLister"> 

            <div id="postable">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        <td></td> 
                        <td>
                            Employee Code
                            
                        </td>
                        <td>
                            Employee Name
                            
                        </td>
                        
                        <td>
                            Job Position
                            
                        </td>
                       
                        <td>
                            Division
                        </td>
<!--                        <td>
                            Action
                        </td>-->
                    </tr>
                </thead>

                <thead class="listHeaderBottom">
                
                    <tr class="headingHolder">
                        <td></td>
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
                                <select class="job_position" name="job_position" id="job_position">
                                    <option value="">All</option>
                                    @foreach ($job_positions as $job_position)
                                        <option value="{{$job_position->id}}" <?php if($job_position->id==$job_id){ echo "selected";}?>>
                                            <?php echo str_replace('_',' ',$job_position->name);?>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                                

                        </td>
                       
                        <td>
                            <div class="custCol-12">
                                <select name="division" id="division">
                                    <option value="">All</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{$division->id}}">{{$division->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
<!--                        <td></td>-->
                    </tr>
                      
                </thead>
              
                <tbody class="employee_list" id='employee_list'>
                   @include('masterresources/exclude_employees/searchresults')
                </tbody>

            </table>
            </div>
            <div class="commonLoaderV1"></div>
        </div>

    </div>
     <a class="btnAction action bgGreen spacingBtm3 addempexclude" href="javascript:void(0)"> Submit</a>
    <div class="pagesShow">
        <span>Showing 10 of 20</span>
        <select id="page-limit">
          
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>
     
     
      <form action="{{ url('masterresource/excepted/store') }}" method="post" id="add_exclude_emp" class="selected_emp_section">
            

            <h4 class="blockHeadingV1">Excluded List</h4>
            
            <div class="selected_pos">
            <div class="listHolderType1">
                

                <div class="listerType1"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Date</td>
                                <td>Branch Name</td>
                                <td>Shift</td>
                                <td>Collected Cash</td>
                                <td>Remove</td>
                            </tr>
                        </thead>

                    </table>
                    <div class="commonLoaderV1"></div>
                </div>					
            </div>

             </div>
            
<input value="Submit" class="commonBtn bgRed addBtn" type="submit">
                  
            
            </form>
</div>
<script>
    $('#search_key').bind('keyup', function () {
        search();
    });
    $('#searchbyph').bind('keyup', function () {
        search();
    });
    $('#searchbycode').bind('keyup', function () {
        search();
    });
    $('#searchbyemail').bind('keyup', function () {
        search();
    });
    $('#job_position').on('change', function () {
        search();
    });
    $('#division').on('change', function () {
        search();
    });
    $('#country').on('change', function () {
        search();
    });
    $('#page-limit').on("change", function () {
        search();

    });
    
    function search()
    {
        var search_key = $('#search_key').val();
        //var searchbyph = $('#searchbyph').val();
        var searchbycode = $('#searchbycode').val();
       // var searchbyemail = $('#searchbyemail').val();
        var job_position = $('#job_position').val();
        var division = $('#division').val();
       // var country = $('#country').val();
        var pagelimit = $('#page-limit').val();
        
        
        
        $.ajax({
            type: 'POST',
            url: 'excepted',
            data: {search_key: search_key,searchbycode:searchbycode,job_position: job_position, division:division,pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.employee_list').html('');
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
    
      
        $('.addempexclude').on("click", function () {
      
        var pagelimit = $('#page-limit').val();
        var selected_pos =  $("input[name='selected_pos[]']:checked");
        var values = new Array();
        $.each($("input[name='selected_pos[]']:checked"), function() {
           values.push($(this).val());
        });
        selected_pos = JSON.stringify(values);
        
        var selected_ids =  $("input[name='emp_ids[]']");
         var selectedvalues = new Array();
         $.each($("input[name='emp_ids[]']"), function() {
           selectedvalues.push($(this).val());
        });
         selected_ids = JSON.stringify(selectedvalues);
         
         
            $.ajax({
                type: 'POST',
                url: '../masterresources/excepted/exclude_employees',
                data: {selected_ids:selected_ids,selected_pos:selected_pos,pagelimit:pagelimit,status:0},
                async: false,
                cache: false,
                timeout: 30000,
                success: function (return_data) {
                   // alert(return_data);
                    $('.selected_pos').html(return_data);
                    $('.selected_emp_section').show();
                }
            });
            $.ajax({
                type: 'POST',
                url: '../masterresources/excepted/notexcludedids',
                async: false,
                cache: false,
                timeout: 30000,
                data: {selected_pos:selected_pos,pagelimit:pagelimit},
                success: function (return_data) {
                  //  alert(return_data);
                $('.employee_list').html('');
                    $('.employee_list').html(return_data);
                   
                }
            });
        
    });
    
     $('.selected_pos').on("click", "a.remove_emp_id", function () {
        var pagelimit = $('#page-limit').val();
        var selected_ids =  $("input[name='emp_ids[]']");
         var selectedvalues = new Array();
         $.each($("input[name='emp_ids[]']"), function() {
           selectedvalues.push($(this).val());
        });
         selected_ids = JSON.stringify(selectedvalues);
         
         var selected_pos = $(this).attr("value");
         var values = new Array();
             values.push(selected_pos);
             selected_pos = JSON.stringify(values);
              
             
            $.ajax({
                type: 'POST',
                 url: '../masterresources/excepted/remove_employees',
                async: false,
                cache: false,
                timeout: 30000,
                data: {selected_ids:selected_ids,selected_pos: selected_pos,pagelimit:pagelimit,status:0},
                success: function (return_data) {
                   if(return_data!=""){
                  $('.selected_pos').html(return_data);
                    $('.selected_emp_section').show();
                }else{
                  $('.selected_pos').html("");
                    $('.selected_emp_section').hide();   
                }
                }
            });
            
          
            $.ajax({
                type: 'POST',
                url: '../masterresources/excepted/notexcludedlist',
                async: false,
                cache: false,
                timeout: 30000,
                data: {selected_pos: selected_pos,selected_ids:selected_ids,pagelimit:pagelimit},
                success: function (return_data) {
                 //  alert(return_data);
                $('.employee_list').html('');
                    $('.employee_list').html(return_data);
                   
                }
            });
    });
    
</script>



@endsection