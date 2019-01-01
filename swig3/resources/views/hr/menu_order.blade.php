@extends('layouts.main')

@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Menu <span>Order</span></h1>
    </header>

    <div class="formListV1">
        <div class="clsMenuOrder" id="" style="">
                @foreach ($modules as $module)
                <div class="privilegeHolder modules <?php if($module->name=="Calendar"){echo "nodragorsort";}?>" id="{{$module->id}}">
                    <div class="privilegeTop">
                        <div class="commonCheckHolder">
                            <label>
                                <em>{{$module->name}}</em>
                            </label>
                        </div>
                    </div>
                    
                </div>
                @endforeach
        </div>
    </div>
    <div class="commonLoaderV1"></div>
     
</div>

<script>
    $(document).ready(function () {
        $(".commonLoaderV1").hide();
        $('.clsMenuOrder').sortable({
            connectWith: '.clsMenuOrder',
            items: '>:not(.nodragorsort)',
            update: function( event, ui ) {
                change_priority();
            }
        });

        function change_priority(){
            var arrNewTaskPy=[];
            $(".modules").each(function(){
                var index = $(this).index();
                var moduleid = $(this).attr("id");

                 var arraData = {
                    moduleindex: index+50,
                    moduleid: moduleid,
                }

                arrNewTaskPy.push(arraData);
            });

           var arrModulePys = encodeURIComponent(JSON.stringify(arrNewTaskPy));
           $(".commonLoaderV1").show();
            $.ajax({
               type: 'POST',
               url: 'change_priority',
               data: '&arrModulePys=' + arrModulePys,
                success: function (return_data) {
                    $(".commonLoaderV1").hide();
                    window.location.href = '{{url("hr/menu_order")}}';
                }
            });
        }
    });
</script>
@endsection