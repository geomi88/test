@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Branch Attendees <span>Report</span></h1>
    </header>	
    <?php if($login_type == "CASHIER") { ?>
    <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-12">
                     <a href="{{ action('Branchsales\BranchattendeesController@add') }}" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>
            
    </div>
    <div class="customClear"></div>
    <?php } ?>
        <div class="listerType1"> 
            <div class="headingHolder deskCont">
                <ul class="custRow">
                    <li class="custCol-1">No</li>
                    <li class="custCol-2">Branch Name</li>
                    <li class="custCol-2">Employee</li>
                    <li class="custCol-2">Total Time (HH:MM)</li>
                    <li class="custCol-3"></li>
                </ul>
            </div>
            <div class="listerV1">
                <?php $n = $branch_attendees->perPage() * ($branch_attendees->currentPage()-1); ?>
                @foreach ($branch_attendees as $branch_attendee)
                 <?php $n++; ?>
                <ul class="custRow">
                    <li class="custCol-1"><span class="mobileCont">No</span>{{ $n }}</li>
                    <li class="custCol-2"><span class="mobileCont">Branch</span>{{$branch_attendee->branch_name}}</li>
                    <li class="custCol-2"><span class="mobileCont">Employee</span>{{$branch_attendee->employee_fname}} {{$branch_attendee->employee_aname}}</li>
                    <li class="custCol-2"><span class="mobileCont">Total Time</span>{{$branch_attendee->total_time}}</li>
                    <li class="custCol-3 btnHolder">
                        <div class="actionBtnSet">
                            <a class="btnAction action bgGreen" href="{{ URL::to('branchsales/branch_attendees/show', ['id' => Crypt::encrypt($branch_attendee->id)]) }}">View</a>
                        </div>
                    </li>
                </ul>
                @endforeach
            </div>	
        
    </div>



    <?php echo $branch_attendees->render(); ?> 
</div>
@endsection