@extends('layouts.main')
@section('content')
 <script>
    $(document).ready(function()
    {
         var unit_type = $('#unisess').val();
        // alert(unit_type);
        if(unit_type == 'SIMPLE')
        {
            
            $('.simpleunitsection').show();
            $('.compoundunitsection').hide();
        }
        else if(unit_type == 'COMPOUND')
        {
            
            $('.simpleunitsection').hide();
            $('.compoundunitsection').show();
        }
         
        $('#unit_type').on('change', function () {
        var unit_type = $('#unit_type').val();
        
            $.ajax({
                type: 'POST',
                url: 'units/setsession',
                data: 'unit_type=' + unit_type,
                success: function (return_data) {
                    if(return_data)
                    {
                     //  $('#unit_type').val(unit_type);
                       document.getElementById("unisess").setAttribute("value", unit_type);
                    }
                                 }
            });
        if(unit_type == 'SIMPLE')
        {
            
            $('.simpleunitsection').show();
            $('.compoundunitsection').hide();
        }
        else if(unit_type == 'COMPOUND')
        {
            
            $('.simpleunitsection').hide();
            $('.compoundunitsection').show();
        }
       });
});
</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1><span>Units</span></h1>
    </header>
    <input type="hidden" name="unisess" id="unisess" value="{{$data['sess']}}">
                        
     <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
<!--                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Choose Type</label>
                        <select class="commoSelect" name="unit_type" id="unit_type">
                            <option <?php echo ($data['sess'] == "SIMPLE")?"selected":"" ?>  value='SIMPLE'>SIMPLE</option>
                            <option <?php echo ($data['sess'] == "COMPOUND")?"selected":"" ?>  value='COMPOUND'>COMPOUND</option>
                        </select>
                    </div>
                </div>-->
                <div class="custRow">
                <div class="custCol-12">
                     <a href="{{ action('Masterresources\UnitsController@add') }}" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>
             <div class="customClear"></div>
                
                <div class="simpleunitsection">
                    <div class="listerType1"> 
        <div class="headingHolder deskCont">
            <ul class="custRow">
                <li class="custCol-1">No</li>
                <li class="custCol-4">Name</li>
                <li class="custCol-4">Alias</li>
                <li class="custCol-4">Decimal Value</li>
                <li class="custCol-2"></li>
            </ul>
        </div>
        <div class="listerV1">
            
        <?php $n = $simple_units->perPage() * ($simple_units->currentPage()-1); ?>           
               @foreach ($simple_units as $simple_unit)
               @if($simple_unit->status==0)
               <?php $status='Enable'; ?>
               @else
               <?php $status='Disable'; ?>
               @endif
               <?php $n++; ?>
            <ul class="custRow">
		<li class="custCol-1"><span class="mobileCont">No</span>{{ $n }}</li>
		<li class="custCol-4"><span class="mobileCont">Name</span>{{$simple_unit->name}}</li>
		<li class="custCol-4"><span class="mobileCont">Alias</span>{{$simple_unit->formal_name}}</li>
		<li class="custCol-4"><span class="mobileCont">Alias</span>{{$simple_unit->decimal_value}}</li>
		<li class="custCol-2 btnHolder">
                <div class="actionBtnSet">
                    <a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/units/'.$status, ['id' => Crypt::encrypt($simple_unit->id)]) }}"><?php echo $status; ?></a>
		</div>
		</li>
<!--            <li class="custCol-2 btnHolder">
                <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
		<div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/units/edit', ['id' => Crypt::encrypt($simple_unit->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/units/'.$status, ['id' => Crypt::encrypt($simple_unit->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/units/delete', ['id' => Crypt::encrypt($simple_unit->id)]) }}">Delete</a>
		</div>
		</div>
            </li>-->
            </ul>           
            @endforeach

        </div>
    </div>
     <?php echo $simple_units->render(); ?> 
                </div>
                <div class="compoundunitsection">
                     <div class="listerType1"> 
        <div class="headingHolder deskCont">
            <ul class="custRow">
                <li class="custCol-1">No</li>
                <li class="custCol-4">From</li>
                <li class="custCol-4">To</li>
                <li class="custCol-2"></li>
            </ul>
        </div>
        <div class="listerV1">
<!--            
    <?php $n1 = 0; ?>  -->         
               @foreach ($units as $unit)
               @if($unit['status']==0)
               <?php $cstatus='Enable'; ?>
               @else
               <?php $cstatus='Disable'; ?>
               @endif
               <?php $n1++; ?>
            <ul class="custRow">
		<li class="custCol-1"><span class="mobileCont">No</span>{{ $n1 }}</li>
                 
		<li class="custCol-4"><span class="mobileCont">From</span>{{$unit['name']}}</li>
                  
		<li class="custCol-4"><span class="mobileCont">To</span>{{$unit['conversion_value']}} {{$unit['to_name']}}</li>
                    
                <li class="custCol-2 btnHolder">
                <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
		<div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/units/edit', ['id' => Crypt::encrypt($unit['id'])]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/units/'.$cstatus, ['id' => Crypt::encrypt($unit['id'])]) }}"><?php echo $cstatus; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/units/delete', ['id' => Crypt::encrypt($unit['id'])]) }}">Delete</a>
		</div>
		</div>
		</li>
            </ul>  
            @endforeach
        </div>
    </div>
                </div>
            </div>


        </div> 
</div>
 

@endsection
