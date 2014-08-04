/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,s){e.Models.StatusMessage=e.Models.Status.extend({defaults:s.extend({},e.Models.Status.prototype.defaults,{message:"",visible:!1}),getModelType:function(){return"StatusMessage"},setMessage:function(e){this.set({message:e,visible:!0}),this.autoHide()}})}(Mibew,_);