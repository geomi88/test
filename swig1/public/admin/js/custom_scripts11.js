	
var customJS;

jQuery(document).ready(function($){

	customJS = {
		
		common : {
			commonJS : function(){
				function pieGraphCall(gtlabel){
			      	var qData = gtlabel;
					qData = JSON.parse(qData);
			  		//-----------------graph--------------
					var ctx1 = document.getElementById('pieGraphCanvas1').getContext('2d');
					var doughnut1 = new Chart(ctx1, {
						type: 'pie',
						data: qData,
						options: {
							responsive: true,
							maintainAspectRatio: false,
							legend: {
								display: false
							},
							pieceLabel: {
								fontColor: '#000',
								fontSize: '10',
								overlap: true,
								position: 'border',
							    render: 'percentage'
							},
							tooltips: {
								callbacks: {
									title: function (tooltipItem, data) {
										return data['labels'][tooltipItem[0]['index']];
									},
									label: function (tooltipItem, data) {
									}
								},
								backgroundColor: '#000',
								titleFontSize: 12,
								titleFontColor: '#fff',
								displayColors: false
							}
						}
					});
				}
				function equalHeight(){
					$('.eqHeightHolder').each(function(){  
				        var highestBox = 0;
				        $(this).find('.eqHeightInner').each(function(){
				            if($(this).height() > highestBox){  
				                highestBox = $(this).height();  
				            }
				        })
				        $(this).find('.eqHeightInner').height(highestBox);
				    });
				}
				//all listing change background image
				$(".allListingWrapper .allListBoxHolder").each(function() {
				  	var imgPath = $(this).find(".listImgHolder img").attr('src');
				  	$(this).find(".listImgHolder").css('background-image', 'url("'+ imgPath +'")');
				});
				//dropdown script
				$('.dropClick').click(function(e){
					$(this).parent().find('.dropdownOpen').toggle();
				});
				$(document).on('click', function(event) {
					if(!$(event.target).closest('.dropClick, .dropdownOpen').length) {
						$('.dropdownOpen').hide();	
					}
				});
				if($('.detailPageWrapper .detailSliderHolder').length>0)
				{
					$('.detailPageWrapper .detailSliderHolder ul').bxSlider({
						auto: false,
						infiniteLoop: true,
						controls: false,
						speed: 800
					});
				}
				//tab script
				$('.tabEventHolder li:eq(0)').addClass('current');
			    $('.tabOuterHolder .tabContent:eq(0)').addClass('current');
		    	$('.tabEventHolder li').click(function(){
					var tab_id = $(this).attr('data-tab');
					$(this).siblings().removeClass('current');
					$(this).closest('.tabOuterHolder').find('.tabContent').removeClass('current');
					$(this).addClass('current');
					$("#"+tab_id).addClass('current');
				});

				$(window).load(function(){
					if($('.pieChart').length>0){
						var gtlabel1 ='{"labels":["Houses","Appartments","Office","Commercial","Parking"],"datasets":[{"fill":"true","backgroundColor":["#17b794","#ffc100","#45aaf2","#ff679b","#777edd"],"borderColor":"#fff","data":["1254","12654","724","2724","1300"]}]}';
						pieGraphCall(gtlabel1);
					}
					equalHeight();
				});
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
			}
		}//end commonJS
	};
	
	customJS.common.commonJS();
	customJS.common.html5Tags();

});