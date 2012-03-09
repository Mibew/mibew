/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

var SurveyForm = Class.create();
Class.inherit(SurveyForm, ClientForm, {
  checkFields: function() {
    if(this.form.email == null || this.form.email.getAttribute('type') == 'hidden') {
      return null;
    }
    if(! this.emailIsValid(this.form.email)){
      return this.localizedStrings.wrongEmail || '';
    }
    return null;
  }
});

EventHelper.register(window, 'onload', function(){
  Survey = new SurveyForm(document.surveyForm);
  Survey.localize(localizedStrings);
});