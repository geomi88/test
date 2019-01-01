var customJS;

jQuery(document).ready(function($) {


    customJS = {

        common: {
            commonJS: function() {

                var currentScrollPos;
                $(".iconMobNav").on('click', function() {
                    if ($(this).hasClass('activeMenu')) {
                        $(this).removeClass('activeMenu');
                        $(".mobNav").removeClass('menuShow');

                        $('html,body').stop().animate({ scrollTop: currentScrollPos }, { queue: false, duration: 1000 });
                    } else {
                        $(this).addClass('activeMenu');
                        currentScrollPos = $(window).scrollTop();
                        $(".mobNav").addClass('menuShow');
                        $('html,body').stop().animate({ scrollTop: 0 }, { queue: false, duration: 1000 });
                    }
                });

                $('body').on('click', '.btnUpload', function() {
                    $(this).parent().find('.btnCommonUplod').trigger('click');
                });

                $('.btnCommonUplod').change(function(e) {
                    var fileName = e.target.files[0].name;
                    $('.fileValue').html(fileName);
                });

                // $('.commonBtn').on('click', function() {
                //     $('#container').stop().animate({ scrollTop: 0 }, { queue: false, duration: 1000 });
                // });



                //----------------mobile Nav--------------

                var newWidth = $('.contentRight').width();
                $('body').on('click', '.iconMobNav', function() {
                    $('.contentRight').toggleClass('leftAnimo');
                });

                if ($(window).width() > 1024) {
                    $('.contentRight').removeClass('leftAnimo');
                }


                $('body').on('click', '.iconMobNav', function() {
                    $(this).toggleClass('open');
                });

                $('body').on('click', '.btnModal', function() {
                    $('.overlay').fadeIn('slow');
                    $('.imgPopup').fadeIn('slow');
                });

                $('body').on('click', '.imgPopup', function() {
                    $(this).fadeOut(250);
                    $('.overlay').fadeOut(250);
                });

                $('#cashierPay').prop('checked', true);
                /*$('.formListV1Dtl .commonBtn').click(function () {
                	var formStep = $(this).attr('rel');
                	$('.formListV1Dtl').hide();
                	$(formStep).show();
                });*/

                $('.tabBtnV2 a').click(function() {
                    var tabCall = $(this).attr('data-rel');
                    $('.count1 .tabList').hide();
                    $('.tabBtnV2 li').removeClass('selected');
                    $(this).parent().addClass('selected');
                    $(tabCall).show();
                });

                $('.cashPay input[type="radio"]').click(function() {
                    var payType = $(this).attr('id');
                    $('.cashPayContent > div').hide();
                    $('.' + payType).show();
                });

                $('.unitOption').click(function() {
                    $('.unitModal').show();
                    $('.commonLoaderV1').show();
                });

                $('.unitModal .btnClose').click(function() {
                    $('.unitModal').hide();
                    $('.commonLoaderV1').hide();
                });

                $('.unitOption').click(function() {
                    $(this).closest('.privilegeHolder').toggleClass('selected');
                });

                $(".allow1").on("sortremove", function(event, ui) {
                    ui.item.prependTo(ui.item.parent());
                    $.each(ui.item.parent().children(), function(index, item) {
                        if (index > 0) {
                            $(this).appendTo($(".allow1"));

                        }
                    });
                });

                $('.dropme').sortable({
                    connectWith: '.dropme',
                    start: function(event, ui) {
                        $(this).addClass('dragged');
                        $(this).find('.empList').addClass('nodragorsort');
                    },
                    stop: function() {
                        $(this).removeClass('dragged');
                    }
                });

                $('.dropme1').sortable({
                    connectWith: '.dropme1',
                    start: function(event, ui) {
                        $(this).addClass('dragged');
                    },
                    stop: function() {
                        $(this).removeClass('dragged');
                    }
                });

                $('.dropme2').sortable({
                    connectWith: '.dropme2',
                    start: function(event, ui) {
                        $(this).addClass('dragged');
                    },
                    stop: function() {
                        $(this).removeClass('dragged');
                    }
                });

                $('.dropme3').sortable({
                    connectWith: '.dropme3',
                    start: function(event, ui) {
                        $(this).addClass('dragged');
                    },
                    stop: function() {
                        $(this).removeClass('dragged');
                    }
                });

                $('.dropme4').sortable({
                    connectWith: '.dropme4',
                    start: function(event, ui) {
                        $(this).addClass('dragged');
                    },
                    stop: function() {
                        $(this).removeClass('dragged');
                    }
                });

//                $('.clsMenuOrder').sortable({
//                });

                $('.assignHolder .dragnDroper').sortable({
                    items: '> :not(.nodragorsort)'
                });

                $('.listSelectAll input, .alltSelected').prop('checked', false);

                $('body').on('click', '.listSelectAll input', function() {
                    if ($(this).is(':checked')) {
                        $(this).parents(':eq(4)').find('.alltSelected').prop('checked', true);
                    } else {
                        $(this).parents(':eq(4)').find('.alltSelected').prop('checked', false);
                    }
                });

                $('.innerNavV1 span').on('click', function() {
                    if ($(this).parent().hasClass('layer1')) {
                        $(this).parent().removeClass('layer1');
                        $(this).parent().find('ul').slideUp(200);
                        // $(this).parent().find('> span').html('+');
                    } else {
                        $(this).parent().toggleClass('layer1');
                        $('.layer1 > ul').slideDown(150);
                        // $('.layer1 > span').html('-');
                    }
                });

                $('body').on('click', '.innerNavV1 a', function() {
                    
                    $('.innerNavV1 a').removeClass('selected');
                    $(this).parent().find('> a').addClass('selected');
                    $("#txtcategoryname").val($(this).text());
                    $("#inventory_category_id").val($(this).attr("id"));
                    $(".commonModalHolder").hide();
                });

                $('body').on('click', '.allocationBtnHolder a', function() {
                    var dataSource = $(this).parent().find('.toolTipV1').html();
                    $('.allocations').html('');
                    $('body').addClass('allocSelected');
                    $('.allocations').append('<div class="toolTipV1">' + dataSource + '</div>');
                });

                $('body').on('click', '.addNewReq.updatedData .reqTop a', function() {
                    $('.addNewReq.updatedData .reqTop a').text('+');
                    $(this).parent().parent().toggleClass('selected');
                    $('.addNewReq.updatedData.selected .reqTop a').text('-');
                });

                $('body').on('click', '.add_product', function() {
                    $('.latestAddForm .addNewReq').clone().appendTo('.latestAddedList');
                    $('.latestAddedList .addNewReq').removeClass('newEntry');
                    $('.latestAddedList .addNewReq').addClass('updatedData');
                    $('.latestAddForm input').val('');
                    $('.addNewReq.newEntry option').attr('selected', false);
                    $('.addNewReq.newEntry option:first-child').attr('selected', true);

                });

                $('body').on('click', '.addNewReq.newEntry option', function() {
                    $('.addNewReq.newEntry option').attr('selected', false);
                    $(this).attr('selected', true);
                    $(this).parent().val($(this).val());
                });


                $('body').on('click', '.toolTipV1 h3 a', function() {
                    $('body').removeClass('allocSelected');
                });


                $('.privilegeTop .btnOpen').click(function() {
                    $(this).closest('.privilegeHolder').toggleClass('selected');
                });

                $('body').on('click', '.privilegeTop input', function() {
                    if ($(this).is(':checked')) {
                        $(this).parents(':eq(3)').find('.privilegeCont input').prop('checked', true);
                    } else {
                        $(this).parents(':eq(3)').find('.privilegeCont input').prop('checked', false);
                    }
                });

                $('.actionReq').on('click', function() {
                    var reqId = $(this).attr('data-rel');
                    $('.reason').hide();
                    $(this).parents(':eq(1)').find(reqId).show();
                });

                //	                $('.headerSection li.notification').on('click', function() {
                //	                    $(this).toggleClass('selected');
                //	                });

                $('.empAllocations .btnHolderV1 .bgBlue').on('click', function() {
                    if ($(this).parent().parent().parent().hasClass('selected')) {
                        $('.empAllocations tr').removeClass('selected');
                    } else {
                        $('.empAllocations tr').removeClass('selected');
                        $(this).parent().parent().parent().toggleClass('selected');
                    }
                });


                var winHeight = $(window).height();
                $('.contentLeft').css('min-height', winHeight);

                $('.memberList .btnViewDtls').on('click', function() {
                    $(this).parent().addClass('selectedList');
                    $('.overlay, .branchModal').show();
                });

                $('.branchModal .btmModalClose').on('click', function() {
                    $('.memberList').removeClass('selectedList');
                    $('.overlay, .branchModal').hide();
                    $('.branchModal input[type="checkbox"').attr('checked', false);
                });

                $('.branchBtns.phone').on('click', function() {
                    $('.modalHolderV2.phone, .overlay').show();
                });

                $('.branchBtns.chat').on('click', function() {
                    $('.modalHolderV2.chat, .overlay').show();
                });

                $('.contactPersonList > a').on('click', function() {
                    $('.contactPersonList').hide();
                });

                var graphHeight = $('.graphSingle').height();
                $('.allSalesReport').height(graphHeight);

                // ----------------------------- Budget Creation -----------------------------------


                $('.checkDropdownWrapper input[type="radio"]').click(function () {
                    $('.checkDDLHolder').show();
                    if ($(this).attr('id') == 'topManager') {
                        $('.approveBtnHolder').show();
                        $('.ddlTableListHolder').hide();
                    }
                    if ($(this).attr('id') == 'otherEmployees') {
                        $('.ddlTableListHolder').show();
                        $('.approveBtnHolder').hide();

                    }
                });

                $('.tabWrapper li:eq(0)').addClass('selected');
                $('.tabContent:eq(0)').show(); $('body').on('click', '.tabWrapper li', function () {
                    $('.tabWrapper li').removeClass('selected');
                    $(this).addClass('selected');
                    var getRel = $(this).attr('rel');
                    //var ConHeight = $('#' + getRel).height() + 80; $('.tabDtls').animate({ height: ConHeight });
                    $('.tabContent').hide(); $('#' + getRel).fadeIn(600);
                });

                $('body').on('click', '.btnTab', function () {
                    $('.tabContent').hide(); $(this).next('.tabContent').slideDown(400).siblings('.tabContent').slideUp(400);
                });


                $('body').on('click', '.iconInfoWrapper .btnTooltip' , function () {
                    $('.tooltipInfo').hide();
                    $(this).closest('.iconInfoWrapper').find('.tooltipInfo').show();
                });

                $('body').on('click', '.iconInfoWrapper .infoClose', function () {
                     $(this).closest('.iconInfoWrapper').find('.tooltipInfo').hide();
                });

                $('.fileUploadHolder .btnFileUpload').on('click',function(){
                    $('.fileUploadHolder .inputfileUpload').trigger('click');
                });


                // Accordion

				$('body').on('click','.accdnTitle span', function(){
					$(this).closest('.accdnTitle').next('.accdnContent').slideToggle();
					$(this).closest('.accordion').toggleClass('active');
					$(this).closest('.accordion').siblings().find('.accdnContent').slideUp();
					$(this).closest('.accordion').siblings().removeClass('active');
				});
                 // search selectbox
                 
                 $(".chosen-select").chosen();
                 
                 $.validator.setDefaults({ ignore: ":hidden:not(.clsparentdiv:visible .chosen-select)" }) ;
                 $('body').on('change', '.chosen-select', function () {
                    if($(this).val()!=''){
                        $(this).removeClass("valErrorV1");
                        var id=$(this).attr("id")+"_chosen";
                        $("#"+id).find('.chosen-single').removeClass('chosen_error');
                    }
                });
            },

            html5Tags: function() {
                document.createElement('header');
                document.createElement('section');
                document.createElement('nav');
                document.createElement('footer');
                document.createElement('menu');
                document.createElement('hgroup');
                document.createElement('article');
                document.createElement('aside');
                document.createElement('details');
                document.createElement('figure');
                document.createElement('time');
                document.createElement('mark');
            },

            commonInput: function() {


                function jqContact() {
                    $('.getOverlay').fadeIn();
                    var $form = $('#UpdateForm'); // set your form ID
                    jQuery(document).ready(function($) {
                        var formData = new FormData($('form#UpdateForm')[0]);
                        $.ajax({
                            type: 'POST',
                            url: $form.attr('action'),
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function(msg) {
                                submitcount43122 = 0;
                                window.location.reload();
                            },
                            error: function(msg) {
                                alert('Update error.');
                                submitcount43122 = 0;
                                $('.getOverlay').fadeOut();
                            }
                        });
                    });

                }

                var $inputText = $('.queryInput input, .queryInput textarea');
                $inputText.each(function() {
                    var $thisHH = $(this);
                    if (!$(this).val()) {
                        $(this).parent().find('label').show();
                    } else {
                        setTimeout(function() {
                            $thisHH.parent().find('label').hide();
                        }, 100);
                    }

                });
                $inputText.focus(function() {
                    if (!$(this).val()) {
                        $(this).parent().find('label').addClass('showLab');
                    }
                });
                $inputText.keydown(function() {
                    if (!$(this).val()) {
                        $(this).parent().find('label').hide();
                    }
                });
                $inputText.on("blur", function() {
                    var $thisH = $(this);
                    if (!$(this).val()) {
                        $(this).parent().find('label').show().removeClass('showLab');
                    } else {
                        $thisH.parent().find('label').hide();
                    }

                });


                $("select").trigger("click");

                function commonSelect() {
                    
                    var $selectText = $('.bgSelect input');
                    var $selectLi = $('.bgSelect li');
                    
                    var selectval;
                    var Drop = 0;

                    $('body').click(function() {
                        if (Drop == 1) {
                            $('.bgSelect ul').hide();
                            Drop = 0;
                        }
                    });
                    $selectText.click(function() {
                        $('.bgSelect ul').hide();
                        Drop = 0;
                        if (Drop == 0) {
                            $(this).parent().find('ul').slideDown();
                        }
                        setTimeout(function() {
                            Drop = 1;
                        }, 50);


                    });

                    $selectLi.click(function() {
                        Drop = 1;
                        selectval = $(this).text();
                        
                        if ($(this).parent().parent().parent().find('.suggestionDroplist').length > 0) {
//                            $(this).parent().parent().parent().find('input').val(selectval);
                           $('#top_manager_id').val(selectval);
                           
                            $(this).parent().parent().parent().find('.commonError').hide();
                        } else {
//                            $(this).parent().parent().find('input').val(selectval);
                            $('#top_manager_id').val(selectval);
                           
                            $(this).parent().parent().find('.commonError').hide();
                        }

                    });
                }

                commonSelect();

                var getUrlPath = location;

                $('.mainMenu li a').each(function() {
                    if ($(this).attr('href') == getUrlPath) {
                        $(this).parent().addClass('selected');
                    }
                });



            }

        } //end commonJS

    };


    customJS.common.commonJS();
    customJS.common.html5Tags();
    customJS.common.commonInput();
    
    // changes done by ajith for dashboard
    $('body').on('click','.iconTab',function(){
        $(this).closest('.TitleWrapper').next('.history').slideToggle();
        $(this).toggleClass('active');
        $(this).closest('.tasklist').siblings().find('.history').slideUp();
        $(this).closest('.tasklist').siblings().find('.iconTab').removeClass('active');
    });

    $('.tasklist').find('.iconDescription').bind('mousemove',function(event) { 
        var contHeight =  $(this).closest('.tasklist').find('.toolDescription').height()+20;
        var left = event.pageX - $(this).offset().left;
        var top = event.pageY - $(this).offset().top;
        $(this).closest('.tasklist').find('.toolDescription').css({top: top-contHeight,left: left-15}).show();
    });

    $('.tasklist').find('.iconDescription').mouseout(function() {
        $('.toolDescription').hide();
    });
    
    function eqHeight(){
        var highestBox = 0;
        $('.taskContent').each(function() {
             if($(this).outerHeight() > highestBox) 
                highestBox = $(this).outerHeight(); 
         }); 

        $('.taskContent').each(function() {
        $(this).css('min-height',highestBox);
      });
    }

    setTimeout(function() {
        eqHeight();
    }, 500);

    $(window).resize(function(){
        eqHeight();
    });
                
    $('.toolDescription p:empty').parent().remove();
    
    $('body').on('click', '.fc-button-group .fc-button.fc-state-active', function () {
        if ($(this).hasClass('fc-month-button')) {
            $('.panel-heading').addClass('btnMonthBlue');
            $('.panel-primary').addClass('btnMonthBlue');
            $('.panel-primary .fc-icon').addClass('btnMonthBlue');

        }
        else {
            $('.panel-heading').removeClass('btnMonthBlue');
            $('.panel-primary').removeClass('btnMonthBlue');
            $('.panel-primary .fc-icon').removeClass('btnMonthBlue');
        }

        if ($(this).hasClass('fc-agendaWeek-button')) {
            $('.panel-heading').addClass('btnWeekBlue');
            $('.panel-primary').addClass('btnWeekBlue');
            $('.panel-primary .fc-icon').addClass('btnWeekBlue');
        }
        else {
            $('.panel-heading').removeClass('btnWeekBlue');
            $('.panel-primary').removeClass('btnWeekBlue');
            $('.panel-primary .fc-icon').removeClass('btnWeekBlue');
        }

        if ($(this).hasClass('fc-agendaDay-button')) {
            $('.panel-heading').addClass('btnDayBrown');
            $('.panel-primary').addClass('btnDayBrown');
            $('.panel-primary .fc-icon').addClass('btnDayBrown');
        }
        else {
            $('.panel-heading').removeClass('btnDayBrown');
            $('.panel-primary').removeClass('btnDayBrown');
            $('.panel-primary .fc-icon').removeClass('btnDayBrown');
        }
    });



  /*  $(".toggleOptn h5").on('click', function() {
        $('.toggleOptn .toggleOptnDtl').slideUp();
        $('.toggleOptn').removeClass('selected');
        $(this).parent().addClass('selected');
        $(this).parent().find('.toggleOptnDtl').stop(true,true).slideToggle();
    });
*/
   


    $(".toggleOptn h5").on('click', function() {
            $('.toggleOptn .toggleOptnDtl').slideUp();
            $('.toggleOptn').removeClass('selected');
            $(this).parent().addClass('selected');
            $(this).parent().find('.toggleOptnDtl').stop(true,true).slideToggle();
    });


    $('body').on('click', '.question .topSection', function () {
        $('.question .btmSection').slideUp();
        $('.question').removeClass('selected');
        $(this).parent().addClass('selected');
        $(this).parent().find('.btmSection').stop(true,true).slideToggle();
    });

   
    $('body').on('click', '.PrevSettings > span', function () {
        $('.privilegeCont .commonCheckHolder').removeClass('toolTipCall');
        $(this).parent().parent().addClass('toolTipCall');
    });

    $('body').on('click', '.commonCheckHolder .btnCloseV2, .commonCheckHolder .btnSavePositions', function () {
		
        $(this).parent().parent().parent().removeClass('toolTipCall');
    });
    
    $('body').on('keyup', '.arabicalign', function() {
        var arabic = /[\u0600-\u06FF]/;
        var string = $(this).val(); 

        if(arabic.test(string)){
            $(this).css("text-align", "right"); 
        }else{
            $(this).css("text-align", "left"); 
        }
    });

   
    var repeat = $('.repeatOptn').val();
    $('#'+repeat).show();
    $('.repeatOptn').change(function () {
        var repeat = $(this).val();
        $('.repeatFields').hide();
        $('#'+repeat).show();
    });

    var empType = $('.empType:checked').val();
    $('#'+empType).show();
    $('.empType').change(function () {
        $('.empTypeDtl').hide();
        if($('.empType').is(':checked')) {
            var empType = $(this).val();
            $('#'+empType).show();
        }
        
    });

     $('.flexslider').flexslider({
      animation: "slide",
      animationLoop: false,
      itemWidth: 89,
      smoothHeight: true,
      controlNav: false
    });
    
    $('body').on('click', '.btnAction.print', function() {
        $('.printChoose').toggle();
        //$('body').toggleClass('printOptns');       
    });
    
    $('body').on('click', '.expandIcon a', function() {
        if ($(this).parent().hasClass('rotateIcon')) {
            $(this).parent().removeClass('rotateIcon');
            $(this).html('+');
        } else {
            $(this).parent().addClass('rotateIcon');
            $(this).html('-');
        }
    });

});
