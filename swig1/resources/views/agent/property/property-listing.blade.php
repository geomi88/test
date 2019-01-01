@extends('layout.agent.agentlayout')
@section('property','active')
@section('content')
@section('title', 'Property List')
		<div class="allListingWrapper eqHeightHolder">
			<div class="mainBoxHolder">
                           
				<div class="row">
					<div class="col-2 text-capitalize mt-2">
						<h2>your listings</h2>
					</div>
					<div class="col-10 text-capitalize">
						<div class="filterHolder">
							<div class="dropList">
								<select name='building' id='building' class='inputstyle'>
                                                                    <option value="">All</option>
                                                                    @foreach($buildingType as $buildings)
                                                                    <option value="{{$buildings->id}}">{{$buildings->name}}</option>
                                                                    @endforeach
									
                                                                </select>	
							</div>
							<div class="dropList">
								<select name='city' id='city' class='inputstyle'>
                                                                     <option value="">All</option>
                                                                    @foreach($municipality as $city)
									<option value="{{$city->id}}">{{$city->name}}</option>
                                                                       
                                                                    @endforeach
								</select>	
							</div>
							<div class="dropList">
                                                            
                                                            <input placeholder="Bedroom" class="inputStyle" type="text" name="bedroom" id="bedroom" >
									
							</div>
                                                        <div class="dropList">
                                                           <input placeholder="Bathroom" class="inputStyle" type="text" name="bathroom" id="bathroom" >
									
							</div>
							
							<div class="dropList text-capitalize advnceFilterHolder">
								<p class="dropListContent dropClick">Advance</p>
								<ul class="dropdownOpen">
									<li>
										<div class="checkboxStyleHolder">
											<input id="garden" name="garden" type="checkbox" >
											<label for="garden"><span>garden</span></label>
										</div>
									</li>
									<li>
										<div class="checkboxStyleHolder">
											<input id="terrace"  name="terrace"  type="checkbox">
											<label for="terrace"><span>terrace</span></label>
										</div>
									</li>
								</ul>	
							</div>
							
							<button type="button" class="btnStyle" id="searchList" onclick="searchList()">search</button>
						</div>
					</div>
				</div>
                           
			</div>
                    <div class="listResult">
			
				@include('agent.property.property-list-result')
                                
                    </div>
		</div>
@endsection	

<script>
$(document).ready(function(){
    function equalHeight(){
                                        $('.eqHeightHolder').each(function(){  
                                        var highestBox = 0;
                                        $(this).find('.eqHeightInner').each(function(){
                                            if($(this).height() > highestBox){  
                                                highestBox = $(this).height();  
                                            }
                                        })
                                        $(this).find('.eqHeightInner').height(highestBox);
                                    });
                                }
                                
                              $(window).load(function(){
                                    equalHeight();
                                });  

}); 

function equalHeight(){
       $('.eqHeightHolder').each(function(){  
        var highestBox = 0;
         $(this).find('.eqHeightInner').each(function(){
          if($(this).height() > highestBox){  
              highestBox = $(this).height();  
              }
            })
           $(this).find('.eqHeightInner').height(highestBox);
          });
         }
         
function searchList(){

    var building=$('#building').val();
    var city=$('#city').val();
    var bedroom=$('#bedroom').val();
    var bathroom=$('#bathroom').val();
    var gardenVal="";
    if($('#garden').is(':checked')){
       gardenVal="1";
    }
    var terraceVal="";
    if($('#terrace').is(':checked')){
       terraceVal="1";
    }
   
    
    $.ajax({
        
        type:'post',
        url:"<?php echo url('/');?>/agent/property-listing",
        data:{building:building,city:city,bedroom:bedroom,bathroom:bathroom,garden:gardenVal,terrace:terraceVal},
        success: function (data) {
           if (data != '')
                {
                    $('.listResult').html('');
                    $('.listResult').html(data);
                    $(".allListingWrapper .allListBoxHolder").each(function() {
				  	var imgPath = $(this).find(".listImgHolder img").attr('src');
				  	$(this).find(".listImgHolder").css('background-image', 'url("'+ imgPath +'")');
				});
                    equalHeight();
                    
                } else
                {
                    
                    $('.listResult').html('<div class="errorMsg1"><p>No Records Found</p></div>');
                }
        }
        
    });
    
  
}

 function confirmation(){
        if(confirm("Are You Sure to Proceed?")){
            return true;
        }else{
            return false;
        }
    }
    
     $('.dropClick').click(function(e){
       $(this).next('.dropdownOpen').toggle();
    });
    $(document).on('click', function(event) {
	if(!$(event.target).closest('.dropClick, .dropdownOpen').length) {
            $('.dropdownOpen').hide();	
	}
    });
    
 </script>