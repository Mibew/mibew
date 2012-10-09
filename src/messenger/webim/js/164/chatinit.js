/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
var chatController=null,pluginManager=new PluginManager;
Behaviour.register({"#postmessage a":function(a){a.onclick=function(){var a=$("msgwnd");a&&chatController.postMessage(a.value)}},"select#predefined":function(a){a.onchange=function(){chatController.selectPredefinedAnswer(this)}},"div#changename2 a":function(a){a.onclick=function(){chatController.showNameField();return!1}},"div#changename1 a":function(a){a.onclick=function(){chatController.applyName();return!1}},"div#changename1 input#uname":function(a){a.onkeydown=function(a){13==(a||event).keyCode&&
chatController.applyName()}},"a#refresh":function(a){a.onclick=function(){chatController.refresh()}},"a#togglesound":function(a){a.onclick=function(){chatController.toggleSound()}},"a.closethread":function(a){a.onclick=function(){chatController.closeThread()}}});
EventHelper.register(window,"onload",function(){FrameUtils.options.cssfile=chatParams.cssfile;var a=new ChatServer(chatParams.serverParams),b=new Thread(chatParams.threadParams);chatParams.initPlugins(pluginManager,b,a);var c=new ChatView(chatParams.localizedStrings,chatParams.predefinedAnswers||[]);chatController=new ChatController(a,c,b,{ignorectrl:-1}.extend(chatParams.controllerParams||{}))});