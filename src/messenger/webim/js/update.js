function loadNews() {
	if (typeof(window.webimNews) == "undefined" || typeof(window.webimNews.length) == "undefined")
		return;
	
	var str = "<div>";
	for (var i = 0; i < window.webimNews.length; i++) {
		str += "<div class=\"newstitle\"><a hre" + "f=\"" + window.webimNews[i].link + "\">" + window.webimNews[i].title + "</a>, <span class=\"small\">" + window.webimNews[i].date + "</span></div>";
		str += "<div class=\"newstext\">" + window.webimNews[i].message+"</div>";
	}
	$("#news").html(str + "</div>");
}

function loadVersion() {
	if(typeof(window.webimLatest) == "undefined" || typeof(window.webimLatest.version) == "undefined")
		return;
	
	var current = $("#cver").html();

	if(current != window.webimLatest.version) {
		if(current < window.webimLatest.version) {
			$("#cver").css("color","red");
		}
		$("#lver").html(window.webimLatest.version+", Download <a href=\""+window.webimLatest.download+"\">"+window.webimLatest.title+"</a>");
	} else {
		$("#cver").css("color","green");
		$("#lver").html(window.webimLatest.version);
	}
}

$(function(){
	loadNews();
	loadVersion();
});