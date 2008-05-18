using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Text;
using System.Windows.Forms;
using System.Resources;
using System.Globalization;

namespace webImTray {
    public partial class OptionsDialog : Form {

        static OptionsPanel[] panels = new OptionsPanel[] { 
            new OptionsGeneralPanel(), 
            new OptionsConnectionPanel(),
            new OptionsSoundsPanel(),
            new About()
        };

        OptionsPanel currentPanel = null;

        // FIXME: we have only one OptionsDialog instance
        // thus it's safe to keep it in a static variable.
        private static OptionsDialog currentInstance = null;

        public OptionsDialog() {
            InitializeComponent();
            currentInstance = this;
        }

        private void changePanel(OptionsPanel panel) {
            if (currentPanel == panel)
                return;

            if (currentPanel != null)
                container.Controls.Clear();
            currentPanel = panel;
            container.Controls.Add((Control)currentPanel);
        }

        private void updatePageSelector() {
            bool inited = false;
            pageSelector.Items.Clear();
            foreach (OptionsPanel p in panels) {
                ListViewItem item = new ListViewItem(p.getDescription(Options.resourceManager));
                if (!inited) {
                    item.Selected = true;
                    changePanel(p);
                    inited = true;
                }
                pageSelector.Items.Add(item);
            }
        }
        private void optionsDialogLoaded(object sender, EventArgs e) {
            updatePageSelector();
            foreach (OptionsPanel p in panels) {
                p.PanelModified += new ModifiedEvent(panelModified);
                p.initialize();
            }
            apply.Enabled = false;
        }

        void panelModified() {
            apply.Enabled = true;
        }

        OptionsPanel getPanel(string s) {
            foreach (OptionsPanel p in panels) {
                if (s.Equals(p.getDescription(Options.resourceManager)))
                    return p;
            }

            return null;
        }

        private void panelSelectionChanged(object sender, EventArgs e) {
            if (pageSelector.SelectedItems.Count == 1) {
                ListViewItem item = pageSelector.SelectedItems[0];
                OptionsPanel panel = getPanel(item.Text);
                if (panel != null) {
                    changePanel(panel);
                }
            }
        }

        private void openWebIMSite(object sender, LinkLabelLinkClickedEventArgs e) {
            System.Diagnostics.Process.Start("http://webim.ru/");
        }

        private void applyChanges() {
            foreach (OptionsPanel p in panels) {
                p.apply();
            }
        }

        private void ok_Click(object sender, EventArgs e) {
            applyChanges();
            Close();
        }

        private void apply_Click(object sender, EventArgs e) {
            applyChanges();
            apply.Enabled = false;
        }

        public static void updateUI() {
            for (int i = 0; i < 4; i++) {
                ((OptionsPanel)panels[i]).updateUI();
            }
            currentInstance.ok.Text = Options.resourceManager.GetString("ok");
            currentInstance.cancel.Text = Options.resourceManager.GetString("cancel");
            currentInstance.apply.Text = Options.resourceManager.GetString("apply");
            currentInstance.Text = Options.resourceManager.GetString("optionsTitle");
            currentInstance.updatePageSelector();
        }
    }
}