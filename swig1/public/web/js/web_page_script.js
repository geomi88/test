//This file include the functions related to website,
// please update js  from here only
var pageJs;

$(document).ready(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    pageJs = {
        webpage: {
            bindDefaults: function () {
                $('body').on('click', '.tabMenu li', function () {
                    var p_type = $(this).attr('id');
                    var tabDiv = $(this).attr('data-tab');
                    if (typeof (p_type) == 'undefined' || p_type=="") {
                        return false;
                    } 
                    var ajax_fn = null;
                    var dataPost = {ptype: p_type};
                    var ajax_fn = $.ajax({
                        type: 'POST',
                        url: 'web/load_properties',
                        cache: false,
                        datatype: 'json',
                        data: dataPost,
                        beforeSend: function () {
                            if (ajax_fn) {
                                ajax_fn.abort();
                            }
                        },
                        success: function (response) {

                            if (typeof (response) == 'object') {
                                var re_error = response.error;
                                if (re_error) {
                                    alert("Server error");
                                } else {
                                    $('.tabContent').html("");
                                    $('#' + tabDiv).html(response.html);
                                }

                            }
                            ajax_fn = null;
                        },
                        error: function (msg) {
                            ajax_fn = null;
                        }
                    });
                });
            }

        }//end commonJS

    };

    pageJs.webpage.bindDefaults();


});

function startLoader(){
    console.log("loading");
}
function stopLoader(){
    console.log("Stopped loading");
}