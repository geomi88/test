@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>POS <span>Reasons</span></h1>
    </header>
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
            <div class="custCol-12">
                <a href="{{ action('Masterresources\PosreasonController@add') }}" id="btnNew" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
            </div>
        </div>
        <div class="customClear"></div>
    </div>
    <div class="listerType1"> 
        <div class="headingHolder deskCont">
            <ul class="custRow">
                <li class="custCol-1">No</li>
                <li class="custCol-4">Name</li>
                <li class="custCol-4">Alias</li>
                <li class="custCol-2"></li>
            </ul>
        </div>
        <div class="listerV1">

            <?php $n = $poss->perPage() * ($poss->currentPage() - 1); ?>           
            @foreach ($poss as $pos)
            @if($pos->status==0)
            <?php $status = 'Enable'; ?>
            @else
            <?php $status = 'Disable'; ?>
            @endif
            <?php $n++; ?>
            <ul class="custRow">
                <li class="custCol-1"><span class="mobileCont">No</span>{{ $n }}</li>
                <li class="custCol-4"><span class="mobileCont">Name</span>{{$pos->name}}</li>
                <li class="custCol-4"><span class="mobileCont">Alias</span>{{$pos->alias_name}}</li>
                <li class="custCol-2 btnHolder">
                    <div class="actionBtnSet">
                        <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
                        <div class="actionBtnHolderV1">
                            <a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/pos_reasons/edit', ['id' => Crypt::encrypt($pos->id)]) }}">Edit</a>
                            <a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/pos_reasons/'.$status, ['id' => Crypt::encrypt($pos->id)]) }}"><?php echo $status; ?></a>
                            <a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/pos_reasons/delete', ['id' => Crypt::encrypt($pos->id)]) }}">Delete</a>
                        </div>
                    </div>
                </li>
            </ul>           
            @endforeach
        </div>
    </div>
    <?php echo $poss->render(); ?> 
</div>


@endsection
