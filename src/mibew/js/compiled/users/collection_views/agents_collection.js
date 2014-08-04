/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e){e.Views.AgentsCollection=e.Views.CollectionBase.extend({itemView:e.Views.Agent,className:"agents-collection",collectionEvents:{"sort add remove reset":"render"},initialize:function(){this.on("itemview:before:render",this.updateIndexes,this)},updateIndexes:function(e){var i=this.collection,t=e.model;t&&(e.isModelFirst=0==i.indexOf(t),e.isModelLast=i.indexOf(t)==i.length-1)}})}(Mibew);