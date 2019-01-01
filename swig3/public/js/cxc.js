	
var customJS;

jQuery(document).ready(function($){


	customJS = {
		
		common : {
			commonJS : function(){
				
			var currentScrollPos;
			$(".iconMobNav").on('click',function() {
				if($(this).hasClass('activeMenu')){
					$(this).removeClass('activeMenu');
					$(".mobNav").removeClass('menuShow');
					
					$('html,body').stop().animate({scrollTop: currentScrollPos},{queue: false, duration:1000});
				}else{
					$(this).addClass('activeMenu');
					currentScrollPos = $(window).scrollTop();
					$(".mobNav").addClass('menuShow');
					$('html,body').stop().animate({scrollTop: 0},{queue: false, duration:1000});
				}
			});

			$('body').on('click','.btnUpload',function(){
				$(this).parent().find('.btnCommonUplod').trigger('click');
			});

			$('.btnCommonUplod').change(function(e){
				var fileName = e.target.files[0].name;
				$('.fileValue').html(fileName);
			});

				
				//----------------mobile Nav--------------
				
			//----------------mobile Nav--------------
			
			var newWidth = $('.contentRight').width();
			$('body').on('click','.iconMobNav',function(){
				$('.contentRight').toggleClass('leftAnimo');
			});
			
			if($(window).width()>1024){
				$('.contentRight').removeClass('leftAnimo');
			}
			
			
			$('body').on('click','.iconMobNav', function(){
				$(this).toggleClass('open');
			});
			
			$('body').on('click','.btnModal',function(){
				$('.overlay').fadeIn('slow');
				$('.imgPopup').fadeIn('slow');
			});
			
			$('body').on('click','.imgPopup',function(){
				$(this).fadeOut(250);
				$('.overlay').fadeOut(250);
			});
			    

			$('#cashierPay').prop('checked', true);	
			$('.formListV1Dtl .commonBtn').click(function () {
				var formStep = $(this).attr('rel');
				$('.formListV1Dtl').hide();
				$(formStep).show();
			});

			$('.tabBtnV2 a').click(function () {
				var tabCall = $(this).attr('rel');
				$('.count1 .tabList').hide();
				$('.tabBtnV2 li').removeClass('selected');
				$(this).parent().addClass('selected');
				$(tabCall).show();
			});

			$('.cashPay input[type="radio"]').click(function() {
				var payType = $(this).attr('id');
				$('.cashPayContent > div').hide();
				$('.'+payType).show();
			});

			$('.unitOption').click(function () {
				$('.unitModal').show();
				$('.commonLoaderV1').show();
			});

			$('.unitModal .btnClose').click(function () {
				$('.unitModal').hide();
				$('.commonLoaderV1').hide();
			});

			$('.unitOption').click(function () {
				$(this).closest('.privilegeHolder').toggleClass('selected');
			});

			$(".allow1").on("sortremove", function(event, ui) {
				ui.item.prependTo( ui.item.parent());
				$.each(ui.item.parent().children(), function(index, item) {
					if ( index > 0 ) {
						$(this).appendTo($(".allow1"));

					}
				});
			});    

			$('.dropme').sortable({
				connectWith: '.dropme',
				start: function(event, ui) {
					$(this).addClass('dragged');
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

	

			$('.listSelectAll input, .alltSelected').prop('checked', false);	
                        
                        $('body').on('click','.listSelectAll input', function () {
//		
				if ($(this).is(':checked')) {
					$(this).parents(':eq(4)').find('.alltSelected').prop('checked', true);	
				}
				else {
					$(this).parents(':eq(4)').find('.alltSelected').prop('checked', false);	
				}
			});


			
			var winHeight = $(window).height();
			$('.contentLeft').css('min-height',winHeight);	


			},
			
			html5Tags : function(){
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
						
			commonInput : function(){
				
				
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
					
				}
				
				var $inputText = $('.queryInput input, .queryInput textarea');
				$inputText.each(function(){
					var $thisHH = $(this);
					if(!$(this).val()){
						$(this).parent().find('label').show();
					}else{
						setTimeout(function(){
						$thisHH.parent().find('label').hide();
						},100);
					}
					
				});
				$inputText.focus(function(){
					if(!$(this).val()){
						$(this).parent().find('label').addClass('showLab');
					}
				});
				$inputText.keydown(function(){
					if(!$(this).val()){
						$(this).parent().find('label').hide();
					}
				});
				$inputText.on("blur",function(){
					var $thisH = $(this);
					if(!$(this).val()){
						$(this).parent().find('label').show().removeClass('showLab');
					}else{
						$thisH.parent().find('label').hide();
					}
					
				});

				
				$( "select" ).trigger( "click" );

				function commonSelect (){
					var $selectText = $('.bgSelect input');
					var $selectLi = $('.bgSelect li');
					var selectval;
					var Drop=0;
					
					$('body').click(function(){
						if(Drop==1){
							$('.bgSelect ul').hide();
							Drop=0;			
						}
					});
					$selectText.click(function(){
						$('.bgSelect ul').hide();
						Drop=0;							  
						if(Drop==0){					  
							$(this).parent().find('ul').slideDown();
						}
						setTimeout(function(){
							Drop=1;				
						},50);
									
									
					});
					
					$selectLi.click(function(){
						Drop = 1;					
						selectval = $(this).text();
						if($(this).parent().parent().parent().find('.suggestionDroplist').length > 0){
							$(this).parent().parent().parent().find('input').val(selectval);
							$(this).parent().parent().parent().find('.commonError').hide();
						}else{
							$(this).parent().parent().find('input').val(selectval);
							$(this).parent().parent().find('.commonError').hide();
						}
						
					});
				}

				commonSelect ();
                                
                    var getUrlPath = location;
                               
				$('.mainMenu li a').each(function() {
					if ($(this).attr('href') == getUrlPath) {
							$(this).parent().addClass('selected');
					}
				});
                                
                                
				
			}
			
		}//end commonJS
			
	};
	
	
	customJS.common.commonJS();
	customJS.common.html5Tags();
	customJS.common.commonInput();

});
