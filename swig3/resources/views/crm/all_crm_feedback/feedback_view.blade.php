@extends('layouts.main')
@section('content')
<div class="divprint" style="display: none;">
    <table cellpadding="3" width="100%" height="100%" cellspacing="0" 
           style=" -webkit-appearance: none;-moz-appearance:none;appearance:none; margin-top: 35px; font-family:open_sansregular; font-size: 14px; border: 3px solid #760000; padding: 8px; height: auto">
        <tr>
            <td valign="top">
                <table>
                    <tr>
                        <td style="vertical-align: top; box-sizing:border-box; border:0">
                            <h2 style="color: #760000; font-size: 25px; text-align: center; font-weight: bold; display: block; margin: 9px; ">CRM Feedbacks and Followups</h2>
                        </td>
                        <td style="vertical-align: top; border:0; text-align: right; margin-top: -33px; margin-right: 14px; width: 4%;">
                            <img src="{{ URL::asset('images/imgImtiyazatLogo.png')}}" style="width: 105px">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="top">
                <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none; " >
                    <tr> 
                        <td colspan="5" style="background: #760000; font-size: 14px; font-weight: bold; color: #fff; text-align: left; margin-bottom: 0; padding: 4px 0px 4px 0px;" width="100%"> CRM Feedback Details 
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 0; vertical-align: top; width: 20%">
                            <p>
                                <span style="display:block; font-size: 16px; font-weight: bold">Customer Name</span>
                            <p style="font-size: 14px; color: #4d4d6f; ; margin-top: -10px;">{{ $feedback_data['0']->customer_name }}</p>
                            </p>
                        </td>
                        <td style="border: 0; vertical-align: top; width: 20%">
                            <p>
                                <span style="display:block; font-size: 16px; font-weight: bold">Branch</span>
                            <p style="font-size: 14px; color: #4d4d6f; ; margin-top: -10px;">{{ $feedback_data['0']->branch_name }}</p>
                            </p>
                        </td>
                        <td style="border: 0; vertical-align: top; width: 20%">
                            <p>
                                <span style="display:block; font-size: 16px; font-weight: bold">Created On</span>
                            <p style="font-size: 14px; color: #4d4d6f; ; margin-top: -10px;"><?php echo date("d-m-Y H:i", strtotime($feedback_data['0']->created_at)); ?></p>
                            </p>
                        </td>
                        <td style="border: 0; vertical-align: top; width: 20%">
                            <p>
                                <span style="display:block; font-size: 16px; font-weight: bold">Customer Phone</span>
                            <p style="font-size: 14px; color: #4d4d6f; ; margin-top: -10px;">{{ $feedback_data['0']->mobile_number }}</p>
                            </p>
                        </td>
                        <td style="border: 0; vertical-align: top; width: 20%">
                            <p>
                                <span style="display:block; font-size: 16px; font-weight: bold">Created By</span>
                            <p style="font-size: 14px; color: #4d4d6f; ; margin-top: -10px;">{{ $feedback_data['0']->created_by }}</p>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="border: 0; vertical-align: top; width: 100%">
                            <p>
                                <span style="display:block; font-size: 16px; font-weight: bold">Customer Comments</span>
                            <p style="font-size: 14px; color: #4d4d6f; ; margin-top: -10px;">{{ $feedback_data['0']->customer_comment }}</p>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none;   " >
                    <tr > 
                        <td colspan="5" style="background: #760000; font-size: 14px; font-weight: bold; color: #fff; text-align: left; margin-bottom: 0; padding: 4px 0px 4px 0px;"> Customer Followups
                        </td>
                    </tr>
                    @foreach($followups as $eachfollowup)
                    <tr>
                        <td style="border-bottom: 1px solid grey; border-top: 0; border-right: 0; border-left: 0; vertical-align: top; width:200px;">
                            <p>
                                <span style="display:block; font-size: 16px; font-weight: bold">{{$eachfollowup->username}}  {{$eachfollowup->first_name}}</span>
                            <p style="font-size: 14px; color: #4d4d6f; ; margin-top: -3px;"><?php echo date("d-m-Y H:i", strtotime($eachfollowup->created_at)); ?></p>
                            </p>
                        </td>
                        <td style="border-bottom: 1px solid grey; border-top: 0; border-right: 0; border-left: 0; vertical-align: top;">
                            <p>
                                <span style="display:block; font-size: 16px;">{{$eachfollowup->followup}}</span>
                            </p>
                        </td>
                    </tr>
                    @endforeach
                    @if($feedback_data['0']->is_closed == 2)
                    <tr style="background-color: #c5c5c5;">
                        <td style="border-bottom: 0 ; border-top: 1px solid grey; border-right: 0; border-left: 0; vertical-align: top; width:200px;">
                            <p>
                                <span style="display:block; font-size: 16px; font-weight: bold">{{$feedback_data['0']->closed_code}}  {{$feedback_data['0']->closed_name}}</span>
                            <p style="font-size: 14px; color: #4d4d6f; ; margin-top: -3px;"><?php echo date("d-m-Y H:i", strtotime($feedback_data['0']->closed_date)); ?></p>
                            </p>
                        </td>
                        <td style="border-bottom:0 ; border-top: 1px solid grey; border-right: 0; border-left: 0; vertical-align: top; font-size: 22px; font-weight: bold;" >

                            <p style="vertical-align: top; margin-top:11px;">Closed Complaint</p>

                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="innerContent">
    @if($page == 'all_crm_feedback')
    <a class="btnBack" href="{{ url('crm/all_crm_feedback')}}">Back</a>
    @else
    <a class="btnBack" href="{{ url('crm/crm_followups')}}">Back</a>
    @endif
    <div class="fieldGroup meetingHolder">
        <header class="pageTitle">
            <h1>CRM Feedback <span>Details</span></h1>
        </header>
        <a class="btnAction print bgGreen printBtn2" id="btnPrint" href="#">Print</a>
        <div class="customClear"> </div>
        <div class="viewMeetingWrapper">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Customer Name</label>
                        <p class="dataElements">{{ $feedback_data['0']->customer_name }}</p>
                    </div>

                    <div class="inputHolder">
                        <label>Customer Phone</label>
                        <p class="dataElements">{{ $feedback_data['0']->mobile_number }}</p>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Branch</label>
                        <p class="dataElements">{{ $feedback_data['0']->branch_name }}</p>
                    </div>
                    <div class="inputHolder">
                        <label>Created By</label>
                        <p class="dataElements">{{ $feedback_data['0']->created_by }}</p>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Created On</label>
                        <p class="dataElements"><?php echo date("d-m-Y H:i", strtotime($feedback_data['0']->created_at)); ?></p>
                    </div>
                    <div class="inputHolder">
                        <label>Customer Comments</label>
                        <p class="dataElements">{{ $feedback_data['0']->customer_comment }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="viewMeetingWrapper customerFollowHolder">
            <div class="custRow">
                <div class="custCol-12">
                    <div class="inputHolder subHeadingHolder">
                        <label>Customer Followups</label>
                    </div>
                </div>
            </div>
            @foreach($followups as $eachfollowup)
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>{{$eachfollowup->username}}  {{$eachfollowup->first_name}}</label>
                        <p class="dataElements"><?php echo date("d-m-Y H:i", strtotime($eachfollowup->created_at)); ?></p>
                    </div>
                </div>
                <div class="custCol-8">
                    <div class="inputHolder">
                        <p class="dataElements">{{$eachfollowup->followup}}</p>
                    </div>
                </div>
            </div>
            <hr class="borderLineHolder">
            @endforeach
            @if($feedback_data['0']->is_closed != 2)
            @if($page == 'all_crm_feedback')
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Follow Up</label>
                    </div>
                </div>
                <div class="custCol-8">
                    <form action="{{ url('crm/crm_feedback_followup/store') }}" method="post" id="frmaddfollowup">
                        <input type="hidden" name="feedback_id" id="feedback_id" value="{{$feedback_data['0']->id}}">
                        <div class="inputHolder">
                            <textarea name="follow_up" id="follow_up" placeholder="Enter your comments here..."></textarea>
                        </div>
                        <div class="custCol-4">
                            <div class="custCol-3">
                                <input type="submit" value="Create" class="commonBtn bgGreen addBtn">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="custCol-3">
                                <a onclick="return confirm('Are you sure?')" href="{{ URL::to('crm/crm_feedback_followup/close', ['id' => \Crypt::encrypt($feedback_data['0']->id)]) }}"><input type="button" value="Close Complaint" name="close_complaint" id="close_complaint" class="commonBtn bgRed addBtn"></a>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
            @endif
            @else
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>{{$feedback_data['0']->closed_code}}  {{$feedback_data['0']->closed_name}}</label>
                        <p class="dataElements"><?php echo date("d-m-Y H:i", strtotime($feedback_data['0']->closed_date)); ?></p>
                    </div>
                </div>
                <div class="custCol-8">
                    <div class="inputHolder">
                        <p class="dataElements">Closed Complaint</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    $("#frmaddfollowup").validate({
            errorElement: "span",
            errorClass: "commonError",
            ignore: [],
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                follow_up:
                        {
                            required: {
                                depends: function () {

                                    if ($.trim($(this).val()) == '') {
                                        $(this).val($.trim($(this).val()));
                                        return true;
                                    }
                                }
                            },
                        },
            },
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                follow_up: "Enter Your Comments",
            }
        });
        
        $(document).ready(function ()
        {
            $('#btnPrint').click(function () {
                win = window.open('', 'Print', 'width=720, height=1018');
                win.document.write($('.divprint').html());
                win.document.close();
                win.print();
                //win.close();
                return false;
            });

        });
        
</script>
@endsection