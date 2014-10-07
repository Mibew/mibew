function loadNews() {
	if (typeof(window.mibewNews) == "undefined" || typeof(window.mibewNews.length) == "undefined")
		return;
	
	var str = "<div>";
	for (var i = 0; i < window.mibewNews.length; i++) {
		str += "<div class=\"newstitle\"><a hre" + "f=\"" + window.mibewNews[i].link + "\">" + window.mibewNews[i].title + "</a>, <span class=\"small\">" + window.mibewNews[i].date + "</span></div>";
		str += "<div class=\"newstext\">" + window.mibewNews[i].message+"</div>";
	}
	$("#news").html(str + "</div>");
}

function loadVersion() {
	if(typeof(window.mibewLatest) == "undefined" || typeof(window.mibewLatest.version) == "undefined")
		return;
	
	var current = $("#cver").html();

	if(current != window.mibewLatest.version) {
		if(current < window.mibewLatest.version) {
			$("#cver").css("color","red");
		}
		$("#lver").html(window.mibewLatest.version+", Download <a href=\""+window.mibewLatest.download+"\">"+window.mibewLatest.title+"</a>");
	} else {
		$("#cver").css("color","green");
		$("#lver").html(window.mibewLatest.version);
	}
}

$(function(){
	loadNews();
	loadVersion();
});
