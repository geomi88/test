@extends('layout.admin.menu')
@section('content')
@section('title', 'Agent List')
<?php 
$agentList=$pageData['agentList'];
$municipalities=$pageData['municipalities'];
?>
			<div class="adminPageHolder adminAddPropertyHolder">
				<div class="mainBoxHolder">
					<div class="row">
						<div class="col-6 text-capitalize">
							<h2 class="mt-2">Agents / Owners</h2>
						</div>
						<div class="col-6 text-right">
							<a class="addPropertyBtn"  href="<?php echo url('/');?>/admin/add-agent">
								<figure>
									<img class="mr-1" src="images/iconPlus.png"> add new user
								</figure>
							</a>
						</div>
					</div>
					<hr>
					<div class="row text-capitalize">
						<div class="col-3">
							<input class="inputStyle" type="text" placeholder="Search by name, email" name="searchkey" id="searchkey">
						</div>
						<div class="col-3">
							
                                <select class="inputStyle" name="municipality" id="municipality">
								<option value="">Choose City</option>
                                @foreach($municipalities as $city)
								<option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
							</select>
                        </div>
                        <div class="col-3">
							
                                <select class="inputStyle" name="user_type" id="user_type">
								<option value="">Choose User Type</option>
                                <option value="1">Agent</option>
                                <option value="2">Owner</option>
							</select>
						</div>
						<div class="col-3">
							<button type="button" class="btnStyle" id="searchBtn">Search</button>
						</div>	
					</div>
					<table class="table text-capitalize tableStyleHolder mt-3">
						<tr>
							<th>Sl.No</th>
							<th>name</th>
							<th>city</th>
							<th>phone</th>
							<th>email id</th>
							<th>actions</th>
						</tr>
                                                 <tbody id="agentlist">
						@include('admin.agent.agent-list-result')
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
        
        var municipality=$('#municipality').val();
        var searchKey=$('#searchkey').val();
        var user_type=$('#user_type').val();
       
       $.ajax({
            type: 'POST',
            url: 'agent-listing',
            data: {municipality: municipality, searchKey: searchKey,user_type : user_type},
            
            success: function (return_data) { 
                if (return_data != '')
                {
                    $('#agentlist').html('');
                    $('#agentlist').html(return_data);
                   
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
    
    function getData(page) {

        var municipality=$('#municipality').val();
        var searchKey=$('#searchkey').val();
        

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {municipality: municipality, searchKey: searchKey},
            
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                   // console.log(data);

                  $('#agentlist').empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }
    
</script>
@endsection
    
