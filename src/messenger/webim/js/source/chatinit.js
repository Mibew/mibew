/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

var chatController = null;
var pluginManager = new PluginManager();

Behaviour.register({
    '#postmessage a' : function(el) {
        el.onclick = function() {
            var message = $('msgwnd');
            if (message) {
                chatController.postMessage(message.value);
            }
        };
    },

    'select#predefined' : function(el) {
        el.onchange = function() {
            chatController.selectPredefinedAnswer(this);
        };
    },

    'div#changename2 a' : function(el) {
        el.onclick = function() {
            chatController.showNameField();
            return false;
        };
    },

    'div#changename1 a' : function(el) {
        el.onclick = function() {
            chatController.applyName();
            return false;
        };
    },

    'div#changename1 input#uname' : function(el) {
        el.onkeydown = function(e) {
            var ev = e || event;
            if( ev.keyCode == 13 ) {
                chatController.applyName();
            }
        };
    },

    'a#refresh' : function(el) {
        el.onclick = function() {
            chatController.refresh();
        };
    },

    'a#togglesound' : function(el) {
        el.onclick = function() {
            chatController.toggleSound();
        };
    },

    'a.closethread' : function(el) {
        el.onclick = function() {
            chatController.closeThread();
        };
    }
});

EventHelper.register(window, 'onload', function(){
    FrameUtils.options.cssfile = chatParams.cssfile;
    var chatServer = new ChatServer(chatParams.serverParams);
    var thread = new Thread(chatParams.threadParams);
    chatParams.initPlugins(pluginManager, thread, chatServer);
    var chatView = new ChatView(
        chatParams.localizedStrings,
        chatParams.predefinedAnswers || []
    );
    chatController = new ChatController(
        chatServer,
        chatView,
        thread,
        {ignorectrl: -1}.extend(chatParams.controllerParams || {})
    );
});