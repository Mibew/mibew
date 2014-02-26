/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew) {

    /**
     * @class Represents Status bar view
     */
    Mibew.Views.StatusCollection = Mibew.Views.CollectionBase.extend(
        /** @lends Mibew.Views.StatusCollection.prototype */
        {
            /**
             * Default item view constructor.
             * @type Function
             */
            itemView: Mibew.Views.Status,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'status-collection'
        }
    );

})(Mibew);