/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(i,e){i.Models.Avatar=i.Models.Base.extend({defaults:{imageLink:!1},initialize:function(){this.registeredFunctions=[],this.registeredFunctions.push(i.Objects.server.registerFunction("setupAvatar",e.bind(this.apiSetupAvatar,this)))},finalize:function(){for(var e=0;e<this.registeredFunctions.length;e++)i.Objects.server.unregisterFunction(this.registeredFunctions[e])},apiSetupAvatar:function(i){i.imageLink&&this.set({imageLink:i.imageLink})}})}(Mibew,_);