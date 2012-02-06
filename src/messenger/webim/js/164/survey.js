/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
var Survey={checkFields:function(){var a=document.surveyForm.email,b=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;return null!=a&&-1==a.value.search(b)?this.localizedStrings.wrongEmail:null},submit:function(){var a=this.checkFields();null==a?document.surveyForm.submit():alert(a)}};EventHelper.register(window,"onload",function(){Survey.localizedStrings=localizedStrings});