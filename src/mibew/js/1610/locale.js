var popupStatus = 0;

function loadPopup(){
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#dashlocalesPopup").fadeIn("slow");
		popupStatus = 1;
	}
}
function disablePopup(){
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$("#dashlocalesPopup").fadeOut("slow");
		popupStatus = 0;
	}
}

function normpos(a) {
	if(a < 10) {
		return 10;
	}
	return a;
}

function centerPopup(){
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $("#dashlocalesPopup").height();
	var popupWidth = $("#dashlocalesPopup").width();
	$("#dashlocalesPopup").css({
		"position": "absolute",
		"top": normpos((windowHeight-popupHeight) * 0.2),
		"left": normpos(windowWidth/2-popupWidth/2)
	});
	$("#backgroundPopup").css({
		"height": windowHeight
	});
}

$(function(){
	$("#changelang").click(function(){
		centerPopup();
		loadPopup();
		return false;
	});
	$("#dashlocalesPopupClose").click(function(){
		disablePopup();
		return false;
	});
	$("#backgroundPopup").click(function(){
		disablePopup();
	});
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});
});
