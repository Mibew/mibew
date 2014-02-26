/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,c){b.Models.Avatar=b.Models.Base.extend({defaults:{imageLink:!1},initialize:function(){this.registeredFunctions=[];this.registeredFunctions.push(b.Objects.server.registerFunction("setupAvatar",c.bind(this.apiSetupAvatar,this)))},finalize:function(){for(var a=0;a<this.registeredFunctions.length;a++)b.Objects.server.unregisterFunction(this.registeredFunctions[a])},apiSetupAvatar:function(a){a.imageLink&&this.set({imageLink:a.imageLink})}})})(Mibew,_);
