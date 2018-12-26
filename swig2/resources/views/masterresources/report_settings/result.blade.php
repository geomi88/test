<?php  $n = $report_settings->perPage() * ($report_settings->currentPage() - 1); ?> 
    @foreach ($report_settings as $report_setting)
    
    <?php  $n++; 
    if($report_setting->type=="Daily"){
        $strDay="All";
    }else if($report_setting->type=="Weekly"){
        $arrDaystring = array(1=>'Sunday', 2=>'Monday', 3=>'Tuesday', 4=>'Wednesday', 5=>'Thursday', 6=>'Friday', 7=>'Saturday');
        $strDay=$arrDaystring[$report_setting->day];
    }else{
        $strDay="Month End";
    }
    ?>
    <tr>
        <td>{{ $n }}</td>
        <td><?php echo str_replace("_", " ", $report_setting->report_name);?></td>
        <td>{{$report_setting->type}}</td>
        <td>{{$strDay}}</td>
        <td>{{$report_setting->time}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/report_settings/edit', ['id' => Crypt::encrypt($report_setting->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/report_settings/delete', ['id' => Crypt::encrypt($report_setting->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if(count($report_settings) > 0){ ?>
        <tr class="paginationHolder"><th><div>   {!! $report_settings->render() !!}</div> </th></tr>
    <?php } ?>
    
    