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
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });

        

    });

    function getData(page) {

        var searchbymaster = $('#searchbymaster').val();

        var pagelimit = $('#page-limit').val();

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbymaster: searchbymaster, pagelimit: pagelimit},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    //console.log(data);

                    $(".policybody").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('organizationchart/policy/exportdata') }}" method="post">
        <header class="pageTitle">
            <h1><span>Policy</span> List</h1>
        </header>	
       
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
<!--        <a class="btnAction print bgGreen" href="#">Print</a>
        <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>-->

        <div class="fieldGroup" id="fieldSet1">
            <div class="customClear"></div>
        </div>

        <div class="listHolderType1">
            <div class="listerType1 reportLister"> 

                <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
               
                <div id="tblregion">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">

                                <td>
                                    Sl.No.
                                </td>

                                <td style="min-width: 150px;">
                                   Policy Master

                                </td>
                                <td style="min-width: 120px;">
                                   Alias

                                </td>


                                <td style="min-width: 300px;">
                                    Content

                                </td>
                                
                                <td style="min-width: 90px;">
                                    Created Date
                                </td>
                               
                                <td>
                                    View
                                </td>
                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">

                                <td></td>
                                <td class="filterFields">
                                    <div class="custCol-12">
                                        <select id="searchbymaster" name="searchbymaster" class="searchbymaster">
                                            <option value="">All</option>
                                            @foreach ($policymaster as $policy)
                                            <option value="{{$policy->id}}">{{$policy->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>

                                <td >
                                </td>
                               
                                <td >
                                </td>
                               
                                <td>
                                </td>
                                
                                <td>
                                </td>

                            </tr>

                        </thead>

                        <tbody class="policybody" id='policybody'>
                            @include('organizationchart/policy_list/result')

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

    $('.searchbymaster').on("change", function () {
            search();
    });

    $('#page-limit').on("change", function () {
        search();
    });

    function search()
    {
        var searchbymaster = $('#searchbymaster').val();
       
        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'policy_list',
            data: {searchbymaster: searchbymaster, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.policybody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.policybody').html('<tr><td colspan="3"><p class="noData">No Records Found</p></td></tr>');
                }
            }
        });
    }



    $(function () {

        $('#reset').click(function () {
            window.location.href = '{{url("organizationchart/policy_list")}}';
        });

    });

    function funExportData(strType)
    {
        if (strType == "PDF") {
            $('#excelorpdf').val('PDF');
        } else {
            $('#excelorpdf').val('Excel');
        }

        document.getElementById("pdfgenerator").submit();
    }

</script>
@endsection
