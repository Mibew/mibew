using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Text;
using System.Windows.Forms;

namespace webImTray {
    public partial class OptionsConnectionPanel : UserControl, OptionsPanel {
        bool modified = false;
        bool initialized = false;
        
        public OptionsConnectionPanel() {
            InitializeComponent();
        }

        void OptionsPanel.apply() {
            if (modified) {
                Options.WebIMServer = webimServer.Text;
                Options.isLiteServer = radioLite.Checked;
                if (forceRefresh.Checked) {
                    Options.ForceRefreshTime = forceRefreshTime.Value;
                } else {
                    Options.ForceRefreshTime = 0;
                }
            }
        }

        void OptionsPanel.initialize() {
            webimServer.Text = Options.WebIMServer;

            decimal refreshTime = Options.ForceRefreshTime;
            forceRefreshTime.Enabled = forceRefresh.Checked = refreshTime != 0;
            forceRefreshTime.Value = refreshTime != 0 ? refreshTime : 15;

            bool lite = Options.isLiteServer;
            radioPro.Checked = !lite;
            radioLite.Checked = lite;

            modified = false;
            initialized = true;
        }

        string OptionsPanel.getDescription() {
            return "Connection";
        }

        public event ModifiedEvent PanelModified;

        private void webimServer_TextChanged(object sender, EventArgs e) {
            modified = true;
            PanelModified.Invoke();
        }

        private void forceRefresh_CheckedChanged(object sender, EventArgs e) {
            modified = true;
            PanelModified.Invoke();
            forceRefreshTime.Enabled = forceRefresh.Checked;
        }

        private void forceRefreshTime_Changed(object sender, EventArgs e) {
            modified = true;
            PanelModified.Invoke();
        }

        private void showUserPropertiesOnline(object sender, LinkLabelLinkClickedEventArgs e) {
            System.Diagnostics.Process.Start(Options.WebIMServer + (Options.isLiteServer ? Options.LITE_SETTINGS_PAGE : Options.PRO_SETTINGS_PAGE ));
        }

        private void radioCheckedChanged(object sender, EventArgs e) {
            modified = true;
            PanelModified.Invoke();
            if (initialized) {
                webimServer.Text = radioLite.Checked ? Options.DEFAULT_LITE_SERVER : Options.DEFAULT_PRO_SERVER;
            }
        } 
    }
}
