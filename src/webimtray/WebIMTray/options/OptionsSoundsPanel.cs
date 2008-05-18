using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Text;
using System.Windows.Forms;
using System.Resources;

namespace webImTray {
    public partial class OptionsSoundsPanel : UserControl, OptionsPanel {
        public event ModifiedEvent PanelModified;

        public OptionsSoundsPanel() {
            InitializeComponent();
        }

        private void OptionsSoundsPanel_Load(object sender, EventArgs e) {

        }

        void OptionsPanel.apply() {
        }

        void OptionsPanel.initialize() {
        }

        string OptionsPanel.getDescription() {
            return "Sounds";
        }

        public void updateUI(ResourceManager resManager) {
        }
    }
}
