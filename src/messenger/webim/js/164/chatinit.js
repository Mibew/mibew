/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
var chatController={};
Behaviour.register({"#postmessage a":function(a){a.onclick=function(){var a=$("msgwnd");a&&chatController.postMessage(a.value)}},"select#predefined":function(a){a.onchange=function(){chatController.selectPredefinedAnswer(this)}},"div#changename2 a":function(a){a.onclick=function(){chatController.showNameField();return!1}},"div#changename1 a":function(a){a.onclick=function(){chatController.applyName();return!1}},"div#changename1 input#uname":function(a){a.onkeydown=function(a){13==(a||event).keyCode&&
chatController.applyName()}},"a#refresh":function(a){a.onclick=function(){chatController.refresh()}},"a#togglesound":function(a){a.onclick=function(){chatController.toggleSound()}},"a.closethread":function(a){a.onclick=function(){chatController.closeThread()}}});
EventHelper.register(window,"onload",function(){$LAB.setOptions({BasePath:chatParams.jsBasePath}).script("json2.js").wait().script("mibewapi.js").wait().script("chatserver.js").script("thread.js").script("messageview.js").script("pluginmanager.js").script("brws.js").wait().script("chatcontroller.js").script("chatview.js").wait(function(){FrameUtils.options.cssfile=chatParams.cssfile;var a=new ChatServer(chatParams.serverParams),c=new Thread(chatParams.threadParams),b=new PluginManager;chatParams.initPlugins(b,
c,a);b=new ChatView(new MessageView,chatParams.localizedStrings||{},chatParams.predefinedAnswers||[]);chatController=new ChatController(a,b,c,{ignorectrl:-1}.extend(chatParams.controllerParams||{}))})});