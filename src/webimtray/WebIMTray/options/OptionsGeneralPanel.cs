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
                    Thread.CurrentThread.CurrentUICulture = OptionsDialog.englishCulture;
                } else if (radioRussian.Checked) {
                    Thread.CurrentThread.CurrentUICulture = OptionsDialog.russianCulture;
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

        public void updateUI(ResourceManager resManager) {
            groupBox1.Text = resManager.GetString("application");
            showInTaskBar.Text = resManager.GetString("showInTaskBar");
            autoStart.Text = resManager.GetString("autoStart");
            hideWhenStarted.Text = resManager.GetString("hideWhenStarted");
            groupBox2.Text = resManager.GetString("hotKeys");
            showOptions.Text = resManager.GetString("showOptions");
            showHide.Text = resManager.GetString("showHide");
            languageBox.Text = resManager.GetString("language");
            radioRussian.Text = resManager.GetString("russian");
            radioEnglish.Text = resManager.GetString("english");
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
