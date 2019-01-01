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
      
   
});
function getData(page){
    
        var search_key = $('#search_key').val();
        var job_position = $('#job_position').val();
        var country = $('#country').val();
        var pagelimit = $('#page-limit').val();
        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {search_key: search_key, job_position: job_position, country: country,pagelimit: pagelimit},
            
            // {
            //     you can show your loader 
            // }
        })
        .done(function(data)
        {
            console.log(data);
            
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
                            Action
                        </td>
                    </tr>
                </thead>

                <thead class="listHeaderBottom">
                
                    <tr class="headingHolder">
                         <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search_code_key" name="search_code_key">
                                </div>
                            </div>
                        </td>
                        
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search_key" name="search_key">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="custRow">
                            <div class="custCol-12">
                                <select class="job_position" name="job_position" id="job_position">
                                    <option value="">All</option>
                                    @foreach ($job_positions as $job_position)
                                        <option value="{{$job_position->id}}"><?php echo str_replace('_',' ',$job_position->name);?></option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                        </td>

                        
                        <td></td>
                    </tr>
                      
                </thead>
              
                <tbody class="employee_list" id='employee_list'>
                   @include('hr/userpermissions/searchresults')
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
</div>
<script>
    $('#search_key').bind('keyup', function () {
        search();
    });
    
    $('#search_code_key').bind('keyup', function () {
        search();
    });
    $('#job_position').on('change', function () {
        search();
    });

    $('#page-limit').on("change", function () {
        search();

    });
    function search()
    {
        var search_key = $('#search_key').val();
         var search_code_key = $('#search_code_key').val();
        var job_position = $('#job_position').val();
        var country = $('#country').val();
        var pagelimit = $('#page-limit').val();
        
        $.ajax({
            type: 'POST',
            url: 'userpermissions',
            data: {search_key: search_key,search_code_key: search_code_key, job_position: job_position, country: country,pagelimit: pagelimit},
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
    
</script>



@endsection