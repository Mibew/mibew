using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Text;
using System.Windows.Forms;
using System.Resources;

namespace webImTray {
    public partial class OptionsGeneralPanel : UserControl, OptionsPanel {
        bool modified = false;
        public event ModifiedEvent PanelModified;

        public OptionsGeneralPanel() {
            InitializeComponent();
        }

        private void checkboxChanged(object sender, EventArgs e) {
            modified = true;
            PanelModified.Invoke();
        }

        void OptionsPanel.apply() {
            if (modified) {
                Options.ShowInTaskBar = showInTaskBar.Checked;
                Options.AutoStart = autoStart.Checked;
                Options.HideAfterStart = hideWhenStarted.Checked;
                modified = false;
            }
        }

        void OptionsPanel.initialize() {
            showInTaskBar.Checked = Options.ShowInTaskBar;
            autoStart.Checked = Options.AutoStart;
            hideWhenStarted.Checked = Options.HideAfterStart;
            modified = false;
        }
    
        string OptionsPanel.getDescription() {
            return "General";
        }

        public void updateUI(ResourceManager resManager) {
            groupBox1.Text = resManager.GetString("application");
        }

        private void radioEnglish_CheckedChanged(object sender, EventArgs e) {
            // Set english locale

            // Update UI
            OptionsDialog.updateUI();
        }
    }
}
