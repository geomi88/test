@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>POS <span>Sales Report</span></h1>
    </header>	
    <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-12">
                     <a href="{{ url('branchsales/pos_sales/add') }}" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>
             <div class="customClear"></div>
    </div>
    <div class="listHolderType1">
        <div class="custRow">
            <div class="custCol-12">
                <h3>Branch Name</h3>
            </div>
        </div>
        <div class="listerType1"> 
            <div class="headingHolder deskCont">
                <ul class="custRow">
                    <li class="custCol-1">No</li>
                    <li class="custCol-4">Branch Name</li>
                    <li class="custCol-2">Supervisor Name</li>
                    <li class="custCol-2 ">Total Sales</li>
                </ul>
            </div>
            <div class="listerV1">
                <?php $n = $pos_sales->perPage() * ($pos_sales->currentPage()-1); ?>
                @foreach ($pos_sales as $pos_sale)
                 <?php $n++; ?>
                <ul class="custRow">
                    <li class="custCol-1"><span class="mobileCont">No</span>{{ $n }}</li>
                    <li class="custCol-4"><span class="mobileCont">Branch</span>{{$pos_sale->branch_name}}</li>
                     <li class="custCol-2"><span class="mobileCont">Supervisor Name</span>{{$pos_sale->employee_fname}} {{$pos_sale->employee_aname}}</li>
                    <li class="custCol-2"><span class="mobileCont">Total Sales</span>{{$pos_sale->total_sale}}</li>
                    <li class="custCol-2 btnHolder">
                        <div class="actionBtnSet">
                            <a class="btnAction action bgGreen" href="{{ URL::to('branchsales/pos_sales/show', ['id' => Crypt::encrypt($pos_sale->id)]) }}">View</a>
                        </div>
                    </li>
                </ul>
                @endforeach
            </div>	
        </div>
    </div>



    <?php echo $pos_sales->render(); ?> 
</div>
@endsection