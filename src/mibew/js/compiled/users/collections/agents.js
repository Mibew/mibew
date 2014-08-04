/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t,n){e.Collections.Agents=t.Collection.extend({model:e.Models.Agent,comparator:function(e){return e.get("name")},initialize:function(){var t=e.Objects.Models.agent;e.Objects.server.callFunctionsPeriodically(function(){return[{"function":"updateOperators",arguments:{agentId:t.id,"return":{operators:"operators"},references:{}}}]},n.bind(this.updateOperators,this))},updateOperators:function(e){this.set(e.operators)}})}(Mibew,Backbone,_);