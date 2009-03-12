window.attachEvent('onload', mkwidth);
window.attachEvent('onresize', mkwidth);

var minwidth = 700;

function mkwidth(){
    document.getElementById("wrap").style.width = document.documentElement.clientWidth < minwidth ? minwidth+"px" : "100%";
};