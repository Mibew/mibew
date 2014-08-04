/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,s){e.Collections.Messages=s.Collection.extend({model:e.Models.Message,updateMessages:function(e){for(var s=[],n=0;n<e.length;n++)e[n].message&&s.push(e[n]);s.length>0&&this.add(s)}})}(Mibew,Backbone,_);