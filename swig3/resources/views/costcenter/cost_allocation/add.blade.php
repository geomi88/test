@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Branch <span>Cost Allocation</span></h1>
    </header>	

    <form action="" method="post" id="frmmain">
        <div class="fieldGroup clsparentdiv" id="fieldSet1">

            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Branch</label>
                        <select class="chosen-select" name="cmbbranch" id="cmbbranch">
                            <option selected value=''>Select Branch</option>
                            @foreach ($branches as $branch)
                            <option value='{{ $branch->id }}'>{{$branch->branch_code}} : {{$branch->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            
            
        </div>
    </form>	
    <form id="frmcost">
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder">
                    <label>Cost Name</label>
                    <select  name="costname" id="costname">
                        <option selected value=''>Select Cost</option>
                        @foreach ($costs as $cost)
                        <option value='{{ $cost->id }}'>{{$cost->name}}</option>
                        @endforeach
                    </select>
                    <span class="commonError"></span>
                </div>
            </div>
        </div>
        
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder">
                    <label>Amount</label>
                    <input type="text" name="costamount" id="costamount" autocomplete="off" placeholder="Enter Amount">
                </div>
            </div>
            <div class="custCol-4" style="margin-top: 41px;">
                <a class="btnAction action bgGreen" id="add_cost" > Add</a>
            </div>
        </div>
        
        
        <div class="listHolderType1">
            <div class="listerType1"> 
                <table style="width: 100%;" cellspacing="0" cellpadding="0">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td>Cost Category</td>
                            <td>Cost Amount</td>
                            <td>Remove</td>
                        </tr>
                    </thead>
                    <tbody id="costcategorylist">
                        <tr><td>No cost category added</td></tr>
                    </tbody>
                </table>

            </div>					
        </div>
    </form>
    
    <div class="customClear"></div>
    <div class="commonLoaderV1"></div>
    
    <div class="custRow">
        <div class="custCol-4">
            <input type="button" value="Create" id="btnCostAllocation" class="commonBtn bgGreen addBtn" >
        </div>
    </div>
</div>
<script>
    var arrCostList = [];
    var intGlobalEditIndex=-1;
    $(document).ready(function ()
    {

        $("#frmmain").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                var id=$(element).attr("id")+"_chosen";
                if($(element).hasClass('valErrorV1')){ 
                  $("#"+id).find('.chosen-single').addClass('chosen_error');
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                cmbbranch: {required: true},
            },
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                cmbbranch: "Select Branch",
            }
        });

        $("#frmcost").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                costname: {required: true},
                costamount: {
                                required: true,
                                number: true
                            },
            },
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                costname: "Select Cost Name",
                costamount:{required: "Enter Cost Amount",
                            number: "Enter Number only"},
            }
        });


        $('#btnCostAllocation').on('click', function () {

            if (!$("#frmmain").valid()) {
                return;
            }

            if (arrCostList.length == 0) {
                alert("Please Add Atleast One Cost");
                return;
            }

            var arraData = {
                branch_id: $("#cmbbranch").val(),
            }

            arraData = encodeURIComponent(JSON.stringify(arraData));
            var arrQueries = encodeURIComponent(JSON.stringify(arrCostList));
            
            $('#btnCostAllocation').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../cost_allocation/store',
                data: '&arraData=' + arraData + '&arrCostList=' + arrQueries,
                success: function (return_data) {
                    $('#btnCostAllocation').removeAttr('disabled');
                    window.location.href = '{{url("costcenter")}}';
                },
                error: function (return_data) {
                    $('#btnCostAllocation').removeAttr('disabled');
                    window.location.href = '{{url("costcenter")}}';
                }
            });
            
            $('#btnCostAllocation').removeAttr('disabled');
        })
        
        

        $('#add_cost').on('click', function () {
            
            if (!$("#frmcost").valid()) {
                return;
            }
            
            var arraData = {
                costid: $("#costname").val(),
                costname: $("#costname :selected").text(),
                costamount: $("#costamount").val(),
                
            }
            
            arrCostList.push(arraData);
            
            $("#costname").val('');
            $("#costamount").val('');
            showcostlist();

        })
        
    });

    function showcostlist()
    {
        $("#costcategorylist").html('<tr><td>No cost added<td></tr>');
        if (arrCostList.length > 0) {
            var strHtml = '';
            for (var i = 0; i < arrCostList.length; i++) {
                strHtml += '<tr><td>' + arrCostList[i].costname + '</td><td>' + arrCostList[i].costamount + '</td><td><a href="javascript:remove(' + i + ')">Remove</a></td></tr>';
            }
            $("#costcategorylist").html(strHtml);
        }
    }

    function remove(index) {
        arrCostList.splice(index, 1);
        showcostlist();
    }

</script>
@endsection