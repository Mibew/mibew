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

        public string getDescription() {
            return "About";
        }

        #endregion

        private void i_services_ru_link(object sender, LinkLabelLinkClickedEventArgs e) {
            System.Diagnostics.Process.Start("http://openwebim.org/");
        }

        private void webim_ru_link(object sender, LinkLabelLinkClickedEventArgs e) {
            System.Diagnostics.Process.Start("http://openwebim.org/");
        }

        public event ModifiedEvent PanelModified;
    }
}
