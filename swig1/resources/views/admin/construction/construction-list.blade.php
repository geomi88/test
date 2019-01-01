@extends('layout.admin.menu')
@section('content')
@section('title', 'Property List')
<?php 
$projectsList=$pageData['projectsList'];
//$agentList=$pageData['agentList'];
$districts=$pageData['districts'];
?>
        <div class="adminPageHolder adminAddPropertyHolder">
				
				<div class="mainBoxHolder">
					<div class="row">
						<div class="col-6 text-capitalize">
							<h2 class="mt-2">construction</h2>
						</div>
						<div class="col-6 text-right">
							<a class="addPropertyBtn" href="<?php echo url('/');?>/admin/add-construction">
								<figure>
									<img class="mr-1" src="{{URL::asset('admin')}}/images/iconPlus.png"> add new construction
								</figure>
							</a>
						</div>
					</div>
					<hr>
					<div class="row text-capitalize">

						<div class="col-3">
							<select class="inputStyle" name="district" id="district">
								<option value="">Choose Districts</option>
                                                              
                                                                @foreach($districts as $district)
								<option value="{{$district->id}}">{{$district->name}}</option>
                                                                @endforeach
							</select>
						</div>
						<div class="col-3">
							<input class="inputStyle" type="text" placeholder="Search" name="searchkey" id="searchkey">
						</div>
						<div class="col-3">
							<button type="button" class="btnStyle" id="searchBtn">Search</button>
						</div>	
					</div>
					<table class="table text-capitalize tableStyleHolder mt-3" >
						<tr>
							<th>Sl.No</th>
							<th>Name</th>
							<th>District</th>
							<th>Url</th>
							<th>Actions</th>
						</tr>
                                                <tbody id="property-list">
						@include('admin.construction.construction-list-result')
						</tbody>
					</table>

				</div>
			</div>
<script>
    
    $('#searchBtn').click(function(){
      //  var agent=$('#agent').val();
        var district=$('#district').val();
        var searchKey=$('#searchkey').val();
        
       $.ajax({
            type: 'POST',
            url: 'construction-listing',
            data: { district: district, searchKey: searchKey},
            
            success: function (return_data) { 
                if (return_data != '')
                {
                    $('#property-list').html('');
                    $('#property-list').html(return_data);
                   
                } else
                {
                   $('#property-list').html('');
                    $('#property-list').html("No Projects Found");  
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
			