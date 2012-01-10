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

function mibewInviteOnResponse(response)
{
	var message = response.invitation.message;
	var operator = response.invitation.operator;
	var avatar = response.invitation.avatar;

	var popuptext = '<div id="mibewinvitationpopup">';
	popuptext += '<div id="mibewinvitationclose"><a href="javascript:void(0);" onclick="mibewHideInvitation();">&times;</a></div>';
	if (operator) {
		popuptext += '<h1 onclick="mibewOpenAgent();">' + operator + '</h1>';
	}
	if (avatar) {
		popuptext += '<img id="mibewinvitationavatar" src="' + avatar + '" title="' + operator + '" alt="' + operator + '" onclick="mibewOpenAgent();" />';
	}
	popuptext += '<p onclick="mibewOpenAgent();">' + message + '</p>';
	popuptext += '<div style="clear: both;"></div></div>';
	var invitationdiv = document.getElementById("mibewinvitation");
	if (invitationdiv) {
		invitationdiv.innerHTML = popuptext;
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