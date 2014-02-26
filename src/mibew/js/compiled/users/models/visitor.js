/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,c){var b=[],f=a.Models.Visitor=a.Models.User.extend({defaults:c.extend({},a.Models.User.prototype.defaults,{controls:null,userName:"",userIp:"",remote:"",userAgent:"",firstTime:0,lastTime:0,invitations:0,chats:0,invitationInfo:!1}),initialize:function(){for(var e=[],b=f.getControls(),d=0,c=b.length;d<c;d++)e.push(new b[d]({visitor:this}));this.set({controls:new a.Collections.Controls(e)})}},{addControl:function(a){b.push(a)},getControls:function(){return b}})})(Mibew,_);
