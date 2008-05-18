using System;
using System.Collections.Generic;
using System.Text;
using System.Resources;

namespace webImTray {

    public delegate void ModifiedEvent();

    interface OptionsPanel {
        void initialize();
        void apply();
        string getDescription(ResourceManager resManager);
        void updateUI(ResourceManager resManager);

        event ModifiedEvent PanelModified; 
    }
}
