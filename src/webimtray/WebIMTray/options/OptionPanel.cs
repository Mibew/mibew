using System;
using System.Collections.Generic;
using System.Text;

namespace webImTray {

    public delegate void ModifiedEvent();

    interface OptionsPanel {
        void initialize();
        void apply();
        string getDescription();

        event ModifiedEvent PanelModified; 
    }
}
