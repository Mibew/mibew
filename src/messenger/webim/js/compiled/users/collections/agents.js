/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,c,d){b.Collections.Agents=c.Collection.extend({model:b.Models.Agent,comparator:function(a){return a.get("name")},initialize:function(){var a=b.Objects.Models.agent;b.Objects.server.callFunctionsPeriodically(function(){return[{"function":"updateOperators",arguments:{agentId:a.id,"return":{operators:"operators"},references:{}}}]},d.bind(this.updateOperators,this))},updateOperators:function(a){this.set(a.operators)}})})(Mibew,Backbone,_);
