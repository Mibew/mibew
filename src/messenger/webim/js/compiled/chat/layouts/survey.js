/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b){a.Layouts.Survey=b.Marionette.Layout.extend({template:Handlebars.templates.survey_layout,regions:{surveyFormRegion:"#content-wrapper"},serializeData:function(){return{page:a.Objects.Models.page.toJSON()}}})})(Mibew,Backbone);
