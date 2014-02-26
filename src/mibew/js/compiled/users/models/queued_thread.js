/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a,c){var b=[],f=a.Models.QueuedThread=a.Models.Thread.extend({defaults:c.extend({},a.Models.Thread.prototype.defaults,{controls:null,userName:"",userIp:"",remote:"",userAgent:"",agentName:"",canOpen:!1,canView:!1,canBan:!1,ban:!1,totalTime:0,waitingTime:0,firstMessage:null}),initialize:function(){for(var e=[],b=f.getControls(),d=0,c=b.length;d<c;d++)e.push(new b[d]({thread:this}));this.set({controls:new a.Collections.Controls(e)})}},{addControl:function(a){b.push(a)},getControls:function(){return b}})})(Mibew,
_);
