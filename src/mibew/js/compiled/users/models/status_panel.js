/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e){e.Models.StatusPanel=e.Models.Base.extend({defaults:{message:""},setStatus:function(e){this.set({message:e})},changeAgentStatus:function(){var a=e.Objects.Models.agent;a.get("away")?a.available():a.away()}})}(Mibew);