@extends('layout.admin.menu')
@section('content')
@section('title', 'Property List')
<?php 
$propertyList=$pageData['propertyList'];
$agentList=$pageData['agentList'];
$districts=$pageData['districts'];
$agentId=$pageData['agentId'];
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
							<h2 class="mt-2">properties</h2>
						</div>
						<div class="col-6 text-right">
							<a class="addPropertyBtn" href="<?php echo url('/');?>/admin/add-property">
								<figure>
									<img class="mr-1" src="{{URL::asset('admin')}}/images/iconPlus.png"> add new property
								</figure>
							</a>
						</div>
					</div>
					<hr>
					<div class="row text-capitalize">
						<input type="hidden" name="agent" id="agent" value="{{$agentId}}" >
						<input type="hidden" name="id" id="id" value="{{\Crypt::encrypt($agentId)}}" >
							
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
							<th>beds</th>
							<th>bath</th>
							<th>agent</th>
							<th>actions</th>
						</tr>
                                                <tbody id="property-list">
						@include('admin.agent.agent-property-list-result')
						</tbody>
					</table>

				</div>
			</div>
<script>
       $(document).on('click', '.pagination a', function (event)
        {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });
        
    $('#searchBtn').click(function(){
        var agent=$('#agent').val();
        var district=$('#district').val();
        var searchKey=$('#searchkey').val();
        var id=$('#id').val();
        
       $.ajax({
            type: 'POST',
            url: '<?php echo url('/');?>/admin/agent-property-listing/'+id,
            data: {agent: agent, district: district, searchKey: searchKey},
            
            success: function (return_data) { 
                if (return_data != '')
                {
                    $('#property-list').html('');
                    $('#property-list').html(return_data);
                   
                } else
                {
                    $('#property-list').html('');
                    $('#property-list').html("No Properties Added");
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
    
    function getData(page) {

        var agent=$('#agent').val();
        var district=$('#district').val();
        var searchKey=$('#searchkey').val();
        

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {agent: agent, district: district, searchKey: searchKey}, // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                   // console.log(data);

                    $('#property-list').empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }
    
</script>
@endsection
			