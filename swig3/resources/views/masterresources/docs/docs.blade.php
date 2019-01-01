@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1><span>Document</span> List</h1>
    </header>
    <div class="listerType1"> 
    <div class="headingHolder deskCont">
        <ul class="custRow">
            <li class="custCol-1">No</li>
            <li class="custCol-4">Document Name</li>
            <li class="custCol-4">Alias</li>
        </ul>
    </div>
      

    <div class="listerV1">
        <?php $n = $documents->perPage() * ($documents->currentPage()-1); ?>
        @foreach ($documents as $document)
        <?php $n++; ?>
            <ul class="custRow">
                <li class="custCol-1"><span class="mobileCont">No</span>{{ $n }}</li>
                <li class="custCol-4"><span class="mobileCont">Department Name</span>{{ $document->name }}</li>
                <li class="custCol-4"><span class="mobileCont">Alias</span>{{ $document->alias_name }}</li>
                <li class="custCol-2 btnHolder"><a class="btnAction edit bgRed documentfile" id="documentfile" href="{{ $document->file_url }}">View</a></li>
            </ul>
        @endforeach
            
     
    </div>
</div>
       <?php echo $documents->render(); ?>  
    <div class="commonModalHolder">
        <div class="modalContent">
            <a href="javascript:void(0)" class="btnModalClose">Close(X)</a>
            <iframe id="frame" style="width:100%;height:100%;"></iframe>
        </div>
    </div>
</div>
<script>
 
    
    $('.documentfile').on('click', function () {
        $('.commonModalHolder').show();
        var documentfile = $(this).attr('href');
        $('#frame').attr('src', documentfile)
        return false;
        
    })

    

    $('.btnModalClose').on('click', function () {
        $('.commonModalHolder').hide()

    })
</script>
@endsection
