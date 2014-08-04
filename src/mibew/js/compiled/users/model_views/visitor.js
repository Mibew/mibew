/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,i){e.Views.Visitor=e.Views.CompositeBase.extend({template:i.templates.visitor,itemView:e.Views.Control,itemViewContainer:".visitor-controls",className:"visitor",modelEvents:{change:"render"},events:{"click .invite-link":"inviteUser","click .geo-link":"showGeoInfo","click .track-control":"showTrack"},inviteUser:function(){if(!this.model.get("invitationInfo")){var i=this.model.id,t=e.Objects.Models.page;e.Popup.open(t.get("inviteLink")+"?visitor="+i,"ImCenter"+i,t.get("inviteWindowParams"))}},showTrack:function(){var i=this.model.id,t=e.Objects.Models.page;e.Popup.open(t.get("trackedLink")+"?visitor="+i,"ImTracked"+i,t.get("trackedVisitorWindowParams"))},showGeoInfo:function(){var i=this.model.get("userIp");if(i){var t=e.Objects.Models.page,o=t.get("geoLink").replace("{ip}",i);e.Popup.open(o,"ip"+i,t.get("geoWindowParams"))}}})}(Mibew,Handlebars);