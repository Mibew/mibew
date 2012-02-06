/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

var Survey = {
	checkFields: function(){
		var emailField = document.surveyForm.email;
		var emailPattern = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if( emailField != null && emailField.value.search(emailPattern) == -1 ){
			return this.localizedStrings.wrongEmail;
		}
		return null;
	},

	submit: function(){
		var error = this.checkFields();
		if(error == null){
			document.surveyForm.submit();
		}else{
			alert(error);
		}
	}
}

EventHelper.register(window, 'onload', function(){
  Survey.localizedStrings = localizedStrings;
});