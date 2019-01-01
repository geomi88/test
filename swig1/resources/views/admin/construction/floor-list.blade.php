@extends('layout.admin.menu')
@section('content')
@section('title', 'Property List')

        <div class="adminPageHolder adminAddPropertyHolder">
				<!-- <div class="text-capitalize adminTitle">
					<h1>add new property</h1>
				</div> -->
				<!-- <div class="mainBoxHolder">
						
					</div> -->	
				<div class="mainBoxHolder">
					<div class="row">
						<div class="col-6 text-capitalize">
							<h2 class="mt-2">construction</h2>
						</div>
						<div class="col-6 text-right">
							<a class="addPropertyBtn" href="{{ URL::to('admin/add-floor', ['id' => $projectId]) }}">
								<figure>
									<img class="mr-1" src="{{URL::asset('admin')}}/images/iconPlus.png"> add Floor
								</figure>
							</a>
						</div>
					</div>
					<hr>
					
					<table class="table text-capitalize tableStyleHolder mt-3" >
						<tr>
							<th>Sl.No</th>
							<th>Name</th>
							<th>Approx Area</th>
							<th>No of beds</th>
							<th>No of baths</th>
							<th>Actions</th>
						</tr>
                                                <tbody id="property-list">
						@include('admin.construction.floor-list-result')
						</tbody>
					</table>

				</div>
			</div>
<script>
    
    $('#searchBtn').click(function(){
        var agent=$('#agent').val();
        var district=$('#district').val();
        var searchKey=$('#searchkey').val();
        
       $.ajax({
            type: 'POST',
            url: 'property-listing',
            data: {agent: agent, district: district, searchKey: searchKey},
            
            success: function (return_data) { 
                if (return_data != '')
                {
                    $('#property-list').html('');
                    $('#property-list').html(return_data);
                   
                } else
                {
                    
                }
            }
        });
    });
    
     function confirmation(){
        if(confirm("Are You Sure to Proceed?")){
            return true;
        }else{
            return false;
        }
    }
    
   
    
</script>
@endsection
			