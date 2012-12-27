/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b){a.Models.StatusMessage=a.Models.Status.extend({defaults:b.extend({},a.Models.Status.prototype.defaults,{message:"",visible:!1}),getModelType:function(){return"StatusMessage"},setMessage:function(a){this.set({message:a,visible:!0});this.autoHide()}})})(Mibew,_);
