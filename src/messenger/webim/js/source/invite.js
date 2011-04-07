var mibewinviterequest;
var mibewinviteurl;
var mibewinvitetimeout;
var mibewinvitetimer;

var style = document.createElement('style');
document.getElementsByTagName('head')[0].appendChild(style);
 
if (!window.createPopup) {
    style.appendChild(document.createTextNode(''));
    style.setAttribute("type", "text/css");
}
 
var sheet = document.styleSheets[document.styleSheets.length - 1];
if (!window.createPopup) {
    var node = document.createTextNode(mibewInviteStyle);
    style.appendChild(node);
} else {
    sheet.cssText = mibewInviteStyle;
}

function mibewInviteMakeRequest(url, timeout)
{
	mibewinviteurl = url;
	mibewinvitetimeout = timeout;
        if(window.XMLHttpRequest)
        {
                    mibewinviterequest = new XMLHttpRequest();
        }
        else if(window.ActiveXObject)
        {
                    mibewinviterequest = new ActiveXObject("MSXML2.XMLHTTP");
        }
	if (mibewinviterequest) {
	    mibewinviterequest.onreadystatechange = mibewInviteOnResponse;
	}
        mibewInviteSendRequest(url);
}

function mibewInviteSendRequest(url)
{
	clearTimeout(mibewinvitetimer);
	mibewinviterequest.open("GET", url + '&rnd=' + Math.random(1), true);
	mibewinviterequest.send();
}

function mibewInviteCheckReadyState(obj)
{
        if ((obj.readyState == 4) && ((obj.status == 200) || (obj.status == 304))) {return true;}
}

function mibewInviteOnResponse()
{
        if(mibewInviteCheckReadyState(mibewinviterequest))
        {

                var response = mibewinviterequest.responseXML.documentElement;
                var invite = response.getElementsByTagName('message');
		if (invite[0]) {
		    var message = invite[0].firstChild.data;
		    var operator = response.getElementsByTagName('operator')[0] && response.getElementsByTagName('operator')[0].firstChild != null ? response.getElementsByTagName('operator')[0].firstChild.data : undefined;
		    var avatar = response.getElementsByTagName('avatar')[0] && response.getElementsByTagName('avatar')[0].firstChild != null ? response.getElementsByTagName('avatar')[0].firstChild.data : undefined;

		    var popuptext = '<div id="mibewinvitationpopup">';
		    popuptext += '<div id="mibewinvitationclose"><a href="javascript:void(0);" onclick="mibewHideInvitation();">&times;</a></div>';
		    if (operator) {
			popuptext += '<h1 onclick="mibewOpenAgent();">' + operator + '</h1>';
		    }
		    if (avatar) {
			popuptext += '<img id="mibewinvitationavatar" src="' + avatar + '" title="' + operator + '" alt="' + operator + '" onclick="mibewOpenAgent();" />';
		    }
		    popuptext += '<p onclick="mibewOpenAgent();">' + message + '</p>';
		    popuptext += '<div style="clear: both;"></div>';
		    var invitationdiv = document.getElementById("mibewinvitation");
		    if (invitationdiv) {
			invitationdiv.innerHTML = popuptext;
		    }
		}
		mibewinvitetimer = setTimeout( function(){ mibewInviteMakeRequest(mibewinviteurl, mibewinvitetimeout) }, mibewinvitetimeout);
        }
}

function mibewHideInvitation() {
    if (document.getElementById('mibewinvitationpopup')) {
	document.getElementById('mibewinvitationpopup').style.display='none';
    }
}

function mibewOpenAgent() {
    if (document.getElementById('mibewAgentButton')) {
	document.getElementById('mibewAgentButton').onclick();
	mibewHideInvitation();
    }
}