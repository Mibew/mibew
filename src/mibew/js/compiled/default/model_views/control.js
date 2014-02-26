/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,c,d){b.Views.Control=c.Marionette.ItemView.extend({template:d.templates.default_control,modelEvents:{change:"render"},events:{mouseover:"mouseOver",mouseleave:"mouseLeave"},attributes:function(){var a=[];a.push("control");this.className&&(a.push(this.className),this.className="");var b=this.getDashedControlType();b&&a.push(b);return{"class":a.join(" ")}},mouseOver:function(){var a=this.getDashedControlType();this.$el.addClass("active"+(a?"-"+a:""))},mouseLeave:function(){var a=this.getDashedControlType();
this.$el.removeClass("active"+(a?"-"+a:""))},getDashedControlType:function(){"undefined"==typeof this.dashedControlType&&(this.dashedControlType=b.Utils.toDashFormat(this.model.getModelType())||"");return this.dashedControlType}})})(Mibew,Backbone,Handlebars);
