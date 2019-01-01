
var customJS;

jQuery(document).ready(function ($) {

    customJS = {

        common: {
            commonJS: function () {
                //----------------mobile Nav--------------
                var newWidth = $('#container').width();
                $('body').on('click', '.iconMobNav', function () {
                    $('#container').toggleClass('leftAnimo');
                });

                if ($(window).width() > 1024) {
                    $('#container').removeClass('leftAnimo');
                }


                $('body').on('click', '.iconMobNav', function () {
                    $(this).toggleClass('open');
                });
                //----chosen ----
                if ($(".chosen").length > 0) {
                    $(".chosen").chosen({
                        no_results_text: "Oops, nothing found!"
                    });
                }
                //-----------Mobile Scrolltop------
                var currentScrollPos;
                $(".iconMobNav").on('click', function () {
                    if ($(this).hasClass('activeMenu')) {
                        $(this).removeClass('activeMenu');
                        $(".mobNav").removeClass('menuShow');
                        $('html,body').stop().animate({scrollTop: currentScrollPos}, {queue: false, duration: 1000});
                    } else {
                        $(this).addClass('activeMenu');
                        currentScrollPos = $(window).scrollTop();
                        $(".mobNav").addClass('menuShow');
                        ;
                        $('html,body').stop().animate({scrollTop: 0}, {queue: false, duration: 1000});
                    }
                });
                //all listing change background image
                $(".allListingWrapper .allListBoxHolder").each(function() {
                    var imgPath = $(this).find(".listImgHolder img").attr('src');
                    $(this).find(".listImgHolder").css('background-image', 'url("'+ imgPath +'")');
                });
                //dropdownList  
                // $('.dropList > input').click(function () {
                //     $(this).parent('.dropList').find('ul:first').slideToggle();
                // });

                //  $(document, '.dropList input').on("click", function (event) {
                //     var $trigger = $(".dropList");
                //     if ($trigger !== event.target && !$trigger.has(event.target).length) {
                //         $(".dropList ul").slideUp();
                //     }
                // });

                   $('.dropList input').on('click',function(){
                     $('.dropList').removeClass('dropSelect');
                    $(this).closest('.dropList').addClass('dropSelect');
                    if($(this).closest('.dropList').hasClass('dropSelect')){
                         $(this).closest(".dropList.dropSelect").find('ul').slideDown();
                         $(this).closest(".dropList.dropSelect").siblings('.dropList').find('ul').hide();
                    }

                   })


                $('.dropList li').click(function () {
                    /*if($(this).hasClass('priceInputHolder')){
                        //alert()
                        $(this).unbind("click");
                    }
                    else{*/
                        var val = $(this).text();
                        var val_id = $(this).attr('data-rel');
                        if (typeof (val_id) == "undefined") {
                            val_id = "";
                        }
                        $(this).closest('.dropList').find('input').val(val);
                        $(this).closest('.dropList').find('input').attr('data-rel', val_id);
                        $(this).parent('ul:first').slideUp();
                   /* }*/
                    
                });
                $(document, '.dropdownContact >input').on("click", function (event) {
                    var $trigger = $(".dropdownContact, .dropdownContact input");
                    if ($trigger !== event.target && !$trigger.has(event.target).length) {
                        $(".dropdownContact ul").slideUp();
                    }
                });


                $(document, '.dropList>input').on("click", function (event) {
                    var $trigger = $(".dropList");
                    if ($trigger !== event.target && !$trigger.has(event.target).length) {
                        $(".dropList ul").slideUp();
                    }
                });
                /*-----------price range dropdown----------------*/

                $('.priceRangeDropdown > input').click(function(){
                    $(this).parent('.priceRangeDropdown').find('>ul').slideToggle();
                });

                $('.priceRangeDropdown .priceInputHolder .halfWidthHolder li').click(function () {
                    var val = $(this).text();
                    var val_id = $(this).attr('data-rel');
                    if (typeof (val_id) == "undefined") {
                        val_id = "";
                    }
                    $(this).closest('.priceRangeDropdown .priceInputHolder .halfWidthHolder').find('input').val(val);
                    $(this).closest('.priceRangeDropdown .priceInputHolder .halfWidthHolder').find('input').attr('data-rel', val_id);
                });

                $(document, '.priceRangeDropdown>input').on("click", function (event) {
                    var $trigger = $(".priceRangeDropdown");
                    if ($trigger !== event.target && !$trigger.has(event.target).length) {
                        $(".priceRangeDropdown ul").slideUp();
                    }
                });

                /*-----------end of price range dropdown----------------*/

                $('label input[type="checkbox"]').on('click', function () {
                    if ($(this).is(':checked') == true) {
                        $(this).closest('label').addClass('selected');
                    } else {
                        $(this).closest('label').removeClass('selected');
                    }
                });


                setTimeout(function () {
                    $('.tabContent').first().fadeIn(500);
                }, 300);
                $('body').on('click', '.tabMenu li', function () {
                    $('.tabMenu li').removeClass('selected');
                    $(this).addClass('selected');
                    var getData = $(this).attr('data-tab');
                    var getHeight = $('#' + getData).height();
                    $('.tabContent').hide();
                    $('#' + getData).fadeIn(500);
                });

                /*--------radio checkbox span trigger script----------*/
                $(document).on('click','.filter_checkbox li span', function(){
                    $(this).parent('li').find('input').trigger('click');
                });
                /*---------------scroll----------------*/
                if ($('.cityScroll').length > 0) {
                    var myScroll;
                    function loaded() {
                        myScroll = new IScroll('.cityScroll', {
                            scrollbars: true,
                            mouseWheel: true,
                            interactiveScrollbars: true,
                            shrinkScrollbars: 'scale',
                            fadeScrollbars: true
                        });
                    }

                    $(window).on('load',function(){
                      loaded ();
                  })
                }
                
                
				// $('#places_list').chosen();
                
                
                /*------------Number-----------------*/

                $(".selectInput").append('<div class="inc button">+</div><div class="dec button">-</div>');
                $(".button").on("click", function () {
                    var $button = $(this);
                    var oldValue = $button.parent().find("input").val();

                    if ($button.text() == "+") {
                        var newVal = parseFloat(oldValue) + 1;
                    } else {
                        // Don't allow decrementing below zero
                        if (oldValue > 0) {
                            var newVal = parseFloat(oldValue) - 1;
                        } else {
                            newVal = 0;
                        }
                    }

                    $button.parent().find("input").val(newVal);

                });

                /*-------------------mob tab----------------*/

                if ($(window).width() <= 800) {
                    $('.leftContent:eq(0)').find('.mobTabContent').show();
                    $('body').on('click', '.leftContent strong', function () {
                        $('.leftContent').find('.mobTabContent').slideUp(500);
                        $(this).closest('.leftContent').find('.mobTabContent').slideDown(500);
                        loaded();
                    });
                }

                /*-----------------Detail Tab------------------&*/


                $('.detailTabArea').first().fadeIn(500);
                $('body').on('click', '.tabHeading li', function () {
                    $('.tabHeading li').removeClass('selected');
                    $(this).addClass('selected');
                    var getData = $(this).attr('data-tab');
                    var getHeight = $('#' + getData).height();
                    $('.detailTabArea').hide();
                    $('#' + getData).fadeIn(500);
                });

                /*----------------Floor Plan-------------------*/
                if ($('.galleryWrapper').length > 0) {
                    var images = [{
                        small: 'images/1.jpg',
                        big: 'images/1_big.jpg'
                    }];

                    var curImageIdx = 1,
                    total = images.length;
                    var wrapper = $('.galleryOverlay'),
                    curSpan = wrapper.find('.current');
                    var viewer = ImageViewer(wrapper.find('.galleryWrapper'));

                    //display total count
                    wrapper.find('.total').html(total);

                    function showImage() {
                        var imgObj = images[curImageIdx - 1];
                        viewer.load(imgObj.small, imgObj.big);
                        curSpan.html(curImageIdx);
                    }
                }

                //initially show image

                
                $('.detailTabArea').first().fadeIn(500);
                $('body').on('click','.tabHeading li',function(){
                   $('.tabHeading li').removeClass('selected');
                   $(this).addClass('selected');
                   var getData = $(this).attr('data-tab');
                   var getHeight = $('#'+getData).height();
                   $('.detailTabArea').hide();
                   $('#'+getData).fadeIn(500);
               });
                
                /*----------------Floor Plan-------------------*/
                var images = [{
                   small : 'images/1.jpg',
                   big : 'images/1_big.jpg'
               }];
               
               var curImageIdx = 1,
               total = images.length;
               var wrapper = $('.galleryOverlay'),
               curSpan = wrapper.find('.current');
               var viewer = ImageViewer(wrapper.find('.galleryWrapper'));
               
				//display total count
				wrapper.find('.total').html(total);
                
				function showImage(){
					var imgObj = images[curImageIdx - 1];
					viewer.load(imgObj.small, imgObj.big);
					curSpan.html(curImageIdx);
				}
                
				//initially show image
             

                $('body').on('click', '.btnFloorPlan', function () {
                    $('.galleryOverlay').fadeToggle(100);
                    showImage();
                });

                $('body').on('click', '.btnClose', function () {
                    $('.btnFloorPlan').trigger('click');
                });

                /*---------------Gallery-----------------*/

                $('body').on('click', '.galleryRight li', function () {
                    $('.galleryContainer').fadeIn(500, function () {
                        lux();
                    });
                });

                $('body').on('click', '.btnGalClose', function () {
                    $('.galleryContainer').fadeOut(500);
                });
                /*constructio gallery*/
                $('body').on('click', '.galleryImageSlider div img', function () {
                    $('.galleryContainer').fadeIn(500, function () {
                        lux();
                    });
                });

                $('body').on('click', '.btnGalClose', function () {
                    $('.galleryContainer').fadeOut(500);
                });
            },

            html5Tags: function () {
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

            commonInput: function () {

                //ajax form submit with file upload
                /*function jqContact() {
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
                 success: function (msg) {
                 submitcount43122 = 0;
                 window.location.reload();
                 },
                 error: function (msg) {
                 alert('Update error.');
                 submitcount43122 = 0;
                 $('.getOverlay').fadeOut();
                 }
                 });
                 });
                 
             }*/

             var $inputText = $('.queryInput input, .queryInput textarea');
             $inputText.each(function () {
                var $thisHH = $(this);
                if (!$(this).val()) {
                    $(this).parent().find('label').show();
                } else {
                    setTimeout(function () {
                        $thisHH.parent().find('label').hide();
                    }, 100);
                }

            });
             $inputText.focus(function () {
                if (!$(this).val()) {
                    $(this).parent().find('label').addClass('showLab');
                }
            });
             $inputText.keydown(function () {
                if (!$(this).val()) {
                    $(this).parent().find('label').hide();
                }
            });
             $inputText.on("blur", function () {
                var $thisH = $(this);
                if (!$(this).val()) {
                    $(this).parent().find('label').show().removeClass('showLab');
                } else {
                    $thisH.parent().find('label').hide();
                }

            });

         }

        }//end commonJS

    };


    customJS.common.commonJS();
    customJS.common.html5Tags();
    customJS.common.commonInput();

});


// slick slider 

$(document).on('ready', function() {
   
  $(".home").slick({
    dots: false,
        // infinite: true,
        // variableWidth: true,
        slidesToShow: 1,
        slidesToScroll: 1
    });
  
  $('.gallery').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
    centerPadding: '40px',
    variableWidth:true
  });

  $(".variable").slick({
    dots: false,
        // infinite: true,
        // variableWidth: true,
        slidesToShow: 1,
        slidesToScroll: 1
    });

      jQuery('<div class="quantity-nav"><div class="quantity-button quantity-up">+</div><div class="quantity-button quantity-down">-</div></div>').insertAfter('.quantity input');
    jQuery('.quantity').each(function() {
      var spinner = jQuery(this),
        input = spinner.find('input[type="number"]'),
        btnUp = spinner.find('.quantity-up'),
        btnDown = spinner.find('.quantity-down'),
        min = input.attr('min'),
        max = input.attr('max');

      btnUp.click(function() {
        var oldValue = parseFloat(input.val());
        if (oldValue >= max) {
          var newVal = oldValue;
        } else {
          var newVal = oldValue + 1;
        }
        spinner.find("input").val(newVal);
        spinner.find("input").trigger("change");
      });

      btnDown.click(function() {
        var oldValue = parseFloat(input.val());
        if (oldValue <= min) {
          var newVal = oldValue;
        } else {
          var newVal = oldValue - 1;
        }
        spinner.find("input").val(newVal);
        spinner.find("input").trigger("change");
      });

    });
  


});

