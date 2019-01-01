@extends('layout.admin.menu')
@section('content')
@section('title', 'Property List')
<?php 
$neighbourhoodList=$pageData['neighbourhoodList'];
$districts=$pageData['districts'];
?>
        <div class="adminPageHolder adminAddPropertyHolder">
				<!-- <div class="text-capitalize adminTitle">
					<h1>add new property</h1>
				</div> -->
				<!-- <div class="mainBoxHolder">
						
					</div> -->	
				<div class="mainBoxHolder">
					<div class="row">
						<div class="col-6 text-capitalize">
							<h2 class="mt-2">Neighbourhood</h2>
						</div>
						<div class="col-6 text-right">
							<a class="addPropertyBtn" href="<?php echo url('/');?>/admin/add-neighbourhood">
								<figure>
									<img class="mr-1" src="{{URL::asset('admin')}}/images/iconPlus.png"> add new neighbourhood
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
							<th>name</th>
							<th>city</th>
							<th>district</th>
							
							<th>actions</th>
						</tr>
                                                <tbody id="property-list">
						@include('admin.neighbourhood.neighbor-list-result')
						</tbody>
					</table>

				</div>
			</div>
<script>
    
    $('#searchBtn').click(function(){
        
        var district=$('#district').val();
        var searchKey=$('#searchkey').val();
        
       $.ajax({
            type: 'POST',
            url: 'neighbourhood-listing',
            data: { district: district, searchKey: searchKey},
            
            success: function (return_data) { 
                if (return_data != '')
                {
                    $('#property-list').html('');
                    $('#property-list').html(return_data);
                   
                } else
                {
                     $('#property-list').html('No Data Found');
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
			