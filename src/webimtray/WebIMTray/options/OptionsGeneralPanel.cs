using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Text;
using System.Windows.Forms;
using System.Resources;
using System.Threading;
using System.Globalization;

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

                // Save locale
                // Options.AppLocale = ...

                // Apply locale
                //    Thread.CurrentThread.CurrentUICulture = Options.englishCulture;

                // Update UI according to the current locale
                OptionsDialog.updateUI();
                modified = false;
            }
        }

        void OptionsPanel.initialize() {
            showInTaskBar.Checked = Options.ShowInTaskBar;
            autoStart.Checked = Options.AutoStart;
            hideWhenStarted.Checked = Options.HideAfterStart;
    
            // Restore previously set locale
            languageSelector.Items.Add("English");
            languageSelector.SelectedIndex = 0;

            // Update UI according to the current locale
            OptionsDialog.updateUI();

            modified = false;
        }
    
        string OptionsPanel.getDescription() {
            return "General";
        }

        private void radioEnglish_CheckedChanged(object sender, EventArgs e) {
            modified = true;
            PanelModified.Invoke();
        }

        private void radioRussian_CheckedChanged(object sender, EventArgs e) {
            modified = true;
            PanelModified.Invoke();
        }
    }
}
