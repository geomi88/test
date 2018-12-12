@extends('layouts.main')
@section('content')
<script>
   
    $(window).on('hashchange', function () {
        if (window.location.hash) {
            var page = window.location.hash.replace('#', '');
            if (page == Number.NaN || page <= 0) {
                return false;
            } else {
                getData(page);
            }
        }
    });
    $(document).ready(function ()
    {
        $(document).on('click', '.pagination a', function (event)
        {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            //var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });


        

    });
    
    function getData(page) {

        
        
        var strids='';
        if(arrUserList.length>0){
            for(var i=0;i<arrUserList.length;i++){
                strids=strids+arrUserList[i].user_id+',';
            }
        }
        
        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {strids:strids},

        })
        .done(function (data)
        {

            $(".users_list").empty().html(data);
            location.hash = page;
        })
        .fail(function (jqXHR, ajaxOptions, thrownError)
        {
            alert('No response from server');
        });
    }



</script>
<div class="contentHolderV1">
    <h2>Notification</h2>
    <div class="formSection bothUsers">
        <input type="checkbox" class="chkbothusers">
    <label>All users</label>
    </div>
    <div class="formSection allUsers">
        <input type="checkbox" class="chkallusers">
    <label>Registered users</label>
    </div>
    <div class="formSection guestUsers">
        <input type="checkbox" class="chkguestusers">
    <label>Guest users</label>
    </div>
    <div class="notificationSelect">
        <div class="leftSide">
            <h2>Users List</h2>
            <div class="users_list">
            @include('users/user_result')
            </div>
        </div>
        <div class="rightSide" id="selecteduserslist">
            <h2>Selected Users</h2>
            <ul> </ul>
        </div>

    </div>
    <form method="post" action="{{ action('Users\UsersController@sendnotification') }}" id="sendNotificationform" enctype="multipart/form-data" class="formTypeV1">
        {{ csrf_field() }}
        <input type="hidden" id="user_ids" name="user_ids" value="">
        <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
        <input type="hidden" id="allusersstatus" name="allusersstatus" value="0">
        <input type="hidden" id="guestusersstatus" name="guestusersstatus" value="0">
        <input type="hidden" id="bothusersstatus" name="bothusersstatus" value="0">
        <div class="formSection">

            <label>Message</label>
            <textarea name="message" id="message" required></textarea>
            <div class="customClear"></div>
            <div class="messageError error"></div>

        </div>
        <input class="commonButton" type="submit" value="SEND">  
    </form>
</div>


<script>
    var arrUserList = [];
    var arruser=[];
    if (arruser.length > 0) {
        arrUserList = arruser;
        showselectedusers();
    }
    else
    {
        $("#selecteduserslist ul").html('<li>No Users Selected</li>');
    }
    $(document).ready(function () {

        $('body').on('click', '.chkuser', function () {
            if ($(this).is(":checked")) {
                addtolist($(this).attr('id'));
            } else {
                remove($(this).attr('id'));
            }


        })
        $('body').on('click', '.chkallusers', function () {
            if ($(this).is(":checked")) {
                $("#allusersstatus").val(1);
                $('.chkguestusers').prop('checked', false);
                $('.chkbothusers').prop('checked', false);
                $("#guestusersstatus").val(0);
                $("#bothusersstatus").val(0);
                $('.notificationSelect').hide();
            } else {
                $("#allusersstatus").val(0);
                $('.notificationSelect').show();
            }


        })
        $('body').on('click', '.chkguestusers', function () {
            if ($(this).is(":checked")) {
                $("#guestusersstatus").val(1);
                $('.chkallusers').prop('checked', false);
                $('.chkbothusers').prop('checked', false);
                $("#allusersstatus").val(0);
                $("#bothusersstatus").val(0);
                $('.notificationSelect').hide();
            } else {
                $("#guestusersstatus").val(0);
                $('.notificationSelect').show();
            }


        })
        $('body').on('click', '.chkbothusers', function () {
            if ($(this).is(":checked")) {
                $("#bothusersstatus").val(1);
                $('.chkguestusers').prop('checked', false);
                $('.chkallusers').prop('checked', false);
                $("#guestusersstatus").val(0);
                $("#allusersstatus").val(0);
                $('.notificationSelect').hide();
            } else {
                $("#bothusersstatus").val(0);
                $('.notificationSelect').show();
            }


        })
        $('#sendNotificationform').submit(function (e) {
            if ($('.chkallusers').is(":checked")) {
                return true;
            }
            if ($('.chkguestusers').is(":checked")) {
                return true;
            }
            if ($('.chkbothusers').is(":checked")) {
                return true;
            }
            if (arrUserList.length > 0) {
                
                    var arruser = JSON.stringify(arrUserList);
                    $("#user_ids").val(arruser);
                

            } else {
                alert("Please select atleast one user");
                return false;
            }
        });

    });




    function addtolist(userid) {

        var intItemDuplicate = 0;
        if (arrUserList.length > 0) {
            for (var i = 0; i < arrUserList.length; i++) {
                if (userid == arrUserList[i].user_id) {
                    intItemDuplicate = 1;
                }
            }
        }

        var arraData = {
            user_id: userid,
            user_name: $("#name_" + userid).val(),
            
        }

        if (intItemDuplicate != 1) {
            arrUserList.push(arraData);
            showselectedusers();
        }
    }

    function remove(userid) {
        for (var i = 0; i < arrUserList.length; i++) {
            if (userid == arrUserList[i].user_id) {
                arrUserList.splice(i, 1);
                $('#' + userid).attr('checked', false);
            }
        }
        showselectedusers();
    }

    function showselectedusers()
    {
        $("#selecteduserslist ul").html('<li>No Users Selected</li>');
        if (arrUserList.length > 0) {
            var strHtml = '';
            for (var i = 0; i < arrUserList.length; i++) {
                strHtml += '<ul><li><a href="javascript:remove(' + arrUserList[i].user_id + ')" class="btnRemove">X</a>' + arrUserList[i].user_name + '</li></ul>';
            }
            $("#selecteduserslist ul").html(strHtml);
        }
    }


</script>
@endsection