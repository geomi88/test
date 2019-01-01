<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>Moroccan Taste</title>
        <link rel="icon" href="{{ URL::asset('favicon.png') }}" type="image/png" />
        <!--[if lt IE 9]><script src="js/IEupdate.js" type="text/javascript"></script><![endif]-->
        <script src="{{ URL::asset('js/jquery-2.1.1.min.js') }}" type="text/javascript"></script>

        <script src="{{ URL::asset('js/dev_scripts.js') }}" type="text/javascript"></script> 
        <script src="{{ URL::asset('js/functions.js') }}" type="text/javascript"></script> 
        <script src="{{ URL::asset('js/tax_function.js') }}" type="text/javascript"></script> 
        <link type="text/css" href="{{ URL::asset('css/styles.css') }}" rel="stylesheet" media="all" />
        <script src="{{ URL::asset('js/jquery-min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/toastr.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/jquery.sortable.min.js') }}" type="text/javascript"></script> 

        <script src="{{ URL::asset('js/ui.js') }}" type="text/javascript"></script> 
        <link type="text/css" href="{{ URL::asset('css/toastr.css') }}" rel="stylesheet" media="all" />
        <link type="text/css" href="{{ URL::asset('css/datepicker.css') }}" rel="stylesheet" media="all" />
        <script src="{{ URL::asset('js/jquery.validate.min.js') }}" type="text/javascript"></script> 
        <script src="{{ URL::asset('js/jquery.datepicker.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/additional-methods.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/custom_scripts.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/highcharts.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/jquery.ui.touch-punch.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('../vendor/unisharp/laravel-ckeditor/ckeditor.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('../vendor/unisharp/laravel-ckeditor/adapters/jquery.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/jquery.flexslider.js')}}" type="text/javascript"></script>
        
        <link href="{{ URL::asset('css/chart.css') }}" media="all" rel="stylesheet" type="text/css" />
        <script src="{{ URL::asset('js/chart.js')}}" type="text/javascript"></script>
        
        <link href="{{ URL::asset('css/chosen.css') }}" media="all" rel="stylesheet" type="text/css" />
        <script src="{{ URL::asset('js/chosen.jquery.min.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/html2canvas.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('js/canvas2image.js')}}" type="text/javascript"></script>
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrapcdn.css')}}">
        <link type="text/css" href="{{ URL::asset('css/keyboard.css') }}" rel="stylesheet" media="all" />
        <script src="{{ URL::asset('js/bootstrapcdn.js') }}"></script>
        <script src="{{ URL::asset('js/keyboard.js')}}" type="text/javascript"></script>
 <!--<script src="{{ URL::asset('js/exporting.js') }}" type="text/javascript"></script>-->
        <script>
        window.setInterval(function () {

        notify();
                notification();
        }, 70000);
        function notify()
        {
        $.ajax({
        type: 'POST',
                url:{!! json_encode(url('/fetch_notification_count')) !!},
                beforeSend: function () {
                },
                success: function (return_data) {
                $('#notify_count').html(return_data);
                }
        });
        }

function notification()
        {
        $.ajax({
        type: 'GET',
                url:{!! json_encode(url('/fetch_notification')) !!},
                dataType: "html",
                beforeSend: function () {
                },
                success: function (return_data) {
                if (return_data)
                {
                $('.notificationListing').html(return_data);
                }
                else
                {
                $('.notificationListing').html('No Notifications At Present');
                }
                }
        });
                }
function start()
        {
        notify();
                notification();
                }
$(document).on("click", ".not_id", function() {
var id = this.id;
        $.ajax({
        type: 'POST',
                url:{!! json_encode(url('/mark_notification')) !!},
                data: {id: id},
                beforeSend: function () {
                },
                success: function (return_data) {

                }
        });
        });</script>
        
      
<script type="text/javascript">
function googleTranslateElementInit() {
new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'ar,en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>


    </head>
    <body id="body"  onload="start()">
        <div class="overlay"></div>
        <?php
        $employee_details = \App\Models\Employee::where('id', Session::get('login_id'))->first();
        $user_modules = \App\Models\Usermodule::where('employee_id', Session::get('login_id'))->join('modules', 'modules.id', '=', 'user_modules.module_id')->where('modules.parent_id', '=', 0)->get();
        $user_modules = \App\Models\Usermodule::where('employee_id', Session::get('login_id'))->join('modules', 'modules.id', '=', 'user_modules.module_id')->where('modules.parent_id', '=', 0)->orderby('menu_order','ASC')->get();
        ?>
        <img class="imgPopup" src="{{ URL::asset('images/dashboard_Map.jpg') }}">
        <section id="container">

            <div class="contentLeft mainMenu">
                <figure class="logoMoraccoTaste"><a href="javascript:void(0)"><img src="{{ URL::asset('images/logoMoroccanTaste.png') }}" alt="Moracco Taste"></a> </figure>
                <ul>
                        <li><a href="{{ url('dashboard') }}" id="dashboard"><figure><img src="{{ URL::asset('images/iconDashboard.png') }}" alt="My Calandar"><img src="{{ URL::asset('images/iconThingActive.png') }}" alt=""></figure>My Calendar</a></li>
                    <?php
                    if ($employee_details->admin_status == 1) {
                        ?>
                        <li><a href="{{ url('meeting') }}"><figure><img src="{{ URL::asset('images/iconMeeting.png') }}" alt="Meeting"></figure>Meeting</a></li>
                        <li><a href="{{ url('training') }}"><figure><img src="{{ URL::asset('images/iconHr.png') }}" alt="Training"></figure>Training</a></li>
                        <li><a href="{{ url('reception') }}"><figure><img src="{{ URL::asset('images/iconSupervisor.png') }}" alt="Reception"></figure>Reception</a></li>
                        <li><a href="{{ url('ledgers') }}"><figure><img src="{{ URL::asset('images/iconRequisition.png') }}" alt="Ledgers"></figure>Ledgers</a></li>
                        <li><a href="{{ url('organizationchart') }}"><figure><img src="{{ URL::asset('images/iconNavInventory.png') }}" alt="ISO Rules"></figure>ISO Rules</a></li>
                        <li><a href="{{ url('mis') }}" id="mis"><figure><img src="{{ URL::asset('images/iconMis.png') }}" alt="MIS"><img src="{{ URL::asset('images/iconMisActive.png') }}" alt=""></figure>MIS</a></li>
                        <li><a href="{{ url('hr') }}" id="hr"><figure><img src="{{ URL::asset('images/iconHr.png') }}" alt="Moracco Taste"><img src="{{ URL::asset('images/iconHrActive.png') }}" alt=""></figure>HR</a></li>
                        <li><a href="{{ url('taxation') }}" id="hr"><figure><img src="{{ URL::asset('images/iconMis.png') }}" alt="Moracco Taste"><img src="{{ URL::asset('images/iconMisActive.png') }}" alt=""></figure>Taxation</a></li>
                        
                        <li><a href="{{ url('finance') }}" id="hr"><figure><img src="{{ URL::asset('images/iconFinance.png') }}" alt="Moracco Taste"><img src="{{ URL::asset('images/iconFinanceActive.png') }}" alt=""></figure>Finance</a></li>
                        <li><a href="{{url('costcenter')}}"><figure><img src="{{ URL::asset('images/iconPayments.png') }}" alt=""></figure>Cost Center</a></li>
                        <li><a href="{{ url('branchsales') }}" id="branchsales"><figure><img src="{{ URL::asset('images/iconBranchSale.png') }}" alt=""><img src="{{ URL::asset('images/iconBranchSaleActive.png') }}" alt=""></figure>Branch Sales</a></li>
                        <li><a href="{{ url('checklist') }}"><figure><img src="{{ URL::asset('images/iconChecklist.png') }}" alt=""></figure>Check List</a></li>
                        <li><a href="{{ url('kpi') }}" id="kpi"><figure><img src="{{ URL::asset('images/iconRequisition.png') }}" alt=""><img src="{{ URL::asset('images/iconRequisitionActive.png') }}" alt=""></figure>KPI Analysis</a></li>
                        <li><a href="{{ url('operation') }}" id="operation"><figure><img src="{{ URL::asset('images/iconOperation.png') }}" alt=""></figure>Operation</a></li>
                        <li><a href="{{ url('rfq') }}" id="operation"><figure><img src="{{ URL::asset('images/iconChecklist.png') }}" alt=""></figure>RFQ</a></li>
                        <li><a href="{{ url('requisitions') }}" id="requisitions"><figure><img src="{{ URL::asset('images/iconRequisition.png') }}" alt=""><img src="{{ URL::asset('images/iconRequisitionActive.png') }}" alt=""></figure>Requisitions</a></li>
                        <li><a href="{{ url('inventory') }}" id="inventory"><figure><img src="{{ URL::asset('images/iconNavInventory.png') }}" alt=""><img src="{{ URL::asset('images/iconActiveNavInventory.png') }}" alt=""></figure>Inventory</a></li>
                        <li><a href="{{ url('purchase') }}"><figure><img src="{{ URL::asset('images/iconPurchase.png') }}" alt=""></figure>Purchase</a></li>
                        <li><a href="{{ url('branch') }}"><figure><img src="{{ URL::asset('images/iconBranch.png') }}" alt=""></figure>Branches</a></li>
                        <li><a href="{{ url('supervisors') }}" id="supervisors"><figure><img src="{{ URL::asset('images/iconSupervisor.png') }}" alt=""></figure>Supervisors</a></li>
                        <li><a href="{{ url('warehouse') }}"><figure><img src="{{ URL::asset('images/iconWireHouse.png') }}" alt=""></figure>Warehouse</a></li>
                        <li><a href="{{ action('Masterresources\ResourcesController@index') }}" id="resourceallocation"><figure><img src="{{ URL::asset('images/iconMasterresource.png') }}" alt=""><img src="{{ URL::asset('images/iconMasterresourceActive.png') }}" alt=""></figure>Master Resources</a></li>                    
                        <li><a href="{{ url('elegantclub') }}" id=""><figure><img src="{{ URL::asset('images/iconMasterresource.png') }}" alt=""><img src="{{ URL::asset('images/iconMasterresourceActive.png') }}" alt=""></figure>Elegant Club</a></li>                    
                        <li><a href="{{ url('crm') }}" id=""><figure><img src="{{ URL::asset('images/iconOperation.png') }}" alt=""><img src="{{ URL::asset('images/iconOperation.png') }}" alt=""></figure>CRM</a></li>                    
                        
                        <li><a href="javascript:void(0)"><figure><img src="{{ URL::asset('images/iconAreaManager.png') }}" alt=""></figure>Area Managers</a></li>
                        <li><a href="javascript:void(0)"><figure><img src="{{ URL::asset('images/iconMaintenance.png') }}" alt=""></figure>Maintenance</a></li>
                        <li><a href="javascript:void(0)"><figure><img src="{{ URL::asset('images/iconPayments.png') }}" alt=""></figure>Payments</a></li>
                        <li><a href="javascript:void(0)"><figure><img src="{{ URL::asset('images/iconBank.png') }}" alt=""></figure>Bank</a></li>
                        <li><a href="javascript:void(0)"><figure><img src="{{ URL::asset('images/iconRent.png') }}" alt=""></figure>Rent</a></li>
                        <li><a href="javascript:void(0)"><figure><img src="{{ URL::asset('images/iconVehicle.png') }}" alt=""></figure>Vehicles</a></li>
                        <li><a href="javascript:void(0)"><figure><img src="{{ URL::asset('images/iconFranchise.png') }}" alt=""></figure>Franchise</a></li>
                        <li><a href="javascript:void(0)"><figure><img src="{{ URL::asset('images/iconCall.png') }}" alt=""></figure>Calls/CRM</a></li>
                        <li><a href="javascript:void(0)"><figure><img src="{{ URL::asset('images/iconProduction.png') }}" alt=""></figure>Production/CRM</a></li>
                        <li><a href="javascript:void(0)"><figure><img src="{{ URL::asset('images/iconThing.png') }}" alt=""></figure>Thing to do</a></li>
                        
                    <?php } else { 
                        foreach ($user_modules as $user_module) {
                            $name = str_replace(' ','_',$user_module->name);
                            if($name!="Calendar"){
                    ?> 
                        <li><a href="{{ url($user_module->url) }}" id="{{$name}}"><figure><img src="{{ URL::asset('images/'.$user_module->logo) }}" alt="DashBoard"><img src="{{ URL::asset('images/'.$user_module->active_logo) }}" alt=""></figure>{{$user_module->name}}</a></li>
                    <?php }}}?>

                </ul>
            </div>
            <div class="contentRight">
                <header class="headerSection">
  <div id="google_translate_element"></div>
                    <div class="mobNav">
                        <div class="iconMobNav">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <ul>
                        <li class="notification">
                            <a href="javascript:void(0)"></a>
                            <em id="notify_count"></em>
                            <div class="notificationHolder">
                                <div class="suggestion">Keywords <span>X</span></div>
                                <input type="text" placeholder="Enter Keywords">
                                <div class="notificationListing">


                                </div>
                                <!--<a href="" class="btnViewMore">View More</a>-->
                            </div>
                        </li>
                        <li class="welcome">
                           
                            <a href="{{ url('employee/profile') }}"> <figure><img src="{{$employee_details->profilepic}}" alt="Moracco Taste"></figure>     <span><?php echo "Welcome ".$employee_details->first_name." ".$employee_details->alias_name;?></span></a>

                        </li>
                        <li class="logOut"><a href="{{ url('employee/logout') }}">Logout</a></li>
                    </ul>
                </header>
                <div class="customClear"></div>                
                <div class="contentArea">
                    {!! Toastr::render() !!}
                    @yield('content')
                    <div class="customClear"></div>
                    </section>
                    </body>
                    </html>
