/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(t,e){var o=[],n=t.Models.Visitor=t.Models.User.extend({defaults:e.extend({},t.Models.User.prototype.defaults,{controls:null,userName:"",userIp:"",remote:"",userAgent:"",firstTime:0,lastTime:0,invitations:0,chats:0,invitationInfo:!1}),initialize:function(){for(var e=this,o=[],s=n.getControls(),i=0,r=s.length;r>i;i++)o.push(new s[i]({visitor:e}));this.set({controls:new t.Collections.Controls(o)})}},{addControl:function(t){o.push(t)},getControls:function(){return o}})}(Mibew,_);