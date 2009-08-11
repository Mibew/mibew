window.attachEvent('onload', mkwidth);
window.attachEvent('onresize', mkwidth);

function mkwidth(){
	if(document.getElementById("wrap700")) {
		document.getElementById("wrap700").style.width = document.documentElement.clientWidth < 750 ? "750px" : "100%";
	}
	if(document.getElementById("wrap400")) {
		document.getElementById("wrap400").style.width = document.documentElement.clientWidth < 450 ? "450px" : "100%";
	}
};