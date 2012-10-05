/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
var Thread=function(a){this.threadid=a.threadid||0;this.token=a.token||0;this.lastid=a.lastid||0;this.user=a.user||!1};Thread.prototype.KIND_USER=1;Thread.prototype.KIND_AGENT=2;Thread.prototype.KIND_FOR_AGENT=3;Thread.prototype.KIND_INFO=4;Thread.prototype.KIND_CONN=5;Thread.prototype.KIND_EVENTS=6;Thread.prototype.KIND_AVATAR=7;