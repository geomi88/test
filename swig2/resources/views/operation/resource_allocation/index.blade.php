@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Resource <span>Allocation</span></h1>
    </header>	
    <script>

        $(document).ready(function () {

            $('.dropme').sortable({
                connectWith: '.dropme',
                start: function (event, ui) {
                    $(this).addClass('dragged');
                },
                stop: function () {
                    $(this).removeClass('dragged');
                }
            })
            $(".modalHolderV1").css("display", "none");
            $('.dates_div').hide();
            $('.job_positions').hide();
            $('.shift').hide();
            $(".allocateBottom").hide();
            $(".branchdrop").hide();
            $('.resourcetype').on('click', function () {
                var rtype = this.id;
                $('.resourcetype').removeClass('selected');
                $('.resource_category').val(rtype);
                $(".allocateBottom").hide();
                $(this).addClass('selected');
                $('#from_date').val('');
                $('#to_date').val('');
                $('.shift').hide();
                showbranch(rtype);
            });
            $('.branchdrop').change(function () {
                var branch_id = $('.resourcedrop').val();
                var resource_category = $('.resource_category').val();
                if (branch_id == -1)
                {
                    var str = resource_category;
                    str = str.toLowerCase().replace(/\b[a-z]/g, function (letter) {
                        return letter.toUpperCase();
                    });
                    $(".allocateBottom").hide();
                    toastr.error('Please Select  ' + str);

                }
                else
                {
                    $('#branchsupervisor').html('');

                    if (resource_category == 'region')
                    {

                        $("#regiondiv").show();
                        show_regional_managers();
                        show_allocated_regional_managers();
                    }
                    if (resource_category == 'area')
                    {
                        $("#areadiv").show();
                        show_area_managers();
                        show_allocated_area_managers();
                    }
                    /*if (resource_category == 'branch')
                    {
                        $("#branchdiv").show();
                        show_allocated_supervisors();
                        show_supervisors();
                        show_cashiers();
                        show_baristas();
                        show_shifts();
                        $('.tabContents').hide();

                    }*/
                }
            });
            
            $('#job_position').change(function () {
                
                var job_position = $('#job_position').val();
                if(job_position == 'Supervisor')
                {
                    $('.dates_div').show();
                    $('.shift').hide();
                    $("#branch_cashier_div").hide();
                    $("#branch_barista_div").hide();
                    $("#branchdiv").show();
                    show_allocated_supervisors();
                    show_supervisors();
                }
                if(job_position == 'Barista')
                {
                    $('.dates_div').show();
                    $('.shift').show();
                    $("#branchdiv").hide();
                    $("#branch_cashier_div").hide();
//                    $("#branch_barista_div").show();
                    
                    show_shifts();
                    show_baristas();
                    show_allocated_baristas();
                }
                if(job_position == 'Cashier')
                {
                    $('.dates_div').show();
                    $('.shift').show();
                    $("#branchdiv").hide();
                    $("#branch_barista_div").hide();
//                    $("#branch_cashier_div").show();
                    
                    show_shifts();
                    show_cashiers();
                    show_allocated_cashiers();
                    
                }
                if(job_position == '-1'){
                    $("#branchdiv").hide();
                    $("#branch_barista_div").hide();
                    $("#branch_cashier_div").hide();
                }
                
            });
            $(document).on('change', '#shiftid', function() { 

                var job_position = $('#job_position').val();
                if(job_position == 'Supervisor')
                {
                    $('.dates_div').show();
                    $('.shift').hide();
                    $("#branch_barista_div").hide();
                    $("#branch_cashier_div").hide();
                    $("#branchdiv").show();
                    show_allocated_supervisors();
                    show_supervisors();
                }
                if(job_position == 'Barista')
                {
                    $('.dates_div').show();
                    $('.shift').show();
                    $("#branchdiv").hide();
                    $("#branch_cashier_div").hide();
                    $("#branch_barista_div").show();
                    
                    show_baristas();
                    show_allocated_baristas();
                }
                if(job_position == 'Cashier')
                {
                    $('.dates_div').show();
                    $('.shift').show();
                    $("#branchdiv").hide();
                    $("#branch_barista_div").hide();
                    $("#branch_cashier_div").show();
                    
                    show_cashiers();
                    show_allocated_cashiers();
                }
            });
            

        });
///////////////////////////////// branch dropdown///////////////////////
        function showbranch(rtype)
        {
            //var branch_id = $('.resourcedrop').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/showbranch',
                data: {rtype: rtype},
                beforeSend: function () {
                    // $(".commonLoaderV1").show();
                },
                success: function (return_data) {
                    if (return_data != '')
                    {
                        // alert(return_data);
                        $('.branchdrop').html(return_data);
                        if(rtype=='branch')
                        {
                           $('.job_positions').show();
                           $('.dates_div').hide();
                        }
                        else
                        {
                           $('.job_positions').hide(); 
                           $('.dates_div').show();
                        }
                        $(".branchdrop").show();
                    }
                    else
                    {
                        // $(".commonLoaderV1").hide();
                        $('.branchdrop').html('');
                    }
                }
            });



        }
////////////////////////////dispaly all cashiers/////////////////////
        function show_shifts()
        {
            var branch_id = $('.resourcedrop').val();
            if(branch_id > 0){
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/show_shifts',
                    async:false,
                    data: {branch_id: branch_id},
                    beforeSend: function () {
                    },
                    success: function (return_data) {
                        if (return_data != '')
                        {
                            $('.shift').html(return_data);
                        }
                        else
                        {
                            $('.shift').html('');
                        }
                    }
                });
            }else{
                $('.shift').html('');
            }
        }

        function show_cashiers()
        {
            var shift_id = $('#shiftid').val();
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if(shift_id > 0 && branch_id > 0 && from_date!='' && to_date!='')
            {
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_cashiers',
                data: {branch_id: branch_id, shift_id: shift_id},
                beforeSend: function () {
                },
                success: function (return_data) {
                    if (return_data != '')
                    {
                        // alert(return_data);
                        $('.allcashiers').html(return_data);
                        //$(return_data).draggable().prependTo('.details');
                        //$(".commonLoaderV1").hide();
                    }
                    else
                    {
                        $('.allcashiers').html('No Cashier Is Present');
                    }
                }
            });
                $("#branch_cashier_div").show();
            } else{
                $("#branch_cashier_div").hide();
            }



        }

        function show_baristas()
        {
            var shift_id = $('#shiftid').val();
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if(shift_id > 0 && branch_id > 0 && from_date!='' && to_date!='')
            {
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_baristas',
                data: {branch_id: branch_id, shift_id: shift_id},
                beforeSend: function () {
                },
                success: function (return_data) {
                    if (return_data != '')
                    {
                        // alert(return_data);
                        //$('.allbaristas').html(return_data);
                        //$('.allbaristas2').html(return_data);
                        //$(return_data).draggable().prependTo('.details');
                        //$(".commonLoaderV1").hide();
                        $('.allbaristas').html(return_data);
                    }
                    else
                    {
                        //$('.allbaristas').html('No Cashier Is Present');
                        //$('.allbaristas2').html('No Barista Is Present');
                        $('.allbaristas').html('No Barista Is Present');
                    }
                }
            });
            $("#branch_barista_div").show();
            }else{
                $("#branch_barista_div").hide();
            }


        }
//////////////////////// dispaly all supervisors/////////////////
        function show_supervisors()
        {
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (branch_id !== '' && from_date !== '' && to_date !== '')
            {
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_supervisors',
                data: {branch_id: branch_id, from_date: from_date, to_date: to_date},
                beforeSend: function () {
                },
                success: function (return_data) {
                    if (return_data != '')
                    {
                        // alert(return_data);
                        $('.allemps').html(return_data);

                        //$(return_data).draggable().prependTo('.details');
                        //$(".commonLoaderV1").hide();
                    }
                    else
                    {
                        $('.allemps').html('No Supervisor Present');
                    }
                }
            });
            }



        }

        function show_area_managers()
        {
            var area_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (area_id !== '' && from_date !== '' && to_date !== '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/show_area_managers',
                    data: {area_id: area_id, from_date: from_date, to_date: to_date},
                    beforeSend: function () {
                    },
                    success: function (return_data) {
                        if (return_data !== '')
                        {
                            // alert(return_data);
                            $('.area_allemps').html(return_data);

                            //$(return_data).draggable().prependTo('.details');
                            //$(".commonLoaderV1").hide();
                        }
                        else
                        {
                            $('.area_allemps').html('No Area Manager Present');
                        }
                    }
                });

            }

        }

        function show_regional_managers()
        {
            var region_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (region_id !== '' && from_date !== '' && to_date !== '')
            {
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_regional_managers',
                data: {region_id: region_id, from_date: from_date, to_date: to_date},
                beforeSend: function () {
                },
                success: function (return_data) {
                    if (return_data != '')
                    {
                        // alert(return_data);
                        $('.regionalmanager_allemps').html(return_data);

                        //$(return_data).draggable().prependTo('.details');
                        //$(".commonLoaderV1").hide();
                    }
                    else
                    {
                        $('.regionalmanager_allemps').html('No Regional Manager Present');
                    }
                }
            });
        }


        }
////////////////// display allocated supervisors for specific branch/////////////////
        function show_allocated_supervisors()
        {
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (branch_id !== '' && from_date !== '' && to_date !== '')
            {
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_allocated_supervisors',
                data: {branch_id: branch_id, from_date: from_date, to_date: to_date},
                beforeSend: function () {
                },
                success: function (return_data) {
                    if (return_data != '')
                    {
                        // alert(return_data);
                        $('#branchsupervisor').html(return_data);
                        //$(return_data).draggable().prependTo('.details');
                        //$(".commonLoaderV1").hide();
                    }
                    else
                    {
                        $('#branchsupervisor').html('');
                    }
                }
            });
            }



        }

        function show_allocated_area_managers()
        {
            var area_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (area_id !== '' && from_date !== '' && to_date !== '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/show_allocated_area_managers',
                    data: {area_id: area_id, from_date: from_date, to_date: to_date},
                    beforeSend: function () {
                    },
                    success: function (return_data) {
                        if (return_data != '')
                        {
                            // alert(return_data);
                            $('#area_manager').html(return_data);
                            //$(return_data).draggable().prependTo('.details');
                            //$(".commonLoaderV1").hide();
                        }
                        else
                        {
                            $('#area_manager').html('');
                        }
                    }
                });
            }


        }


        function show_allocated_regional_managers()
        {
            var region_id = $('.resourcedrop').val();
             var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (region_id !== '' && from_date !== '' && to_date !== '')
            {
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_allocated_regional_managers',
                data: {region_id: region_id,from_date: from_date, to_date: to_date},
                beforeSend: function () {
                },
                success: function (return_data) {
                    if (return_data != '')
                    {
                        // alert(return_data);
                        $('.regional_manager').html(return_data);
                        //$(return_data).draggable().prependTo('.details');
                        //$(".commonLoaderV1").hide();
                    }
                    else
                    {
                        $('.regional_manager').html('');
                    }
                }
            });

            }

        }


        function show_allocated_cashiers()
        {
            //var shift_id = $('.shift_resource').val();
            var shift_id = $('#shiftid').val();
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if(from_date != '' && to_date != '' && shift_id > 0){
            
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_allocated_cashiers',
                data: {shift_id: shift_id, branch_id: branch_id, from_date: from_date, to_date: to_date},
                
                success: function (return_data) {
                    if (return_data != '')
                    {
                        // alert(return_data);
                        $('.allocated_cashiers').html(return_data);
                        //$(return_data).draggable().prependTo('.details');
                        //$(".commonLoaderV1").hide();
                    }
                    else
                    {
                        $('.allocated_cashiers').html('');
                    }
                }
            });

            }

        }
        function show_allocated_baristas()
        {
            //var shift_id = $('.shift_resource').val();
            var shift_id = $('#shiftid').val();
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
             if(from_date != '' && to_date != '' && shift_id > 0){
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_allocated_baristas',
                data: {shift_id: shift_id, branch_id: branch_id, from_date: from_date, to_date: to_date},
                beforeSend: function () {
                    $(".allocated_baristas").html('Loading..');
                },
                success: function (return_data) {
                    if (return_data != '')
                    {
                        // alert(return_data);
                        $('.allocated_baristas').html(return_data);
                        //$(return_data).draggable().prependTo('.details');
                        //$(".commonLoaderV1").hide();
                    }
                    else
                    {
                        $('.allocated_baristas').html('');
                    }
                }
            });
             }


        }
////////////////////////// save supervisors for that branch .......................////////////
        function save_allocation()
        {
            var resource_category = $('.resource_category').val();
            if (resource_category == 'region')
            {
                //alert('dfsdf');
                send_region_manager_mail();
            }
            if (resource_category == 'area')
            {
                //
                send_area_manager_mail();
            }
            if (resource_category == 'branch')
            {

                send_branch_manager_mail();
                send_cashier_mail();
                send_barista_mail();

            }
            else
            {

            }

        }

////////////// save supervisors///////////////

        function send_region_manager_mail()
        {

            var region_id = $('.resourcedrop').val();
            var employee_id = $("#regional_manager").children(".empList").attr("id");
            if (!employee_id)
            {

                toastr.error('Please Select An Employee ');
            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/send_region_manager_mail',
                    data: {region_id: region_id},
                    beforeSend: function () {
                        $("#regionbtn").hide();
                    },
                    success: function (return_data) {
                        if (return_data == -1)
                        {
                            $("#regionbtn").show();
                            toastr.error('Mail Has Been Already Sent');
                        }
                        if (return_data == 1)
                        {
                            $("#regionbtn").show();
                            toastr.success('Mail Has Been Sent');
                        }
                        if (return_data == 0)
                        {
                            $("#regionbtn").show();
                            // toastr.error('Sorry There Was Some Problem Sending Mail');
                        }
                    }
                });
            }
        }
        function send_area_manager_mail()
        {

            var area_id = $('.resourcedrop').val();
            var employee_id = $("#area_manager").children(".empList").attr("id");
            if (!employee_id)
            {
                toastr.error('Please Select An Employee ');
            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/send_area_manager_mail',
                    data: {area_id: area_id},
                    beforeSend: function () {
                        $("#areabtn").hide();
                    },
                    success: function (return_data) {
                        if (return_data == -1)
                        {
                            $("#areabtn").show();
                            toastr.error('Mail Has Been Already Sent');
                        }
                        if (return_data == 1)
                        {
                            $("#areabtn").show();
                            toastr.success('Mail Has Been Sent');
                        }
                        if (return_data == 0)
                        {
                            $("#areabtn").show();
                            //toastr.error('Sorry There Was Some Problem Sending Mail');
                        }
                    }
                });
            }
        }
        function send_branch_manager_mail()
        {

            var branch_id = $('.resourcedrop').val();
            var supervisor_id = $("#branchsupervisor").children(".empList").attr("id");
            var cashier_id = $(".allocated_cashiers").children(".empList").attr("id");
            var barista_id = $(".allocated_baristas").children(".empList").attr("id");
            if (supervisor_id || cashier_id || barista_id)
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/send_branch_manager_mail',
                    data: {branch_id: branch_id},
                    beforeSend: function () {
                        $("#branchbtn").hide();
                    },
                    success: function (return_data) {
                        if (return_data == -1)
                        {
                            $("#branchbtn").show(); //toastr.error('Mail Has Been Already Sent To Supervisor');
                        }
                        if (return_data == 1)
                        {
                            $("#branchbtn").show();
                            toastr.success('Mail Has Been Sent To Supervisor');
                        }
                        if (return_data == 0)
                        {
                            $("#branchbtn").show();
                            //toastr.error('Sorry There Was Some Problem Sending Mail');
                        }
                    }
                });
            }
            else
            {
                $("#branchbtn").show();
                toastr.error('Please Select An Employee');
            }
        }
        function send_cashier_mail()
        {

            var branch_id = $('.resourcedrop').val();
            var supervisor_id = $("#branchsupervisor").children(".empList").attr("id");
            var cashier_id = $(".allocated_cashiers").children(".empList").attr("id");
            var barista_id = $(".allocated_baristas").children(".empList").attr("id");
            if (supervisor_id || cashier_id || barista_id)
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/send_cashier_mail',
                    data: {branch_id: branch_id},
                    beforeSend: function () {
                        $("#branchbtn").hide();
                    },
                    success: function (return_data) {
                        if (return_data == -1)
                        {
                            $("#branchbtn").show(); // toastr.error('Employee Has Been Already Allocated And Mail Has Been Already Sent');
                        }
                        if (return_data == 1)
                        {
                            $("#branchbtn").show();
                            toastr.success('Mail Has Been Sent To Cashier');
                        }
                        if (return_data == 0)
                        {
                            $("#branchbtn").show();
                            // toastr.error('Sorry There Was Some Problem Sending Mail');
                        }
                    }
                });
            }
            else
            {
                $("#branchbtn").show();
                //toastr.error('Please Select An Employee');
            }
        }
        function send_barista_mail()
        {

            var branch_id = $('.resourcedrop').val();
            var supervisor_id = $("#branchsupervisor").children(".empList").attr("id");
            var cashier_id = $(".allocated_cashiers").children(".empList").attr("id");
            var barista_id = $(".allocated_baristas").children(".empList").attr("id");
            if (supervisor_id || cashier_id || barista_id)
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/send_barista_mail',
                    data: {branch_id: branch_id},
                    beforeSend: function () {
                        $("#branchbtn").hide();
                    },
                    success: function (return_data) {
                        if (return_data == -1)
                        {
                            $("#branchbtn").show(); // toastr.error('Employee Has Been Already Allocated And Mail Has Been Already Sent');
                        }
                        if (return_data == 1)
                        {
                            $("#branchbtn").show();
                            toastr.success('Mail Has Been Sent To Barista');
                        }
                        if (return_data == 0)
                        {
                            $("#branchbtn").show();
                            //toastr.error('Sorry There Was Some Problem Sending Mail');
                        }
                    }
                });
            }
            else
            {
                $("#branchbtn").show();
                //toastr.error('Please Select An Employee');
            }
        }
        function save_supervisors(id)
        {

            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {
                toastr.error('Please Select Dates');
                show_supervisors();
                show_allocated_supervisors();
            }
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/save_supervisors',
                data: {employee_id: id, branch_id: branch_id, from_date: from_date, to_date: to_date},
                success: function (return_data) {
                    if (return_data === '-1')
                    {
                        show_supervisors();
                        show_allocated_supervisors();
                        show_branch_supervisor_modal(id, branch_id, from_date, to_date);
                    }
                    if (return_data !== '-1')
                    {
                        // alert(return_data);
                        toastr.success(return_data);
                        show_allocated_supervisors();
                        show_supervisors();
                        //$(".commonLoaderV1").hide();
                    }
                    else
                    {

                        // $(".commonLoaderV1").hide();
                        //$('.dropme').html('');
                    }
                }
            });
        }

        function save_regional_managers(id)
        {

            var region_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {
                toastr.error('Please Select Dates');
                show_regional_managers();
                show_allocated_regional_managers();
            }
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/save_regional_managers',
                data: {employee_id: id, region_id: region_id, from_date: from_date, to_date: to_date},
                success: function (return_data) {
                    if (return_data === '-1')
                    {
                        show_regional_managers();
                        show_allocated_regional_managers();
                        show_region_modal(id, region_id, from_date, to_date);
                    }
                    if (return_data !== '-1')
                    {
                        // alert(return_data);
                        toastr.success(return_data);
                        show_regional_managers();
                        show_allocated_regional_managers();

                        //$(".commonLoaderV1").hide();
                    }
                    else
                    {

                        // $(".commonLoaderV1").hide();
                        //$('.dropme').html('');
                    }
                }
            });

        }
        function save_area_managers(id)
        {

            var area_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {
                toastr.error('Please Select Dates');
                show_area_managers();
                show_allocated_area_managers();
            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/save_area_managers',
                    data: {employee_id: id, area_id: area_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        if (return_data === '-1')
                        {
                            show_area_managers();
                            show_allocated_area_managers();
                            show_area_modal(id, area_id, from_date, to_date);
                        }
                        else if (return_data !== '-1')
                        {

                            show_area_managers();
                            show_allocated_area_managers();
                            toastr.success(return_data);
                            //$(".commonLoaderV1").hide();
                        }
                        else
                        {
                            toastr.error('Sorry There Was Some Problem');
                            // $(".commonLoaderV1").hide();
                            //$('.dropme').html('');
                        }
                    }
                });
            }


        }
        ////////////////////////////edit regional managers//////////////
        function edit_region_managers(id)
        {
            var area_id = $('.resourcedrop').val();
            var from_date = $('#region_from_date_'+id).val();
            
            var to_date = $('#region_to_date_'+id).val();
            var from_date1 = $('#from_date').val();
            var to_date1 = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {

            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/edit_region_managers',
                    data: {ra_id: id, area_id: area_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        console.log(return_data);
                        if (return_data !== '-1')
                        {
                            show_region_modal(id, area_id, from_date1, to_date1);
                            show_regional_managers();
                            show_allocated_regional_managers();
                            toastr.success('Allocation dates has been updated.');
                            //toastr.success(return_data.employee + ' Has Been Allocated From ' + return_data.from + ' To ' + return_data.to);
                        }
                        if (return_data === '-1')
                        {
                            toastr.error('Another Employee Has Been Already Allocated In These Dates');
                        }
                    }
                });
            }


        }
        /////////////////////////////////edit_area_managers//////////////////////
        function edit_area_managers(id)
        {

            var area_id = $('.resourcedrop').val();
            
            var from_date = $('#area_from_date_'+id).val();
            
            var to_date = $('#area_to_date_'+id).val();
            
            var from_date1 = $('#from_date').val();
            
            var to_date1 = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {

            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/edit_area_managers',
                    data: {ra_id: id, area_id: area_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        console.log(return_data);
                        if (return_data !== '-1')
                        {
                            show_area_modal(id, area_id, from_date1, to_date1);
                            show_area_managers();
                            show_allocated_area_managers();
                            toastr.success(return_data.employee + ' Has Been Allocated From ' + return_data.from + ' To ' + return_data.to);
                        }
                        if (return_data === '-1')
                        {
                            toastr.error('Another Employee Has Been Already Allocated In That Dates');
                        }
                    }
                });
            }


        }
        
        /////////////////////////////////edit_branch_supervisor//////////////////////
        function edit_branch_supervisor(id)
        {

            var branch_id = $('.resourcedrop').val();
            
            var from_date = $('#branch_from_date_'+id).val();
            
            var to_date = $('#branch_to_date_'+id).val();
            
            var from_date1 = $('#from_date').val();
            
            var to_date1 = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {

            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/edit_branch_supervisor',
                    data: {ra_id: id, branch_id: branch_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        console.log(return_data);
                        if (return_data !== '-1')
                        {
                            show_branch_supervisor_modal(id, branch_id, from_date1, to_date1);
                            show_supervisors();
                            show_allocated_supervisors();
                            toastr.success(return_data.employee + ' Has Been Allocated From ' + return_data.from + ' To ' + return_data.to);
                        }
                        if (return_data === '-1')
                        {
                            toastr.error('Another Employee Has Been Already Allocated In These Dates');
                        }
                    }
                });
            }


        }
        
        //////////////////////////show_region_modal////////////////////////////
        function show_region_modal(employee_id, region_id, from_date, to_date)
        {

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_region_modal',
                data: {employee_id: employee_id, region_id: region_id, from_date: from_date, to_date: to_date},
                success: function (return_data) {

                    $(".overlay").css("display", "block");
                    $(".modalHolderV1").css("display", "block");
                    $('.modalScroll_region').html(return_data);

                }
            });


        }
        ///////////////////////////show_area_modal//////////////////
        function show_area_modal(employee_id, area_id, from_date, to_date)
        {

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_area_modal',
                data: {employee_id: employee_id, area_id: area_id, from_date: from_date, to_date: to_date},
                success: function (return_data) {

                    $(".overlay").css("display", "block");
                    $(".modalHolderV1").css("display", "block");
                    $('.modalScroll_area').html(return_data);

                }
            });


        }
        
        //////////////////////////show_branch_supervisor_modal////////////////////////////
        function show_branch_supervisor_modal(employee_id, branch_id, from_date, to_date)
        {

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_branch_supervisor_modal',
                data: {employee_id: employee_id, branch_id: branch_id, from_date: from_date, to_date: to_date},
                success: function (return_data) {

                    $(".overlay").css("display", "block");
                    $(".modalHolderV1").css("display", "block");
                    $('.modalScroll_region').html(return_data);

                }
            });


        }
        
        //////////////////////////show_branch_barista_modal////////////////////////////
        function show_branch_barista_modal(employee_id, branch_id, from_date, to_date)
        {

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_branch_barista_modal',
                data: {employee_id: employee_id, branch_id: branch_id, from_date: from_date, to_date: to_date},
                success: function (return_data) {

                    $(".overlay").css("display", "block");
                    $(".modalHolderV1").css("display", "block");
                    $('.modalScroll_region').html(return_data);

                }
            });


        }
        
        //////////////////////////show_branch_cashier_modal////////////////////////////
        function show_branch_cashier_modal(employee_id, branch_id, from_date, to_date,shift_id)
        {

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/show_branch_cashier_modal',
                data: {employee_id: employee_id, branch_id: branch_id, from_date: from_date, to_date: to_date,shift_id:shift_id},
                success: function (return_data) {

                    $(".overlay").css("display", "block");
                    $(".modalHolderV1").css("display", "block");
                    $('.modalScroll_region').html(return_data);

                }
            });


        }


        function regional_manager_allocations_modal(employee_id)
        {

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/regional_manager_allocations_modal',
                data: {employee_id: employee_id},
                success: function (return_data) {

                    $(".overlay").css("display", "block");
                    $(".modalHolderV1").css("display", "block");
                    $('.modalScroll_region').html(return_data);

                }
            });


        }
        
        function edit_region_managers_allocations(id,employee_id)
        {
            var area_id = $('.resourcedrop').val();
            var from_date = $('.edit_from_date').val();
            var to_date = $('.edit_to_date').val();
            var from_date1 = $('#from_date').val();
            var to_date1 = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {

            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/edit_region_managers',
                    data: {ra_id: id, area_id: area_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        console.log(return_data);
                        if (return_data !== '-1')
                        {
                            regional_manager_allocations_modal(employee_id);
                            show_regional_managers();
                            show_allocated_regional_managers();
                            toastr.success('Allocation dates has been updated.');
                            //toastr.success(return_data.employee + ' Has Been Allocated From ' + return_data.from + ' To ' + return_data.to);
                        }
                        if (return_data === '-1')
                        {
                            toastr.error('Another Employee Has Been Already Allocated In These Dates');
                        }
                    }
                });
            }


        }
        
        
        function area_manager_allocations_modal(employee_id)
        {

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/area_manager_allocations_modal',
                data: {employee_id: employee_id},
                success: function (return_data) {

                    $(".overlay").css("display", "block");
                    $(".modalHolderV1").css("display", "block");
                    $('.modalScroll_region').html(return_data);

                }
            });


        }
        
        function edit_area_managers_allocations(id,employee_id)
        {

            var area_id = $('.resourcedrop').val();
            
            var from_date = $('#area_from_date_'+id).val();
            
            var to_date = $('#area_to_date_'+id).val();
            
            var from_date1 = $('#from_date').val();
            
            var to_date1 = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {

            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/edit_area_managers',
                    data: {ra_id: id, area_id: area_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        console.log(return_data);
                        if (return_data !== '-1')
                        {
                            area_manager_allocations_modal(employee_id);
                            show_area_managers();
                            show_allocated_area_managers();
                            toastr.success(return_data.employee + ' Has Been Allocated From ' + return_data.from + ' To ' + return_data.to);
                        }
                        if (return_data === '-1')
                        {
                            toastr.error('Another Employee Has Been Already Allocated In That Dates');
                        }
                    }
                });
            }


        }
        
        
        
        function supervisor_allocations_modal(employee_id)
        {

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/supervisor_allocations_modal',
                data: {employee_id: employee_id},
                success: function (return_data) {

                    $(".overlay").css("display", "block");
                    $(".modalHolderV1").css("display", "block");
                    $('.modalScroll_region').html(return_data);

                }
            });


        }
        
        function edit_supervisor_allocations(id,employee_id)
        {
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#branch_from_date_'+id).val();
            var to_date = $('#branch_to_date_'+id).val();
            var from_date1 = $('#from_date').val();
            var to_date1 = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {

            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/edit_branch_supervisor',
                    data: {ra_id: id, branch_id: branch_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        console.log(return_data);
                        if (return_data !== '-1')
                        {
                            supervisor_allocations_modal(employee_id);
                            show_supervisors();
                            show_allocated_supervisors();
                            toastr.success('Allocation dates has been updated.');
                            //toastr.success(return_data.employee + ' Has Been Allocated From ' + return_data.from + ' To ' + return_data.to);
                        }
                        if (return_data === '-1')
                        {
                            toastr.error('Another Employee Has Been Already Allocated In These Dates');
                        }
                    }
                });
            }


        }
        
        function barista_allocations_modal(employee_id)
        {

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/barista_allocations_modal',
                data: {employee_id: employee_id},
                success: function (return_data) {

                    $(".overlay").css("display", "block");
                    $(".modalHolderV1").css("display", "block");
                    $('.modalScroll_region').html(return_data);

                }
            });


        }
        
        function edit_barista_allocations(id,employee_id)
        {
            var branch_id = $('.resourcedrop').val();
            var shift_id = $('#shiftid').val();
            var from_date = $('#branch_from_date_'+id).val();
            var to_date = $('#branch_to_date_'+id).val();
            var from_date1 = $('#from_date').val();
            var to_date1 = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {

            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/edit_branch_barista',
                    data: {ra_id: id, branch_id: branch_id, shift_id: shift_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        console.log(return_data);
                        if (return_data !== '-1')
                        {
                            barista_allocations_modal(employee_id);
                            show_baristas();
                            show_allocated_baristas();
                            toastr.success('Allocation dates has been updated.');
                            //toastr.success(return_data.employee + ' Has Been Allocated From ' + return_data.from + ' To ' + return_data.to);
                        }
                        if (return_data === '-1')
                        {
                            toastr.error('Another Employee Has Been Already Allocated In These Dates');
                        }
                    }
                });
            }


        }
        
        function cashier_allocations_modal(employee_id)
        {

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/cashier_allocations_modal',
                data: {employee_id: employee_id},
                success: function (return_data) {

                    $(".overlay").css("display", "block");
                    $(".modalHolderV1").css("display", "block");
                    $('.modalScroll_region').html(return_data);

                }
            });


        }
        
        
        function edit_cashier_allocations(id,employee_id)
        {
            var branch_id = $('.resourcedrop').val();
            var shift_id = $('#shiftid').val();
            var from_date = $('#branch_from_date_'+id).val();
            var to_date = $('#branch_to_date_'+id).val();
            var from_date1 = $('#from_date').val();
            var to_date1 = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {

            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/edit_branch_cashier',
                    data: {ra_id: id, branch_id: branch_id, shift_id: shift_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        console.log(return_data);
                        if (return_data !== '-1')
                        {
                            cashier_allocations_modal(employee_id);
                            show_cashiers();
                            show_allocated_cashiers();
                            toastr.success('Allocation dates has been updated.');
                            //toastr.success(return_data.employee + ' Has Been Allocated From ' + return_data.from + ' To ' + return_data.to);
                        }
                        if (return_data === '-1')
                        {
                            toastr.error('Another Employee Has Been Already Allocated In These Dates');
                        }
                    }
                });
            }


        }
        
        
        ///////////////////////////close_modal//////////////////
        function close_modal()
        {
            $(".overlay").css("display", "none");
            $(".modalHolderV1").css("display", "none");
            $('.modalScroll').html('');

        }
/////////////////////////save cashiers/////////////////
        function save_cashiers_shift1(id)
        {

            var branch_id = $('.resourcedrop').val();
            //var employee_id = $(".allocated_cashiers").children(".empList").attr("id");
            //alert(employee_id);
            if (id)
            {
                var shift_id = $.trim($('#shiftid').val());
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();

                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/save_cashiers_shift1',
                    data: {employee_id: id, branch_id: branch_id, shift_id: shift_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        if (return_data === '-1')
                        {
                            show_cashiers();
                            show_allocated_cashiers();
                            show_branch_cashier_modal(id, branch_id, from_date, to_date,shift_id);
                        }
                        if (return_data !== '-1')
                        {
                            // alert(return_data);
                            show_cashiers();
                            show_allocated_cashiers();
                            toastr.success(return_data);
                            //$(".commonLoaderV1").hide();
                        }
                        else
                        {
                            //toastr.error('Sorry There Was Some Problem');
                            // $(".commonLoaderV1").hide();
                            //$('.dropme').html('');
                        }
                    }
                });
            }

        }

        function deallocated_cashiers(id)
        {

            var branch_id = $('.resourcedrop').val();
            //var employee_id = $(".allocated_cashiers").children(".empList").attr("id");
            //alert(employee_id);
            if (id)
            {
                var shift_id = $('.shift_resource').val();

                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/deallocated_cashiers',
                    data: {employee_id: id, branch_id: branch_id, shift_id: shift_id},
                    success: function (return_data) {
                        if (return_data == -1)
                        {

                        }
                        else if (return_data !== '')
                        {
                            // alert(return_data);
                            toastr.success(return_data);
                            show_cashiers();
                            show_allocated_cashiers();
                            //$(".commonLoaderV1").hide();
                        }
                        else
                        {

                            toastr.error('Sorry There Was Some Problem'); // $(".commonLoaderV1").hide();
                            //$('.dropme').html('');
                        }
                    }
                });
            }

        }
        function deallocate_supervisors(id)
        {

            var branch_id = $('.resourcedrop').val();
            //var employee_id = $(".allocated_cashiers").children(".empList").attr("id");
            //alert(employee_id);
            if (id)
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/deallocate_supervisors',
                    data: {employee_id: id, branch_id: branch_id},
                    success: function (return_data) {
                        if (return_data == -1)
                        {

                        }
                        else if (return_data !== '')
                        {
                            // alert(return_data);
                            show_supervisors();
                            show_allocated_supervisors();
                            toastr.success(return_data);
                            //$(".commonLoaderV1").hide();
                        }
                        else
                        {

                            toastr.error('Sorry There Was Some Problem'); // $(".commonLoaderV1").hide();
                            //$('.dropme').html('');
                        }
                    }
                });
            }

        }
        function deallocate_regional_managers(id)
        {

            var region_id = $('.resourcedrop').val();
            if (id)
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/deallocate_regional_managers',
                    data: {employee_id: id, region_id: region_id},
                    success: function (return_data) {
                        if (return_data == -1)
                        {

                        }
                        else if (return_data !== '')
                        {
                            // alert(return_data);
                            show_regional_managers();
                            show_allocated_regional_managers();
                            toastr.success(return_data);
                            //$(".commonLoaderV1").hide();
                        }
                        else
                        {

                            toastr.error('Sorry There Was Some Problem');  // $(".commonLoaderV1").hide();
                            //$('.dropme').html('');
                        }
                    }
                });
            }

        }
        function deallocate_area_managers(id)
        {

            var area_id = $('.resourcedrop').val();
            if (id)
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/deallocate_area_managers',
                    data: {employee_id: id, area_id: area_id},
                    success: function (return_data) {
                        if (return_data == -1)
                        {

                        }
                        else if (return_data !== '')
                        {
                            // alert(return_data);
                            show_area_managers();
                            show_allocated_area_managers();
                            toastr.success(return_data);
                            //$(".commonLoaderV1").hide();
                        }
                        else
                        {

                            toastr.error('Sorry There Was Some Problem');// $(".commonLoaderV1").hide();
                            //$('.dropme').html('');
                        }
                    }
                });
            }

        }
        function deallocated_baristas(id)
        {

            var branch_id = $('.resourcedrop').val();
            //var employee_id = $(".allocated_cashiers").children(".empList").attr("id");
            //alert(employee_id);
            if (id)
            {
                var shift_id = $('.shift_resource').val();

                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/deallocated_baristas',
                    data: {employee_id: id, branch_id: branch_id, shift_id: shift_id},
                    beforeSend: function () {
                        $(".commonLoaderV1").show();
                    },
                    complete: function () {
                        $(".commonLoaderV1").hide();
                    },
                    success: function (return_data) {
                        if (return_data == -1)
                        {

                        }
                        else if (return_data !== '')
                        {
                            show_allocated_baristas();
                            show_baristas();
                            toastr.success(return_data);
                            //$(".commonLoaderV1").hide();
                        }
                        else
                        {
                            // alert(return_data);
                            toastr.error('Sorry There Was Some Problem'); // $(".commonLoaderV1").hide();
                            //$('.dropme').html('');
                        }
                    }
                });
            }

        }
///////////////////////////////////////////////////////////////////////////

///////////////////////////////////save barista//////////////
        function save_barista(id)
        {
            var branch_id = $('.resourcedrop').val();
            //var shift_id = $('.shift_resource').val();
            var shift_id = $('#shiftid').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/save_barista',
                data: {employee_ids: id, branch_id: branch_id, shift_id: shift_id, from_date: from_date, to_date: to_date},
                success: function (return_data) {
                    if (return_data === '-1')
                    {
                        show_baristas();
                        show_allocated_baristas();
                        show_branch_barista_modal(id, branch_id, from_date, to_date);
                    }
                    
                    if (return_data !== '-1')
                    {
                        // alert(return_data);
                        show_baristas();
                        show_allocated_baristas();
                        toastr.success(return_data);
                        //$(".commonLoaderV1").hide();
                    }
                    
                }
            });
        }

        function check_cashier_nationality(id)
        {
            var branch_id = $('.resourcedrop').val();

//            var shift_id = $('.shift_resource').val();
            var shift_id = $('#shiftid').val();

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/check_nationality',
                data: {employee_id: id, branch_id: branch_id, shift_id: shift_id},
                success: function (return_data) {
//                    if (return_data !== '')
//                    {
//
//                        toastr.error(return_data);
//                        show_cashiers();
//                        show_allocated_cashiers();
//                        //$(".commonLoaderV1").hide();
//                    }
//                    else
//                    {
//                        save_cashiers_shift1(id);
//                    }

                    if (return_data == 1)
                    {
                        toastr.error("Same Country Employee Exist");
                    }
                    save_cashiers_shift1(id);
                }
            });
        }

        function check_barista_nationality(id)
        {
            var branch_id = $('.resourcedrop').val();

//            var shift_id = $('.shift_resource').val();
            var shift_id = $('#shiftid').val();

            $.ajax({
                type: 'POST',
                url: 'resource_allocation/check_nationality',
                data: {employee_id: id, branch_id: branch_id, shift_id: shift_id},
                success: function (return_data) {
                    if (return_data == 1)
                    {
                        toastr.error("Same Country Employee Exist");
                    }
                    save_barista(id);
                }
            });
        }
        //////////////////////////////////////////////////////////free_region_employee//////////////////
        function free_region_employee(id)
        {
            var ff = 0;
            var area_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/free_region_employee',
                data: {id: id},
                success: function (return_data) {

                    if (return_data)
                    {
                        toastr.success(return_data);
                        show_region_modal(ff, area_id, from_date, to_date);
                        show_regional_managers();
                        show_allocated_regional_managers();
                    }
                    else {
                        toastr.error('Sorry There Is Some Problem');
                        show_regional_managers();
                        show_allocated_regional_managers();
                    }
                }
            });
        }
//////////////////////////free_employee////////////////////
        function free_area_employee(id)
        {
            var ff = 0;
            var area_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/free_area_employee',
                data: {id: id},
                success: function (return_data) {

                    if (return_data)
                    {
                        toastr.success(return_data);
                        show_area_modal(ff, area_id, from_date, to_date);
                        show_area_managers();
                        show_allocated_area_managers();
                    }
                    else {
                        toastr.error('Sorry There Is Some Problem');
                        show_area_managers();
                        show_allocated_area_managers();
                    }
                }
            });
        }
        //////////////////////////////////////////////////////////free_branch_supervisor//////////////////
        function free_branch_supervisor(id)
        {
            var ff = 0;
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/free_branch_supervisor',
                data: {id: id},
                success: function (return_data) {

                    if (return_data)
                    {
                        toastr.success(return_data);
                        show_supervisors();
                        show_allocated_supervisors();
                        show_branch_supervisor_modal(id, branch_id, from_date, to_date);
                    }
                    else {
                        toastr.error('Sorry There Is Some Problem');
                        show_supervisors();
                        show_allocated_supervisors();
                    }
                }
            });
        }
        
        //////////////////////////////////////////////////////////free_branch_barista//////////////////
        function free_branch_barista(id)
        {
            var ff = 0;
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/free_branch_barista',
                data: {id: id},
                success: function (return_data) {

                    if (return_data)
                    {
                        toastr.success(return_data);
                        show_baristas();
                        show_allocated_baristas();
                        show_branch_barista_modal(id, branch_id, from_date, to_date);
                    }
                    else {
                        //toastr.error('Sorry There Is Some Problem');
                        show_baristas();
                        show_allocated_baristas();
                    }
                }
            });
        }
        
        
        //////////////////////////////////////////////////////////free_branch_cashier//////////////////
        function free_branch_cashier(id)
        {
            var ff = 0;
            var shift_id = $.trim($('#shiftid').val());
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/free_branch_cashier',
                data: {id: id},
                success: function (return_data) {

                    if (return_data)
                    {
                        toastr.success(return_data);
                        show_cashiers();
                        show_allocated_cashiers();
                        show_branch_cashier_modal(id, branch_id, from_date, to_date,shift_id);
                    }
                    else {
                        //toastr.error('Sorry There Is Some Problem');
                        show_cashiers();
                        show_allocated_cashiers();
                    }
                }
            });
        }
        
        /////////////////////////////////edit_branch_barista//////////////////////
        function edit_branch_barista(id)
        {

            var branch_id = $('.resourcedrop').val();
            
            var shift_id = $('#shiftid').val();
            
            var from_date = $('#branch_from_date_'+id).val();
            
            var to_date = $('#branch_to_date_'+id).val();
            
            var from_date1 = $('#from_date').val();
            
            var to_date1 = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {

            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/edit_branch_barista',
                    data: {ra_id: id, branch_id: branch_id, shift_id: shift_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        if (return_data !== '-1')
                        {
                            show_branch_barista_modal(id, branch_id, from_date1, to_date1);
                            show_baristas();
                            show_allocated_baristas();
                            toastr.success(return_data.employee + ' Has Been Allocated From ' + return_data.from + ' To ' + return_data.to);
                        }
                        if (return_data === '-1')
                        {
                            toastr.error('Another Employee Has Been Already Allocated In These Dates');
                        }
                    }
                });
            }


        }
        
        
        /////////////////////////////////edit_branch_cashier//////////////////////
        function edit_branch_cashier(id)
        {

            var branch_id = $('.resourcedrop').val();
            
            var shift_id = $.trim($('#shiftid').val());
            
            var from_date = $('#branch_from_date_'+id).val();
            
            var to_date = $('#branch_to_date_'+id).val();
            
            var from_date1 = $('#from_date').val();
            
            var to_date1 = $('#to_date').val();
            if (from_date === '' || to_date === '')
            {

            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: 'resource_allocation/edit_branch_cashier',
                    data: {ra_id: id, branch_id: branch_id, shift_id: shift_id, from_date: from_date, to_date: to_date},
                    success: function (return_data) {
                        if (return_data !== '-1')
                        {
                            show_branch_cashier_modal(id, branch_id, from_date1, to_date1,shift_id);
                            show_cashiers();
                            show_allocated_cashiers();
                            toastr.success(return_data.employee + ' Has Been Allocated From ' + return_data.from + ' To ' + return_data.to);
                        }
                        if (return_data === '-1')
                        {
                            toastr.error('Another Employee Has Been Already Allocated In These Dates');
                        }
                    }
                });
            }


        }
        
        function free_region_manager(id,employee_id)
        {
            var ff = 0;
            var area_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/free_region_employee',
                data: {id: id},
                success: function (return_data) {

                    if (return_data)
                    {
                        toastr.success(return_data);
                        regional_manager_allocations_modal(employee_id);
                        show_regional_managers();
                        show_allocated_regional_managers();
                    }
                    else {
                        toastr.error('Sorry There Is Some Problem');
                        show_regional_managers();
                        show_allocated_regional_managers();
                    }
                }
            });
        }
        
        function free_area_manager(id,employee_id)
        {
            var ff = 0;
            var area_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/free_region_employee',
                data: {id: id},
                success: function (return_data) {

                    if (return_data)
                    {
                        toastr.success(return_data);
                        area_manager_allocations_modal(employee_id);
                        show_area_managers();
                        show_allocated_area_managers();
                    }
                    else {
                        toastr.error('Sorry There Is Some Problem');
                        show_area_managers();
                        show_allocated_area_managers();
                    }
                }
            });
        }
        
        function free_supervisor(id,employee_id)
        {
            var ff = 0;
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/free_branch_supervisor',
                data: {id: id},
                success: function (return_data) {

                    if (return_data)
                    {
                        toastr.success(return_data);
                        show_supervisors();
                        show_allocated_supervisors();
                        supervisor_allocations_modal(employee_id);
                    }
                    else {
                        toastr.error('Sorry There Is Some Problem');
                        show_supervisors();
                        show_allocated_supervisors();
                    }
                }
            });
        }
        
        function free_barista(id,employee_id)
        {
            var ff = 0;
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/free_branch_barista',
                data: {id: id},
                success: function (return_data) {

                    if (return_data)
                    {
                        toastr.success(return_data);
                        show_baristas();
                        show_allocated_baristas();
                        barista_allocations_modal(employee_id);
                    }
                    else {
                        //toastr.error('Sorry There Is Some Problem');
                        show_baristas();
                        show_allocated_baristas();
                    }
                }
            });
        }
        
        
        //////////////////////////////////////////////////////////free_branch_cashier//////////////////
        function free_cashier(id,employee_id)
        {
            var ff = 0;
            var branch_id = $('.resourcedrop').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            $.ajax({
                type: 'POST',
                url: 'resource_allocation/free_branch_cashier',
                data: {id: id},
                success: function (return_data) {

                    if (return_data)
                    {
                        toastr.success(return_data);
                        show_cashiers();
                        show_allocated_cashiers();
                        cashier_allocations_modal(employee_id);
                    }
                    else {
                        //toastr.error('Sorry There Is Some Problem');
                        show_cashiers();
                        show_allocated_cashiers();
                    }
                }
            });
        }
//////////////////////////////////////////end save barista//////////////////////
        $('body').on('click', '.shift_name', function () {
            var id = $(this).val();
            $('.shift_name').removeClass('selected');
            $(this).addClass('selected');
            $('.shift_resource').val(id);
            var shift_name = $(this).attr("id");
            $(".cashier_shift").html('Selected Cashier For ' + shift_name);
            $(".barista_shift").html('Selected Baristas For ' + shift_name);
            show_cashiers();
            show_baristas();
            show_allocated_cashiers();
            show_allocated_baristas();
        });
        $(function () {


            $('.tabContents').hide();
            $(".allocated_cashiers").droppable({
                drop: function (event, ui) {
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    check_cashier_nationality(id);

                }
            });

            $("#branchsupervisor").droppable({
                drop: function (event, ui) {
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    save_supervisors(id);
                }
            });
            
            $("#regional_manager").droppable({
                drop: function (event, ui) {
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    save_regional_managers(id);
                }
            });
            $("#destination").droppable({
                drop: function (event, ui) {
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    deallocate_supervisors(id);
                }
            });
            $("#area_manager").droppable({
                drop: function (event, ui) {
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    save_area_managers(id);
                }
            });
            $("#area_allemps").droppable({
                drop: function (event, ui) {
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    deallocate_area_managers(id);
                }
            });
            $("#regionalmanager_allemps").droppable({
                drop: function (event, ui) {
                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    deallocate_regional_managers(id);
                }
            });
            $(".deallocated_cashiers").droppable({
                drop: function (event, ui) {

                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    deallocated_cashiers(id);
                }
            });

            $(".allocated_baristas").droppable({
                drop: function (event, ui) {

                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    check_barista_nationality(id);

                }
            });
            $(".deallocated_baristas").droppable({
                drop: function (event, ui) {

                    var draggable = ui.draggable;
                    var id = draggable.attr("id");
                    deallocated_baristas(id);
                }
            });


            $('#filter').keyup(function () {
                var filter_array = new Array();
                var filter = this.value.toLowerCase();
                filter_array = filter.split(' ');
                var arrayLength = filter_array.length;
                $('.search_emp').each(function () {
                    var _this = $(this);
                    var title = _this.find('b:first').text().toLowerCase();
                    var hidden = 0;
                    for (var i = 0; i < arrayLength; i++) {
                        if (title.indexOf(filter_array[i]) < 0) {
                            _this.hide();
                            hidden = 1;
                        }
                    }
                    if (hidden == 0) {
                        _this.show();
                    }
                });
                
                if($('.search_emp:visible').length==0){
                    $('.noRecordsReg').show()
                }else{
                    $('.noRecordsReg').hide()
                }
               
            });

            $('#areafilter').keyup(function () {
                var filter_array = new Array();
                var filter = this.value.toLowerCase();
                filter_array = filter.split(' ');
                var arrayLength = filter_array.length;
                $('.area_emp').each(function () {
                    var _this = $(this);
                    
                    var title = _this.find('b:first').text().toLowerCase();
                   
                    var hidden = 0;
                    for (var i = 0; i < arrayLength; i++) {
                        if (title.indexOf(filter_array[i]) < 0) {
                            _this.hide();
                            hidden = 1;
                        }
                    }
                    if (hidden == 0) {
                        _this.show();
                    }
                });
                
                if($('.area_emp:visible').length==0){
                    $('.noRecordsArea').show()
                }else{
                    $('.noRecordsArea').hide()
                }
            });
            $('#supfilter').keyup(function () {
                var filter_array = new Array();
                var filter = this.value.toLowerCase();
                filter_array = filter.split(' ');
                var arrayLength = filter_array.length;
                $('.sup_emp').each(function () {
                    var _this = $(this);
                    var title = _this.find('b:first').text().toLowerCase();
                    var hidden = 0;
                    for (var i = 0; i < arrayLength; i++) {
                        if (title.indexOf(filter_array[i]) < 0) {
                            _this.hide();
                            hidden = 1;
                        }
                    }
                    if (hidden == 0) {
                        _this.show();
                    }
                });
                
                if($('.sup_emp:visible').length==0){
                    $('.noRecordsSup').show()
                }else{
                    $('.noRecordsSup').hide()
                }
                
            });

            $('#cashfilter').keyup(function () {
                var filter_array = new Array();
                var filter = this.value.toLowerCase();
                filter_array = filter.split(' ');
                var arrayLength = filter_array.length;
                $('.cash_emp').each(function () {
                    var _this = $(this);
                    var title = _this.find('b:first').text().toLowerCase();
                    var hidden = 0;
                    for (var i = 0; i < arrayLength; i++) {
                        if (title.indexOf(filter_array[i]) < 0) {
                            _this.hide();
                            hidden = 1;
                        }
                    }
                    if (hidden == 0) {
                        _this.show();
                    }
                });
                
                if($('.cash_emp:visible').length==0){
                    $('.noRecordsCash').show()
                }else{
                    $('.noRecordsCash').hide()
                }
                
            });

            $('#barfilter').keyup(function () {

                var filter_array = new Array();
                var filter = this.value.toLowerCase();
                filter_array = filter.split(' ');
                var arrayLength = filter_array.length;
                $('.bar_emp').each(function () {
                    var _this = $(this);
                    var title = _this.find('b:first').text().toLowerCase();
                    var hidden = 0;
                    for (var i = 0; i < arrayLength; i++) {
                        if (title.indexOf(filter_array[i]) < 0) {
                            _this.hide();
                            hidden = 1;
                        }
                    }
                    if (hidden == 0) {
                        _this.show();
                    }
                });
                
                if($('.bar_emp:visible').length==0){
                    $('.noRecordsBar').show()
                }else{
                    $('.noRecordsBar').hide()
                }
                
            });
        });
////////////////////////////////////
    </script>

    <div  class="allocateHolder">
        <div class="allocateTop">
            <div class="commonTabV1">
                <ul>
                    <li class="region resourcetype" id="region" ><a>Region</a></li>
                    <li class="area resourcetype" id="area" ><a>Area</a></li>
                    <li class="branch resourcetype" id="branch"><a>Branch</a></li>
                </ul>
            </div>

            <div class="fieldGroup">
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="inputHolder valErrorV1 branchdrop">
                            <label>Select Branches</label>
                            <select id="resourcedrop">

                            </select>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder valErrorV1 job_positions">
                            <label>Select Job Position</label>
                            <select id="job_position">
                                <option value='-1'>Select Job Position</option>
                                <option value='Supervisor'>Supervisor</option>
                                <option value='Barista'>Barista</option>
                                <option value='Cashier'>Cashier</option>
                            </select>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder valErrorV1 shift">
                            <label>Select Job Shift</label>
                            <select id="shiftid">
                                
                            </select>
                        </div>
                    </div>
                    <input type="hidden" value='' class="resource_category">
                    <input type="hidden" value='' class="shift_resource">
                    
                </div>
                <div class="dates_div custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>From Date</label>
                                <input class="date" type="text" name="from_date" id="from_date" readonly="readonly">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>To Date</label>
                                <input class="date" type="text" name="to_date" id="to_date" readonly="readonly">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        <!--//////////////////////////////////////regiondiv//////////////////////////////////////////////////////-->
        <div class="allocateBottom" id="regiondiv">
            <div class="commonDragHolderV1">
                <h3>Region <span>Allocation</span></h3>
                <div class="dragContainer">

                    <div class="dragContent assignHolder">
                        <p>Selected Regional Managers </p>
                        <div class="dragnDroper dropme regional_manager" id="regional_manager">

                        </div>
                    </div>

                    <div class="dragBtnCtrl">
                        <ul>
                            <li><a href="javascript:void(0)" class="btnLeft" title="Add"></a></li>
                            <li><a href="javascript:void(0)" class="btnRight" title="Remove"></a></li>
                        </ul>
                    </div>

                    <div class="dragContent assignHolder">
                        <p>All Regional Managers</p>
                        <div class="dragnDroper">
                            <input type="text" placeholder="Search" id="filter">
                            <div class="dropme allow1 ui-sortable regionalmanager_allemps" id="regionalmanager_allemps">

                            </div>
                            <div style="display: none;padding-left: 15px;" class="noRecordsReg">No Records Found</div>
                        </div>
                    </div>
                </div>
            </div>   
            <a class="commonBtn bgGreen addBtn" id="regionbtn"onclick="save_allocation()">Send Email</a>
        </div>
        <!--/////////////////////////////////////end region div////////////////////////////////////-->

        <!--//////////////////////////////////////area div//////////////////////////////////////////////////////-->
        <div class="allocateBottom" id="areadiv">
            <div class="commonDragHolderV1">
                <h3>Area <span>Allocation</span></h3>
                <div class="dragContainer">

                    <div class="dragContent assignHolder">
                        <p>Selected Area Managers</p>
                        <div class="dragnDroper dropme area_manager" id="area_manager">

                        </div>
                    </div>

                    <div class="dragBtnCtrl">
                        <ul>
                            <li><a href="javascript:void(0)" class="btnLeft" title="Add"></a></li>
                            <li><a href="javascript:void(0)" class="btnRight" title="Remove"></a></li>
                        </ul>
                    </div>
                    <div class="dragContent assignHolder">
                        <p>All Area Managers</p>
                        <div class="dragnDroper">
                            <input type="text" placeholder="Search" id="areafilter">
                            <div class="dropme allow1 ui-sortable area_allemps" id="area_allemps">

                            </div>
                            <div style="display: none;padding-left: 15px;" class="noRecordsArea">No Records Found</div>
                        </div>
                    </div>
                </div>
            </div>   
            <a class="commonBtn bgGreen addBtn" id="areabtn" onclick="save_allocation()">Send Email</a>
        </div>
        <!--/////////////////////////////////////end area div////////////////////////////////////-->


        <!--//////////////////////////////////////branchdiv//////////////////////////////////////////////////////-->
        <div class="allocateBottom" id="branchdiv">
            <div class="commonDragHolderV1">
                <h3>Resource <span>Allocation</span></h3>
                <div class="dragContainer">

                    <div class="dragContent assignHolder">
                        <p>Selected Supervisors</p>
                        <div class="dragnDroper dropme branchsupervisor" id="branchsupervisor">

                        </div>
                        <div class="commonLoaderV1"></div>
                    </div>

                    <div class="dragBtnCtrl">
                        <ul>
                            <li><a href="javascript:void(0)" class="btnLeft" title="Add"></a></li>
                            <li><a href="javascript:void(0)" class="btnRight" title="Remove"></a></li>
                        </ul>
                    </div>
                    <div class="dragContent assignHolder">
                        <p>All Supervisors</p>
                        <div class="dragnDroper">
                            <input type="text" placeholder="Search" id="supfilter">
                            <div class="dropme allow1 ui-sortable allemps" id="destination">

                            </div>
                            <div style="display: none;padding-left: 15px;" class="noRecordsSup">No Records Found</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--<div class="tabHolderV2 count1">
                <div class="tabBtnV2 themeV1">
                    <ul class="shift" id="shiftid">
                    </ul>
                </div>
                <div class="tabContents">
                    <div class="tabList" id="tabS">
                        <div class="dragContainer">

                            <div class="dragContent ">
                                <p class="cashier_shift"></p>
                                <div class="dragnDroper dropme allocated_cashiers" >

                                </div>
                            </div>

                            <div class="dragBtnCtrl">
                                <ul>
                                    <li><a href="javascript:void(0)" class="btnLeft" title="Add"></a></li>
                                    <li><a href="javascript:void(0)" class="btnRight" title="Remove"></a></li>
                                </ul>
                            </div>
                            <div class="dragContent">
                                <p>All Cashiers</p>
                                <div class="dragnDroper">
                                    <input type="text" placeholder="Search" class="cashfilter">
                                    <div class="dropme allow1 ui-sortable allcashiers deallocated_cashiers" >

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dragContainer">

                            <div class="dragContent ">
                                <p class="barista_shift"></p>
                                <div class="dragnDroper dropme allocated_baristas" >

                                </div>
                            </div>

                            <div class="dragBtnCtrl">
                                <ul>
                                    <li><a href="javascript:void(0)" class="btnLeft" title="Add"></a></li>
                                    <li><a href="javascript:void(0)" class="btnRight" title="Remove"></a></li>
                                </ul>
                            </div>
                            <div class="dragContent">
                                <p>All Baristas</p>
                                <div class="dragnDroper">
                                    <input type="text" placeholder="Search" class="barfilter">
                                    <div class="dropme  ui-sortable allbaristas deallocated_baristas" >

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 

                </div>
            </div>-->
            <a class="commonBtn bgGreen addBtn" id="branchbtn" onclick="save_allocation()">Send Email</a>
        </div>
        
        
        
        <div class="allocateBottom" id="branch_barista_div">
            <div class="commonDragHolderV1">
                <h3>Resource <span>Allocation</span></h3>
                <div class="dragContainer">

                    <div class="dragContent assignHolder">
                        <p>Selected Baristas</p>
                        <div class="dragnDroper dropme allocated_baristas" id="branch_baristas">

                        </div>
                        <div class="commonLoaderV1"></div>
                    </div>

                    <div class="dragBtnCtrl">
                        <ul>
                            <li><a href="javascript:void(0)" class="btnLeft" title="Add"></a></li>
                            <li><a href="javascript:void(0)" class="btnRight" title="Remove"></a></li>
                        </ul>
                    </div>
                    <div class="dragContent assignHolder">
                        <p>All Baristas</p>
                        <div class="dragnDroper">
                            <input type="text" placeholder="Search" id="barfilter">
                            <div class="dropme allow1 ui-sortable allbaristas deallocated_baristas">

                            </div>
                            <div style="display: none;padding-left: 15px;" class="noRecordsBar">No Records Found</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <a class="commonBtn bgGreen addBtn" id="branchbtn" onclick="save_allocation()">Send Email</a>
        </div>
        
        <div class="allocateBottom" id="branch_cashier_div">
            <div class="commonDragHolderV1">
                <h3>Resource <span>Allocation</span></h3>
                <div class="dragContainer">

                    <div class="dragContent assignHolder">
                        <p>Selected Cashiers</p>
                        <div class="dragnDroper dropme allocated_cashiers">

                        </div>
                        <div class="commonLoaderV1"></div>
                    </div>

                    <div class="dragBtnCtrl">
                        <ul>
                            <li><a href="javascript:void(0)" class="btnLeft" title="Add"></a></li>
                            <li><a href="javascript:void(0)" class="btnRight" title="Remove"></a></li>
                        </ul>
                    </div>
                    <div class="dragContent assignHolder">
                        <p>All Cashiers</p>
                        <div class="dragnDroper">
                            <input type="text" placeholder="Search" id="cashfilter">
                            <div class="dropme allow1 ui-sortable allcashiers deallocated_cashiers">

                            </div>
                            <div style="display: none;padding-left: 15px;" class="noRecordsCash">No Records Found</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <a class="commonBtn bgGreen addBtn" id="branchbtn" onclick="save_allocation()">Send Email</a>
        </div>


        <!--/////////////////////////////////////end branch div////////////////////////////////////-->
    </div>

</div>
<div class="modalHolderV1">
    <a href="javascript:void(0)" title="Close" class="btmModalClose" onclick="close_modal()">X</a>
    <div class="modalScroll modalScroll_area">
    </div>
    <div class="modalScroll modalScroll_region">
    </div>
</div>

<div class="customClear"></div>
</div>

<input type="hidden"  id="edit_from_date" value="">
<input type="hidden"  id="edit_to_date" value="">
</div>
<script>
    $(document).on('click', '.bgBlue', function () {
        if ($(this).parent().parent().parent().hasClass('selected')) {
            $('.empAllocations tr').removeClass('selected');
        } else {
            $('.empAllocations tr').removeClass('selected');
            $(this).parent().parent().parent().toggleClass('selected');
        }
    });
    $(document).on('focus', '.edit_from_date', function () {
        $(this).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function (selected) {
                var dt = new Date(selected);
                dt.setDate(dt.getDate() + 1);
                $(".edit_to_date").datepicker("option", "minDate", dt);
                $('#edit_from_date').val($(this).val());
            }
        });
    });
    $(document).on('focus', '.edit_to_date', function () {
        $(this).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function (selected) {
                var dt = new Date(selected);
                dt.setDate(dt.getDate() + 1);
                $(".edit_from_date").datepicker("option", "minDate", dt);
                $('#edit_to_date').val($(this).val());
            }
        });
    });
    $("#from_date").datepicker({
        changeMonth: true,
        changeYear: true,
        minDate: '+0D',
        dateFormat: 'dd-mm-yy',
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() + 1);
            $("#to_date").datepicker("option", "minDate", selected);
            if ($('.resource_category').val() == 'region')
                    {

                        $("#regiondiv").show();
                        show_regional_managers();
                        show_allocated_regional_managers();
                    }
                    if ($('.resource_category').val() == 'area')
                    {
                        $("#areadiv").show();
                        show_area_managers();
                        show_allocated_area_managers();
                    }
                    /*if ($('.resource_category').val() == 'branch')
                    {
                        $("#branchdiv").show();
                        show_allocated_supervisors();
                        show_supervisors();
                        show_cashiers();
                        show_baristas();
                        show_shifts();
                        $('.tabContents').hide();

                    }*/
        if ($('.resource_category').val() == 'branch'){
                    if ($('#job_position').val() == 'Supervisor')
                    {
                          $("#branchdiv").show();
                          show_allocated_supervisors();
                          show_supervisors();

                    }
                    if ($('#job_position').val() == 'Barista')
                    {
                        
                          $("#branch_barista_div").show();
                          show_allocated_baristas();
                          show_baristas();
                    }
                    if ($('#job_position').val() == 'Cashier')
                    {
                          $("#branch_cashier_div").show();
                          show_allocated_cashiers();
                          show_cashiers();

                    }
        }
        }
    });
    $("#to_date").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            //$("#from_date").datepicker("option", "maxDate", selected);
            if ($('.resource_category').val() == 'region')
                    {

                        $("#regiondiv").show();
                        show_regional_managers();
                        show_allocated_regional_managers();
                    }
                    if ($('.resource_category').val() == 'area')
                    {
                        $("#areadiv").show();
                        show_area_managers();
                        show_allocated_area_managers();
                    }
                    /*if ($('.resource_category').val() == 'branch')
                    {
                        $("#branchdiv").show();
                        show_allocated_supervisors();
                        show_supervisors();
                        show_cashiers();
                        show_baristas();
                        show_shifts();
                        $('.tabContents').hide();

                    }*/
        if ($('.resource_category').val() == 'branch'){
                    if ($('#job_position').val() == 'Supervisor')
                    {
                          $("#branchdiv").show();
                          show_allocated_supervisors();
                          show_supervisors();

                    }
                    if ($('#job_position').val() == 'Barista')
                    {
                          $("#branch_barista_div").show();
                          show_allocated_baristas();
                          show_baristas();

                    }
                    
                    if ($('#job_position').val() == 'Cashier')
                    {
                          $("#branch_cashier_div").show();
                          show_allocated_cashiers();
                          show_cashiers();

                    }
        }
        }
    });
    
    $('#region').on('click', function () {
        $("#areadiv").hide();
        $("#branchdiv").hide();
        $("#branch_barista_div").hide();
        $("#branch_cashier_div").hide();
    });
    
    $('#area').on('click', function () {
        $("#regiondiv").hide();
        $("#branchdiv").hide();
         $("#branch_barista_div").hide();
        $("#branch_cashier_div").hide();
    });
    
    $('#branch').on('click', function () {
        $("#regiondiv").hide();
        $("#areadiv").hide();
        $("#branch_barista_div").hide();
        $("#branch_cashier_div").hide();
    });
    
</script>
<div class="allocations"></div>
@endsection
