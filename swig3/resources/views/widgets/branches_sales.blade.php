<div class="regionalSales allSalesReport">
    <h3 class="commonHeadingV2">Branches Sales</h3><input type="text" name="search_branch" id="search_branch" onkeyup="search()">
    <input type="hidden" name="search_key" id="search_key" value="<?php echo $config['search_key'];?>">
    <div class="customClear"></div>
    <div id="ajaxappend">
    <?php foreach($branches_sales as $branch_sale) {?>
    <div class="statusList <?php echo $branch_sale['profit'];?>">
        <b><a href="{{ URL::to('kpi/branch/view', ['id' => Crypt::encrypt($branch_sale['branch_id'])]) }}"><?php echo $branch_sale['branch_name'];?></a></b>
        <em><?php echo Customhelper::numberformatter($branch_sale['total']);?></em>
    </div>
    <?php } ?>
    </div>  
    
</div>
<script>
    function search(){
     
         var search_branch = $('#search_branch').val();
         var search_key = $('#search_key').val();
         
         
      
        $.ajax({
            type: 'POST',
            url: 'filterbranch',
            data: {search_branch: search_branch,search_key:search_key},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
              
               $('.statusList').hide();
               var tab="";
              for(var i=0;i<return_data.branches_sales.length;i++){
                  //var aray=['id'=Crypt::encrypt("+return_data.branches_sales[i]['branch_id']+")];
                
             // console.log(return_data.branches_sales[i]["encode_id"]) ;
        
                //   var checkurl="URL::to('kpi/branch/view',['id'=>"+return_data.branches_sales[i]['encode_id']+"])";
           //   console.log(checkurl) ;
                tab=tab+' <div class="statusList ' + return_data.branches_sales[i]["profit"]+'">'+
                          '<b><a href="" onclick="viewdetails(\''+ return_data.branches_sales[i]["encode_id"]+'\')">'+
                  return_data.branches_sales[i]["branch_name"]+'</a></b><em>'+return_data.branches_sales[i]["total"]+'</em></div>';
      
                
                
              }  
              $('#ajaxappend').html(tab)
//                if (return_data != '')
//                {
//                   // alert(return_data);
//                    $('.pos').html(return_data);
//                    $(".commonLoaderV1").hide();
//                }
//                else
//                {
//                    $(".commonLoaderV1").hide();
//                    $('.pos').html('<p class="noData">No Records Found</p>');
//                }
            }
        });
        
        
       
    }
    
    function viewdetails(id){

 window.location = 'branch/view/' +id;

}
    </script>