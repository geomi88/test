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
        var profession = $('#profession').val();
        var searchbyph = $('#searchbyph').val();
        var searchbycode = $('#searchbycode').val();
        var searchbyemail = $('#searchbyemail').val();
        var division = $('#division').val();
        var country = $('#country').val();
        var pagelimit = $('#page-limit').val();
        if(searchbyph=='0'){
            searchbyph=';';
        }
        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {search_key: search_key, searchbyph:searchbyph,searchbycode:searchbycode, searchbyemail:searchbyemail,profession: profession, division:division, country: country,pagelimit: pagelimit},
            
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
    
 <form  id="pdfgenerator" action="{{ url('employee/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>Employee <span>List</span></h1>
    </header>

    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>
    <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>


    <div class="fieldGroup" id="fieldSet1">

        <div class="customClear"></div>
    </div>
    <div class="listHolderType1">
        <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
           
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
                           Gossi Number
                            
                        </td>
                          
                          <td>
                           Profession
                            
                        </td>
                        <td>
                            Country
                            
                        </td>
                        <td>
                           Passport Number
                            
                        </td>
                        <td>
                            Mail ID
                        </td>
                        <td>
                            Phone Number
                            
                        </td>
                       
                        <td>
                            Division
                        </td>
                       <td>
                            Status
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
                        
                          <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search_gossi_no" name="search_gossi_no" placeholder="Enter Gossi Nmber">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="">
                            <div class="custCol-12">
                                <select class="profession" name="profession" id="profession">
                                    <option value="">All</option>
                                    @foreach ($id_professional as $profession)
                                        <option value="{{$profession->id}}" <?php if($profession->id==$job_id){ echo "selected";}?>>
                                            <?php echo str_replace('_',' ',$profession->name);?>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                                

                        </td>
                        <td>
                            <div class="">
                                <div class="custCol-12">
                                    <select class="country" name="country" id="country">
                                        <option value="">All</option>
                                        @foreach ($countries as $country)
                                        <option value="{{$country->id}}" <?php if($country_id==$country->id){echo "selected";}?>>{{$country->name}}</option>
                                    @endforeach
                                    </select>

                                </div>
                            </div>
                        </td>
                        
                          <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search_pass_no" name="search_pass_no" placeholder="Enter Passport Number">
                                </div>
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
                            <div class="custCol-12">
                                <select name="division" id="division">
                                    <option value="">All</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{$division->id}}">{{$division->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        
                        <td>
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
              
                <tbody class="employee_list" id='employee_list'>
                   @include('employee/searchresults')
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
    $('#searchbyph').bind('keyup', function () {
        search();
    });
    $('#searchbycode').bind('keyup', function () {
        search();
    });
    $('#searchbyemail').bind('keyup', function () {
        search();
    });
    $('#profession').on('change', function () {
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
    $('#status').on("change", function () {
        search();

    });
    $('#search_gossi_no').bind('keyup', function () {
        search();
    }); 
    $('#search_pass_no').bind('keyup', function () {
        search();
    });
    
    function search()
    {
        var search_key = $('#search_key').val();
        var searchbyph = $('#searchbyph').val();
        var searchbycode = $('#searchbycode').val();
        var searchbyemail = $('#searchbyemail').val();
        var profession = $('#profession').val();
        var division = $('#division').val();
        var country = $('#country').val();
        var pagelimit = $('#page-limit').val();
        var status = $('#status').val();
        var search_gossi_no = $('#search_gossi_no').val();
        var search_pass_no = $('#search_pass_no').val();
        
        if(searchbyph=='0'){
            searchbyph=';';
        }
        
        $.ajax({
            type: 'POST',
            url: 'employee',
            data: {search_gossi_no:search_gossi_no,search_pass_no:search_pass_no,search_key: search_key,searchbyph:searchbyph,searchbycode:searchbycode, searchbyemail:searchbyemail,profession: profession, division:division,country: country,pagelimit: pagelimit,status:status},
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
            $('#profession').val('');
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