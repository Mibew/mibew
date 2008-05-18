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
                Options.RussianLocale = radioRussian.Checked;

                // Apply locale
                if (radioEnglish.Checked) {
                    Thread.CurrentThread.CurrentUICulture = Options.englishCulture;
                } else if (radioRussian.Checked) {
                    Thread.CurrentThread.CurrentUICulture = Options.russianCulture;
                }
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
            if (!Options.RussianLocale) {
                radioEnglish.Checked = true;
            } else {
                radioRussian.Checked = true;
            }

            // Update UI according to the current locale
            OptionsDialog.updateUI();

            modified = false;
        }
    
        string OptionsPanel.getDescription(ResourceManager resManager) {
            return resManager.GetString("general");
        }

        public void updateUI() {
            groupBox1.Text = Options.resourceManager.GetString("application");
            showInTaskBar.Text = Options.resourceManager.GetString("showInTaskBar");
            autoStart.Text = Options.resourceManager.GetString("autoStart");
            hideWhenStarted.Text = Options.resourceManager.GetString("hideWhenStarted");
            groupBox2.Text = Options.resourceManager.GetString("hotKeys");
            showOptions.Text = Options.resourceManager.GetString("showOptions");
            showHide.Text = Options.resourceManager.GetString("showHide");
            languageBox.Text = Options.resourceManager.GetString("language");
            radioRussian.Text = Options.resourceManager.GetString("russian");
            radioEnglish.Text = Options.resourceManager.GetString("english");
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
