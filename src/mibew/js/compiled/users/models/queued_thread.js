/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,n){var t=[],o=e.Models.QueuedThread=e.Models.Thread.extend({defaults:n.extend({},e.Models.Thread.prototype.defaults,{controls:null,userName:"",userIp:"",remote:"",userAgent:"",agentName:"",canOpen:!1,canView:!1,canBan:!1,ban:!1,totalTime:0,waitingTime:0,firstMessage:null}),initialize:function(){for(var n=this,t=[],s=o.getControls(),a=0,r=s.length;r>a;a++)t.push(new s[a]({thread:n}));this.set({controls:new e.Collections.Controls(t)})}},{addControl:function(e){t.push(e)},getControls:function(){return t}})}(Mibew,_);