var customJS;

jQuery(document).ready(function($){

	customJS = {
		
		common : {
			commonJS : function(){

				
				$(".mainNavCtrl").on('click',function() {
					$('body').toggleClass('minMenu');
				});

				

				if($(window).width()<480){
					$('body').addClass('minMenu');
				}
				
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
				
				//----------------mobile Nav--------------
				
				var newWidth = $('.pageContent').width();
				$('body').on('click','.iconMobNav',function(){
					$('.pageContent').toggleClass('leftAnimo');
				});
				
				if($(window).width()>1024){
					$('.pageContent').removeClass('leftAnimo');
				}
				
			   
			    $('body').on('click','.iconMobNav', function(){
			        $(this).toggleClass('open');
			    });
                            
                            $('body').on('change','.tableSelect', function(){
                                $('.resonToolTip').hide();
			        if($(this).val() == 3) {
                                    $(this).parent().find('.resonToolTip').show();
                                }
			    });
                            
                             $('body').on('click','.tooltipClose', function(){
                                $('.resonToolTip').hide();
			    });
			    
				//---------window resize----------
				
				$(window).resize(function(){
					if($(window).width()>640){
						$('.pageContent').removeClass('leftAnimo');
					 	$('.iconMobNav').removeClass('open');
					}
				});
				
				
				//---------------Quantity value change
			    
				$('.plus').on('click',function(e) {
					e.preventDefault();
					var $this = $(this);
					var $input = $this.siblings('input');
					var value = parseInt($input.val());
					
					if (value < 30) {
						value = value + 1;
					} 
					else {
						value =30;
					}
					
					$input.val(value);
				});

				$('.minus').on('click',function(e) {
					e.preventDefault();
					var $this = $(this);
					var $input = $this.siblings('input');
					var value = parseInt($input.val());
					
						if (value > 1) {
							value = value - 1;
						} 
						else {
							value =0;
						}
					$input.val(value);
				});
	//---- Tab Accordion---------------		
				
	$('.tabWrapper li:eq(0)').addClass('selected');
	 	$('.tabContent:eq(0)').show(); $('body').on('click', '.tabWrapper li', function(){
	 	$('.tabWrapper li').removeClass('selected');
	   	$(this).addClass('selected');
	    var getRel = $(this).attr('rel');
	    var ConHeight = $('#'+getRel).height()+80; $('.tabDtls').animate({height:ConHeight});
	    $('.tabContent').hide(); $('#'+getRel).fadeIn(600);
 	}); 
 	
 	
 	
 	
 	
 	// if($('.tabDtls .tabContent').is(':visible')){
 		// $(this).closest('.btnTab').addClass('selected');
 	// }
      
     
   $('body').on('click','.btnTab',function(){
        $('.tabContent').hide(); 
        $(this).next('.tabContent').slideDown(400).siblings('.tabContent').slideUp(400); 
    });
				
				
				
				
				//---------------end--------------
				
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
				
			}
			
		}//end commonJS
			
	};
	
	
	customJS.common.commonJS();
	customJS.common.html5Tags();
	customJS.common.commonInput();

});