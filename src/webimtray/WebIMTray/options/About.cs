using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Text;
using System.Windows.Forms;
using System.Resources;

namespace webImTray {
    public partial class About : UserControl, OptionsPanel {
        public About() {
            InitializeComponent();
        }

        #region OptionsPanel Members

        public void initialize() {
        }

        public void apply() {
        }

        public string getDescription(ResourceManager resManager) {
            return resManager.GetString("about");
        }

        #endregion

        private void i_services_ru_link(object sender, LinkLabelLinkClickedEventArgs e) {
            System.Diagnostics.Process.Start("http://i-services.ru/");
        }

        private void webim_ru_link(object sender, LinkLabelLinkClickedEventArgs e) {
            System.Diagnostics.Process.Start("http://webim.ru/");
        }

        public event ModifiedEvent PanelModified;

        public void updateUI() {
            label1.Text = Options.resourceManager.GetString("webimtray");
            label2.Text = Options.resourceManager.GetString("version");
            label3.Text = Options.resourceManager.GetString("copyright");
            label4.Text = Options.resourceManager.GetString("visitUs");
            linkLabel1.Text = Options.resourceManager.GetString("url");
        }
    }
}
