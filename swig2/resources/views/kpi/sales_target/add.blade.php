@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Set Sales <span>Target</span></h1>
    </header>
   
    <div class="memberListHolder">
        
        <div class="mode quarterFilter" style="">               

            <ul class="classDateMode tabV2">                       
                <!--<li><a href="javascript:void(0);">Month</a></li>-->
                <li class="selectedDateMode selected"><a href="javascript:void(0);">Quarter</a></li>
                <!--<li><a href="javascript:void(0);">Year</a></li>-->
            </ul>
            <div class="outputHolder quarterHolder">
                <a title="Prev" id="datePrev" class="btnLeft"  href="javascript:void(0);"></a>
                <div class="selectQur">
                    <input type="text" id="dateFilter" class="selectQur" readonly="readonly" value="2011">
                </div>
                <a title="Next" id="dateNext" class="btnRight" href="javascript:void(0);"></a>

            </div>
        </div>

        <input type="text" id="txttargetamount" class="amountAssign number" placeholder="Enter Amount">
        <div class="customClear"></div>
        <div id="branches">
            
        </div>
        <input type="hidden" id="startdate" >
        <input type="hidden" id="enddate" >
        
        <a href="javascript:void(0)" class="commonBtn bgGreen addBtn" id="btnAssign">Assign</a>
    </div>
    
</div>
<div class="commonLoaderV1"></div>
<script>
var arrSelectedBranches=[];
$(document).ready(function (){
    
    //date picker starts
    var d = new Date();
    var dateSelection = {
        SelectionMode: 'Quarter',
        LeftNavControl: $('#datePrev'),
        RightNavControl: $('#dateNext'),
        LabelControl: $('#dateFilter'),
        StartDate: new Date(d.getFullYear(), 0, 1),
        EndDate: new Date(d.getFullYear(), 11, 31),
        SelectedYear: d.getFullYear(),
        SelectedMonth: d.getMonth(),
        SelectedQuarter: Math.floor(d.getMonth() / 3),
        MonthNames: new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"),
        MonthAbbreviations: new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"),
        Quarters: new Array("Q1", "Q2", "Q3", "Q4"),
        LeftClick: function () {
            if (dateSelection.SelectionMode == 'Year') {
                dateSelection.SelectedYear -= 1;
            }
            if (dateSelection.SelectionMode == 'Month') {
                if (dateSelection.SelectedMonth == 0) {
                    dateSelection.SelectedMonth = 11;
                    dateSelection.SelectedYear -= 1;
                }
                else {
                    dateSelection.SelectedMonth -= 1;
                }
            }
            if (dateSelection.SelectionMode == 'Quarter') {
                if (dateSelection.SelectedQuarter == 0) {
                    dateSelection.SelectedQuarter = 3;
                    dateSelection.SelectedYear -= 1;
                }
                else {
                    dateSelection.SelectedQuarter -= 1;
                }
            }
             
            dateSelection.show();
            getSalesTargets($.trim($("#dateFilter").val()));
        },
        RightClick: function () {           
            if (dateSelection.SelectionMode == 'Year') {
                dateSelection.SelectedYear += 1;
            }
            if (dateSelection.SelectionMode == 'Month') {
                if (dateSelection.SelectedMonth == 11) {
                    dateSelection.SelectedMonth = 0;
                    dateSelection.SelectedYear += 1;
                }
                else {
                    dateSelection.SelectedMonth += 1;
                }
            }
            if (dateSelection.SelectionMode == 'Quarter') {
                if (dateSelection.SelectedQuarter == 3) {
                    dateSelection.SelectedQuarter = 0;
                    dateSelection.SelectedYear += 1;
                }
                else {
                    dateSelection.SelectedQuarter += 1;
                }
            }
             
            dateSelection.show();
            getSalesTargets($.trim($("#dateFilter").val()));
        },
        init: function () {
            dateSelection.LeftNavControl.bind('click', dateSelection.LeftClick);
            dateSelection.RightNavControl.bind('click', dateSelection.RightClick);          
        },
 
        show: function () {
            if (dateSelection.SelectionMode == 'Year') {
                dateSelection.LabelControl.val('Jan - Dec ' + dateSelection.SelectedYear);
            }
            if (dateSelection.SelectionMode == 'Month') {
                dateSelection.LabelControl.val(dateSelection.MonthNames[dateSelection.SelectedMonth] + ' ' + dateSelection.SelectedYear);
            }
            if (dateSelection.SelectionMode == 'Quarter') {
                dateSelection.LabelControl.val(dateSelection.Quarters[dateSelection.SelectedQuarter] + ' ' + dateSelection.SelectedYear);
            }
            
            $("#startdate").val(dateSelection.getStartDate());
            $("#enddate").val(dateSelection.getEndDate());
        },
        getStartDate: function () {
            if (dateSelection.SelectionMode == 'Year') {
                dateSelection.StartDate.setFullYear(dateSelection.SelectedYear);
                dateSelection.StartDate.setMonth(0,1);
                dateSelection.StartDate.setDate(1);
            }
            if (dateSelection.SelectionMode == 'Month') {
                dateSelection.StartDate.setFullYear(dateSelection.SelectedYear);
                dateSelection.StartDate.setMonth(dateSelection.SelectedMonth,1);
                dateSelection.StartDate.setDate(1);
            }
            if (dateSelection.SelectionMode == 'Quarter') {
                dateSelection.StartDate.setFullYear(dateSelection.SelectedYear);
                dateSelection.StartDate.setDate(1);
                dateSelection.StartDate.setMonth(dateSelection.SelectedQuarter * 3);
            }
            return dateSelection.StartDate;
        },
        getEndDate: function () {
            if (dateSelection.SelectionMode == 'Year') {
                dateSelection.EndDate.setFullYear(dateSelection.SelectedYear);
                dateSelection.EndDate.setMonth(11);
                dateSelection.EndDate.setDate(dateSelection.EndDate.getDaysInMonth());
            }
            if (dateSelection.SelectionMode == 'Month') {
                dateSelection.EndDate.setFullYear(dateSelection.SelectedYear);
                dateSelection.EndDate.setMonth(dateSelection.SelectedMonth,1);
                dateSelection.EndDate.setDate(dateSelection.EndDate.getDaysInMonth());
            }
            if (dateSelection.SelectionMode == 'Quarter') {
                var now = dateSelection.EndDate;
                dateSelection.EndDate.setFullYear(dateSelection.SelectedYear);
                dateSelection.EndDate.setMonth(dateSelection.SelectedQuarter * 3 + 2,1);
                dateSelection.EndDate.setDate(new Date(now.getFullYear(), now.getMonth()+1, 0).getDate());
                
            }
            return dateSelection.EndDate;
        }
    };
 
    dateSelection.init();
    dateSelection.show(); 
    
    $('.classDateMode li a').click(function () {
        dateSelection.SelectionMode = $(this).text();       
        dateSelection.show();
        $('.classDateMode li').removeClass('selectedDateMode');
        $(this).parent().addClass('selectedDateMode');     
    });
    
     $('.classDateMode.tabV2 li').on('click', function () {
        $('.classDateMode.tabV2 li').removeClass('selected');
        $(this).addClass('selected');
    });
    
    //date picker ends
    
    
    getSalesTargets($.trim($("#dateFilter").val()));
        
    $('#btnAssign').on('click', function () {
        $(".chkbranches").each(function() {
            var chkid = $(this).attr("id");
            if($("#"+chkid).prop('checked') == true){
                arrSelectedBranches.push(chkid);
            }

        });
        
        if(arrSelectedBranches.length==0){
            alert("Please Select Atleast One Branch")
            return;
        }
        
        var arraData = encodeURIComponent(JSON.stringify(arrSelectedBranches));
        var txttargetamount = $("#txttargetamount").val();
        var strQuarter = $.trim($("#dateFilter").val());
        var datStartDate = $.trim($("#startdate").val());
        var datEndDate = $.trim($("#enddate").val());
        
        $.ajax({
            type: 'POST',
            url: '../sales_target/store',
            data: '&arrData=' + arraData + '&txttargetamount=' + txttargetamount + 
                   '&strQuarter=' + strQuarter + '&datStartDate=' + datStartDate +
                   '&datEndDate=' + datEndDate,
            success: function (return_data) {
                window.location.href = '{{url("kpi/sales_target")}}';
            },
            error: function (return_data) {
                window.location.href = '{{url("kpi/sales_target")}}';
            }
        });

    });
    
       
    $('.number').keypress(function(event) {

        if(event.which == 8 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.which == 0) {
             return true;
        } else if((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)){
             event.preventDefault();
        }

        if($(this).val() == parseFloat($(this).val()).toFixed(2))
        {
            event.preventDefault();
        }

        return true;
   });
    

});
   
function getSalesTargets(strQuarter){
    $.ajax({
        type: 'POST',
        url: '../sales_target/getsalestargets',
        data: '&strQuarter=' + strQuarter,
        success: function (return_data) {
            if(return_data!=-1){
                filldatatogrid(return_data);
            } else {
                window.location.href = '{{url("kpi/sales_target")}}';
            }
        },
        error: function (return_data) {
            window.location.href = '{{url("kpi/sales_target")}}';
        }
    });
}

function filldatatogrid(result){
    
    var strHtml='';
    
    if(result!=-1){
        var data=result.arrBranchList;
        for(var i=0;i<data.length;i++){
            if(data[i].created_at != ''){
                strHtml+='<div class="listHolderV2 targetList assigned">';
            }else{
                strHtml+='<div class="listHolderV2 targetList">';
            }
                strHtml+='<div class="commonCheckHolder">'+
                            '<label>'+
                            '<input name="gender" id='+ data[i].branch_id +' class="chkbranches" type="checkbox">'+
                            '<span></span>'+
                       '</label>'+
                    '</div>'+
                    '<h2>'+data[i].branch_code+' : '+data[i].name+'</h2>';
            if(data[i].created_at != ''){
                strHtml+='<span>Target assigned on '+data[i].created_at+'</span>'+
                    '<div class="targetAmount">'+data[i].target_amount+'</div>';
//                    '<div class="YearlyTarget">'+data[i].yearly_target+'</div>';    
            }else{
                strHtml+='<span>Target not assigned</span>'+
                   '<div class="targetAmount">- -</div>';
//                   '<div class="YearlyTarget">'+data[i].yearly_target+'</div>';
            }
            strHtml+='</div>';
        }
       
       if(result.allowSave==0){
            $("#btnAssign").hide();
            $("#txttargetamount").hide();
       }else{
            $("#btnAssign").show();
            $("#txttargetamount").show();
       }
       
    }else{
        strHtml='';
    }
    
    $("#branches").html(strHtml);
   
}
</script>
@endsection