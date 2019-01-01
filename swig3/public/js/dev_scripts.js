

jQuery(document).ready(function ($) {
    
    $('.supervisordetails').hide();
    $('#employee_id').on("change", function () {
        var employee_id = $(this).val();
        if (employee_id != '')
        {
            $.ajax({
                type: 'POST',
                url: '../pos_sales/getsupervisordetails',
                data: 'employee_id=' + employee_id,
                success: function (return_data) {

                    $('.supervisordetails').html(return_data);
                    $('.supervisordetails').show();
                }
            });
        }
        else
        {
             $('.supervisordetails').hide();
        }
    });
    $('.cashierdetails').hide();
    $('#cash_employee_id').on("change", function () {
        var employee_id = $(this).val();
        if (employee_id != '')
        {
            $.ajax({
                type: 'POST',
                url: '../pos_sales/getcashierdetails',
                data: 'employee_id=' + employee_id,
                success: function (return_data) {

                    $('.cashierdetails').html(return_data);
                    $('.cashierdetails').show();
                }
            });
        }
        else
        {
             $('.cashierdetails').hide();
        }
    });
$('.addinventorysubcat').hide();
    $('#sub_cat_name').on("keyup", function () {
        $('.inventorysubcatName').removeClass('validV1');
        $('.inventorysubcatName').removeClass('errorV1');
        $('.inventorysubcatName').addClass('ajaxLoaderV1');
        var subcatName = $(this).val();
        subcatName = $.trim(subcatName);
        if (subcatName != '')
        {
            $.ajax({
                type: 'POST',
                url: '../inventory_sub_category/checksubcategories',
                data: 'subcatName=' + subcatName,
                success: function (return_data) {
                    
                    if (return_data == 0)
                    {
                        $('.inventorysubcatName').removeClass('errorV1');
                        $('.inventorysubcatName').addClass('validV1');
                        $('.addinventorysubcat').show();
                        
                    }
                    else
                    {
                       
                        $('.inventorysubcatName').removeClass('validV1');
                        $('.inventorysubcatName').addClass('errorV1');
                        $('.addinventorysubcat').hide();
                    }
                }
            });
        }
        else
        {
            $('.inventorysubcatName').removeClass('validV1');
            $('.inventorysubcatName').addClass('errorV1');
            $('.addinventorysubcat').hide();
        }

    });
    
    $('.compoundunitsection').hide();
    $('#unit_type').on('change', function () {
        var unit_type = $(this).val();
        if(unit_type == 'SIMPLE')
        {
            
            $('.simpleunitsection').show();
            $('.compoundunitsection').hide();
        }
        else
        {
            
            $('.simpleunitsection').hide();
            $('.compoundunitsection').show();
        }
    }); 
    $('.addinventoryBtn').hide();
    $('#product_code').on("keyup", function () {
        $('.productCode').removeClass('validV1');
        $('.productCode').removeClass('errorV1');
        $('.productCode').addClass('ajaxLoaderV1');
        var product_code = $(this).val();
        product_code = $.trim(product_code);
        if (product_code != '')
        {
            $.ajax({
                type: 'POST',
                url: '../inventory/checkproductcode',
                data: 'product_code=' + product_code,
                success: function (return_data) {
                    if (return_data == 0)
                    {
                        $('.productCode').removeClass('errorV1');
                        $('.productCode').addClass('validV1');
                        $('.addinventoryBtn').show();
                    }
                    else
                    {
                        $('.productCode').removeClass('validV1');
                        $('.productCode').addClass('errorV1');
                        $('.addinventoryBtn').hide();
                    }
                }
            });
        }
        else
        {
            $('.productCode').removeClass('validV1');
            $('.productCode').addClass('errorV1');
            $('.addinventoryBtn').hide();
        }

    });
    $('.selectedCompany').hide();
    $('#branch_id').on("change", function () {
        var branch_id = $(this).val();
        if (branch_id != '')
        {
            $.ajax({
                type: 'POST',
                url: '../../branchsales/pos_sales/getbranchname',
                data: 'branch_id=' + branch_id,
                success: function (return_data) {
    
                    $('.selectedCompany').html(return_data);
                    $('.selectedCompany').show();
                }
            });
        }
        else
        {
             $('.selectedCompany').hide();
        }
        
    });

    $('.shifttimings').hide();
    $('#job_shift').on("change", function () {
        var job_shift = $(this).val();
        if (job_shift != '')
        {
            $.ajax({
                type: 'POST',
                url: '../pos_sales/getjobtimings',
                data: 'job_shift=' + job_shift,
                success: function (return_data) {
    
                    $('.shifttimings').html(return_data);
                    $('.shifttimings').show();
                }
});
        }
        else
        {
            $('.shifttimings').hide();
        }
    });
 $('.reason_details').hide();
    $('#pos_reason').on("change", function () {
        var pos_reason = $(this).val();
        if (pos_reason == 'other')
        {
   $('.reason_details').show();
        }
        else
        {
             $('#reason_details').text('');
            $('.reason_details').hide();
            
        }
    });
    $('.employee_contact_email').hide();
    $('#email_radio').on("change", function () {
        $('.employee_contact_email').hide();
        $('.employee_email').show();
    });
    
    $('#contact_email_radio').on("change", function () {
        $('.employee_email').hide();
        $('.employee_contact_email').show();
    });
    
    
    $('body').on('click', '.delete', function() {
        var usr = $(this).attr("usr-attr");
        if(usr==undefined){
            usr='';
        }else{
            usr= usr+" ";
        }
        return confirm("Are you sure to delete "+usr+"?");
    });
    
    
    $('body').on('click', '.disable', function() {
        var usr = $(this).attr("usr-attr");
        if(usr==undefined){
            usr='';
        }else{
            usr= usr+" ";
        }
        
         if($(this).text()=='Disable'){
             return confirm("Are you sure to disable "+usr+"?");
         }
         
         if($(this).text()=='Enable'){
             return confirm("Are you sure to enable "+usr+"?");
         }
    });
    
    $('body').on('keypress', '.numberwithdot', function(event) {
        return isNumber(event, this)
    });
   
    $('body').on('change', '.reqdocument', function () {

        var files = this.files;
        var size = files[0].size/1000; //now size in kb

        if( size > 5000){
           alert('Please upload less than 5 Mb document');
           $(this).val('');
           return false;
        }
        return true;
    });
    
    $('body').on('click', '.viewreqdocument', function () {
        var documentfile = $(this).attr('href');
        var arr = documentfile.split(".");      // Split the string using dot as separator
        var extension = arr.pop();       // Get last element
        var myArray = ["doc", "docx","xls","xlsx"];
        var arrImages=["img","png","jpeg","jpg"];

        if ($.inArray(extension, myArray) == -1) {
            if ($.inArray(extension, arrImages) != -1) {
                $('.btnImgPrint').show();
            } else {
                $('.btnImgPrint').hide();
            }
            
            $('.commonModalHolder').show();
        }
            
        $('#frame').attr('src', documentfile)
        return false;
    });
    
    // THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
    function isNumber(event,element) {

        var key = window.event ? event.keyCode : event.which;

        if (event.keyCode == 37 || event.keyCode == 39) {
            return true;
        }
        else if (
                (key != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
                (key != 8) &&      // “.” CHECK DOT, AND ONLY ONE.
                (key < 48 || key > 57)
                ) {
            return false;
        }
        else {
            return true;
        }
    }    
    
});

function amountformat(value){
    return parseFloat($.trim(value)).toFixed(2);
}