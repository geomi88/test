<input type="hidden" id="currentPage" value="{{ $paginate_list->currentPage() }}">
<?php if ($paginate_list->lastPage() > 0) { ?>
    {!! $paginate_list->render() !!}  
<?php } ?>