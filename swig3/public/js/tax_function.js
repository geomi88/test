var customJS;

jQuery(document).ready(function($) {


     function getTaxValue(){
        
        
          

            var cash_collection = $('#cash_collection').val();  
            var employee_type = $('#employee_type').val();
            
            var pos_date=$('#currentdate').val();
            
            
            if (cash_collection != '' && cash_collection != 0)
            {
                $.ajax({
                    type: 'POST',
                    url: 'helper/helper/gettaxamount',
                    data: 'cash_collection=' + cash_collection +'&pos_date='+ pos_date ,
                    success: function (return_data) {

                        $('#tax_amount').val(return_data.tax_amount); 
                        $('#sales_amount').val(return_data.sales_amount);
                      
                    }
                });
            }else{
                       $('#tax_amount').val(""); 
                       $('#sales_amount').val("");
            }
      
    }


  
  

   

    
});
