/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b){b.Models.UserNameControl=b.Models.Control.extend({getModelType:function(){return"UserNameControl"},changeName:function(a){var c=b.Objects.Models.user,d=b.Objects.Models.thread,e=c.get("name");a&&e!=a&&(b.Objects.server.callFunctions([{"function":"rename",arguments:{references:{},"return":{},threadId:d.get("id"),token:d.get("token"),name:a}}],function(a){a.errorCode&&(b.Objects.Models.Status.message.setMessage(a.errorMessage||"Cannot rename"),c.set({name:e}))},!0),c.set({name:a}))}})})(Mibew);
