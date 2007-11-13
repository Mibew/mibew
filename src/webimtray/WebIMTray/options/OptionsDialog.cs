using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Text;
using System.Windows.Forms;

namespace webImTray {
    public partial class OptionsDialog : Form {

        OptionsPanel[] panels = new OptionsPanel[] { 
            new OptionsGeneralPanel(), 
            new OptionsConnectionPanel(),
            new OptionsSoundsPanel(),
            new About()
        };

        OptionsPanel currentPanel = null;

        public OptionsDialog() {
            InitializeComponent();
        }

        private void changePanel(OptionsPanel panel) {
            if (currentPanel == panel)
                return;

            if (currentPanel != null)
                container.Controls.Clear();
            currentPanel = panel;
            container.Controls.Add((Control)currentPanel);
        }

        private void optionsDialogLoaded(object sender, EventArgs e) {
            bool inited = false;

            foreach (OptionsPanel p in panels) {
                ListViewItem item = new ListViewItem(p.getDescription());
                if (!inited) {
                    item.Selected = true;
                    changePanel(p);
                    inited = true;
                }
                p.PanelModified += new ModifiedEvent(panelModified);
                p.initialize();
                pageSelector.Items.Add(item);
            }
            apply.Enabled = false;
        }

        void panelModified() {
            apply.Enabled = true;
        }

        OptionsPanel getPanel(string s) {
            foreach (OptionsPanel p in panels) {
                if (s.Equals(p.getDescription()))
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
    }
}