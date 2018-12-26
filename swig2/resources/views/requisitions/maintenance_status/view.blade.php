@extends('layouts.main')
@section('content')
<?php
$requisitiondata = $pageData['requisitiondata'];
$action_takers = $pageData['action_takers'];
$next_action_takers_list = $pageData['next_action_takers_list'];
$center_type = $pageData['center_type'];
if ($center_type == "BRANCH") {
    $text = "Branch";
}
if ($center_type == "OFFICE") {
    $text = "Office";
}
if ($center_type == "WAREHOUSE") {
    $text = "Warehouse";
}
if ($center_type == "STAFF_HOUSE") {
    $text = "Staff House";
}
?>

<div class="innerContent">
   
    <header class="pageTitleV3">
        <h1>Maintenance Requisition Status</h1>
    </header>

    <div class=" inputAreaWrapper">
        <div class="blockWrapper">

            <div class="custRow reqCodeDateHolder">
                <div class="custCol-6">
                    <label>Requisition Date : <span><?php echo date('d-m-Y', strtotime($requisitiondata->created_at)); ?></span></label>

                </div>
                <div class="custCol-6 alignRight">
                    <label>Requisition Code : <span>{{$requisitiondata->requisition_code}}</span></label>  
                </div>
            </div>
            <div class="custRow ">
                <div class="custCol-4">
                    <div class="inputView">
                        <span>Requisition Title :</span><strong>{{$requisitiondata->title}}</strong>
                    </div>
                    <div class="inputView">
                        <span>{{$text}} :</span><strong>{{$requisitiondata->name}} </strong>
                    </div>
                    <div class="inputView">
                        <span>Amount :</span><strong>{{$requisitiondata->total_price}}</strong>
                    </div>
                    <div class="inputView">
                        <span>Description :</span><strong>{{$requisitiondata->description}}</strong>
                    </div>

                </div>
                <div class="custRow">
                    <div class="custCol-6">
                        <label>Created By : <span>{{$requisitiondata->createdby}}</span></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="blockWrapper">
            <h1 style="padding-bottom: 13px;font-size: 20px;">Work Completion Details</h1>
            <div class="custCol-2">
                <div class="inputHolder ">
                    <label ><span>Completed date : </span><strong><?php if($requisitiondata->completed_date) echo date("d-m-Y", strtotime($requisitiondata->completed_date)); ?></strong></label>
                </div>
            </div>
            <form action="{{ action('Requisitions\MaintenanceinpendingController@update') }}" id="frmupdatereq" method="post" enctype="multipart/form-data">
                <div class="custRow clscheckinfo" >
                    <div class="custCol-4 ">
                        <div class="inputHolder  ">
                            <label>Work Duration</label>
                            <input type="text" name="workduration" id="workduration" value="{{$requisitiondata->workduration}}" disabled  placeholder="Enter Duration" autocomplete="off" maxlength="100" >
                            <input type="hidden"  name="req_id" id="req_id" value="{{$requisitiondata->req_id}}">
                            <span class="commonError"></span>

                        </div>
                    </div>

                    <div class="custCol-4 ">
                        <div class="inputHolder  ">
                            <label>No Of Employees</label>
                            <input type="text" name="emgagedemps" id="emgagedemps" value="{{$requisitiondata->no_of_emp_engaged}}" disabled placeholder="Enter No Of Employees" autocomplete="off" maxlength="100" >
                            <span class="commonError"></span>

                        </div>
                    </div>

                    <div class="custCol-4 ">
                        <div class="inputHolder  ">
                            <label>Work Expenditure</label>
                            <input type="text" name="expenditure" id="expenditure" value="{{$requisitiondata->expenditure}}" disabled placeholder="Enter Expenditure" autocomplete="off" maxlength="100" >
                            <span class="commonError"></span>
                        </div>
                    </div>

                </div>

                <div class="custRow">
                    <div class="custCol-6">
                        <div class="inputHolder">
                            <label>Description</label>
                            <textarea id="description" name="description" placeholder="Enter Description" disabled >{{$requisitiondata->maintenance_desc}}</textarea>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    
                    <?php  if($requisitiondata->document_url){?>
                    <div class="custCol-4 ">
                        <div class="inputHolder ">
                            <label>Uploaded Document</label>
                            <span class="commonError"></span>
                            <a class="btnAction edit bgBlue viewreqdocument" href="{{$requisitiondata->document_url}}">View</a>
                        </div>
                    </div>
                    <?php } ?>
                    
                </div>

            </form>
        </div>

        <div class="approverDetailsWrapper">
            <div class="tbleListWrapper">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="headingHolder ">
                        <tr>
                            <th>Action Taker</th>
                            <th>Date</th>
                            <th class="tbleComments">Comments</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($action_takers as $actor)
                        <?php
                        if ($actor->action == "Rejected") {
                            $class = "bgRed";
                        } else {
                            $class = "bgGreen";
                        }
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td>{{$actor->action_taker}}</td>
                            <td><?php echo date('d-m-Y', strtotime($actor->created_at)); ?></td>
                            <td>{{$actor->comments}}</td>
                            <td>{{$actor->action}}</td>

                        </tr>
                        @endforeach

                        @foreach ($next_action_takers_list as $actor)
                        <tr class="bgOrange">
                            <td>{{$actor['name']}}</td>
                            <td></td>
                            <td></td>
                            <td>{{$actor['action']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        <?php if (count($documents) > 0) { ?>

            <div class="documentWrapper">
                <div class="tbleListWrapper">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="headingHolder ">
                            <tr>
                                <th>Date</th>
                                <th>Uploaded By</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documents as $document)
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($document->created_at)); ?></td>
                                <td>{{$document->createdby}}</td>
                                <td >
                                    <a class="viewreqdocument btnViewModal bgDarkGreen" href="{{$document->doc_url}}">View</a>
                                </td>

                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div> 
        <?php } ?>
    </div>

    <div class="commonModalHolder">
        <div class="modalContent printModal">
            <a href="javascript:void(0);" class="btnAction print bgGreen btnImgPrint" style="display: none;">Print</a>
            <a href="javascript:void(0);" class="btnModalClose">Close(X)</a>
            <div id="printHolder">
                <iframe id="frame" style="width:100%;height:100%;"></iframe>
            </div>
        </div>
    </div>    
</div>
<script>
    $(function () {

        $('.btnModalClose').on('click', function () {
            $('.commonModalHolder').hide()
        });

        $('body').on('click', '.btnImgPrint', function () {
            win = window.open('', 'Print', 'width=720, height=1018');
            win.document.write($('#printHolder').html());
            win.document.close();
            win.print();
            win.close();
        });

    });
 
</script>
@endsection