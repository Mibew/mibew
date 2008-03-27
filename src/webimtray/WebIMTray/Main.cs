//#define DEBUG

using System;
using System.Drawing;
using System.Collections;
using System.ComponentModel;
using System.Windows.Forms;
using System.Data;

namespace webImTray {

    public partial class MainWindow : LockNotificationForm {

        public MainWindow() {
            InitializeComponent();
            PostInitialize();
        }

        [STAThread]
        static void Main(string[] args) {
            Options.parseParameters(args);
            Application.Run(new MainWindow());
        }

        void PostInitialize() {

            webBrowser1.DocumentTitleChanged += new EventHandler(webBrowser1_DocumentTitleChanged);

            if (Options.ShowInTaskBar) {
                this.ShowInTaskbar = true;
            }

            if (Options.HideMainWindow) {
                this.WindowState = FormWindowState.Minimized;
            }

            navigateThere();
            setupReloadTimer();
        }

        void navigateThere() {
#if DEBUG
            webBrowser1.Navigate("http://localhost:8080/webim/operator/users.php");
#else
            webBrowser1.Navigate(Options.WebIMServer + Options.PENDING_USERS_PAGE);
#endif
        }

        void navigateBlank() {
            webBrowser1.Navigate("about:blank");
        }

        private void showWindow() {
            this.Visible = true;
            this.Activate();
            this.WindowState = FormWindowState.Normal;
        }

        private void hideWindow() {
            this.Visible = false;
        }

        private void notifyIconClick(object sender, System.Windows.Forms.MouseEventArgs e) {
            if (e.Button != MouseButtons.Left)
                return;

            bool wasVisible = this.Visible;

            if (wasVisible)
                hideWindow();
            else
                showWindow();
        }

        bool forceClosing = false;

        private void menuExitClick(object sender, System.EventArgs e) {
            forceClosing = true;
            this.Close();
        }

        private void gotFocus(object sender, System.EventArgs e) {
            if (this.Visible == false) {
                showWindow();
            }
        }

        void webBrowser1_DocumentTitleChanged(object sender, EventArgs e) {
            string s = webBrowser1.DocumentTitle;
            if (s == null || s.Length == 0)
                s = "Internet Services - Web Messenger [loading]";
            this.Text = s;
        }

        private void Client_FormClosing(object sender, FormClosingEventArgs e) {
            if( !forceClosing && e.CloseReason == CloseReason.UserClosing 
                        && MessageBox.Show(this, "Do you want to quit WebIM for Tray?", "Web Messenger", 
                        MessageBoxButtons.YesNo, MessageBoxIcon.Question) == DialogResult.No) {
                e.Cancel = true;
            }
        }

        private void webBrowser1_PreviewKeyDown(object sender, PreviewKeyDownEventArgs e) {
            if (e.KeyCode == Keys.Escape && this.Visible) {
                hideWindow();
            }
        }

        private void optionsMenu_Click(object sender, EventArgs e) {
            OptionsDialog dialog = new OptionsDialog();
            dialog.ShowDialog(this);

            // apply options
            if (Options.ShowInTaskBar != this.ShowInTaskbar)
                this.ShowInTaskbar = !this.ShowInTaskbar;
            setupReloadTimer();
        }

        private void toolNavigate_Click(object sender, EventArgs e) {
            navigateThere();
        }

        private void timer1_Tick(object sender, EventArgs e) {
            navigateThere();
        }

        private void MainWindow_Shown(object sender, EventArgs e) {
            if (Options.HideMainWindow) {
                hideWindow();
            }
        }

        private void setupReloadTimer() {
            int reloadSettings = (int)Options.ForceRefreshTime;
            if (reloadSettings != currentReloadTime) {
                if( currentReloadTime > 0 )
                    reloadPageTimer.Stop();

                if (reloadSettings != 0) {
                    reloadPageTimer.Interval = reloadSettings * 60 * 1000;
                    reloadPageTimer.Start();
                }

                currentReloadTime = reloadSettings;
            }
        }

        private int currentReloadTime = 0;

        private void toolHideWindow_Click(object sender, EventArgs e) {
            hideWindow();
        }

        protected override void OnSessionLock() {
            if (Options.DisconnectOnLock) {
                navigateBlank();
            }
        }

        protected override void OnSessionUnlock() {
            if (Options.DisconnectOnLock) {
                navigateThere();
            }
        }
    }
}
