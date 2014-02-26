/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone){

    /**
     * @class Represents status bar
     */
    Mibew.Collections.Status = Backbone.Collection.extend(
        /** @lends Mibew.Collections.Status.prototype */
        {
            /**
             * Use for sort controls in collection
             * @param {Backbone.Model} model Control model
             */
            comparator: function(model) {
                return model.get('weight');
            }
        }
    );

})(Mibew, Backbone);