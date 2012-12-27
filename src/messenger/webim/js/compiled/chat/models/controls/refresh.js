/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a){a.Models.RefreshControl=a.Models.Control.extend({getModelType:function(){return"RefreshControl"},refresh:function(){a.Objects.server.restartUpdater()}})})(Mibew);
