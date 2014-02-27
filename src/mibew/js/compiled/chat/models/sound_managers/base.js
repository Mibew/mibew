/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,a){b.Models.BaseSoundManager=a.Model.extend({defaults:{enabled:!0},play:function(a){this.get("enabled")&&b.Utils.playSound(a)}})})(Mibew,Backbone);
