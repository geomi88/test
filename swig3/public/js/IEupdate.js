function updateBrowser(){
document.getElementById('body').innerHTML = '<div id="ieWrapper">'+
		'<div class="ieHolder">'+
			'<div class="ieLogo"></div>'+        
			'<a href="http://ie9.discoverbing.com/index_nie8.html?form=MFEHPG&publ=IE9&crea=TEXT_MFEHPG_EIE9_MSCOM_OIE8productpage_1x1" class="btnIeUpdate">Browser update</a>'+ 
			'<p class="ieContent">'+ 
				'<strong>Update your browser</strong>'+ 
				'This browser cant preview this website<br /> Please update to higher version.'+   
			'</p>'+ 
			'<br clear="all" />'+ 
		'</div>'+     
	'</div>';
}

setTimeout(function(){updateBrowser();},500);

window.onload = updateBrowser;