namespace webImTray {
    partial class MainWindow {
        private System.Windows.Forms.NotifyIcon notifyIcon;
        private System.ComponentModel.IContainer components;

        protected override void Dispose(bool disposing) {
            if (disposing) {
                if (components != null) {
                    components.Dispose();
                }
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code
        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent() {
            this.components = new System.ComponentModel.Container();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(MainWindow));
            this.notifyIcon = new System.Windows.Forms.NotifyIcon(this.components);
            this.notifyMenu = new System.Windows.Forms.ContextMenuStrip(this.components);
            this.optionsToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.toolStripMenuItem1 = new System.Windows.Forms.ToolStripSeparator();
            this.exitToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.toolbar = new System.Windows.Forms.ToolStrip();
            this.toolNavigate = new System.Windows.Forms.ToolStripButton();
            this.toolOptions = new System.Windows.Forms.ToolStripButton();
            this.toolHideWindow = new System.Windows.Forms.ToolStripButton();
            this.webBrowser1 = new System.Windows.Forms.WebBrowser();
            this.reloadPageTimer = new System.Windows.Forms.Timer(this.components);
            this.notifyMenu.SuspendLayout();
            this.toolbar.SuspendLayout();
            this.SuspendLayout();
            // 
            // notifyIcon
            // 
            this.notifyIcon.ContextMenuStrip = this.notifyMenu;
            this.notifyIcon.Icon = ((System.Drawing.Icon)(resources.GetObject("notifyIcon.Icon")));
            this.notifyIcon.Text = "Web Messenger Tray";
            this.notifyIcon.Visible = true;
            this.notifyIcon.MouseDown += new System.Windows.Forms.MouseEventHandler(this.notifyIconClick);
            // 
            // notifyMenu
            // 
            this.notifyMenu.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.optionsToolStripMenuItem,
            this.toolStripMenuItem1,
            this.exitToolStripMenuItem});
            this.notifyMenu.Name = "notifyMenu";
            this.notifyMenu.Size = new System.Drawing.Size(144, 58);
            // 
            // optionsToolStripMenuItem
            // 
            this.optionsToolStripMenuItem.Image = ((System.Drawing.Image)(resources.GetObject("optionsToolStripMenuItem.Image")));
            this.optionsToolStripMenuItem.ImageTransparentColor = System.Drawing.Color.Silver;
            this.optionsToolStripMenuItem.Name = "optionsToolStripMenuItem";
            this.optionsToolStripMenuItem.Size = new System.Drawing.Size(143, 24);
            this.optionsToolStripMenuItem.Text = "Options..";
            this.optionsToolStripMenuItem.Click += new System.EventHandler(this.optionsMenu_Click);
            // 
            // toolStripMenuItem1
            // 
            this.toolStripMenuItem1.Name = "toolStripMenuItem1";
            this.toolStripMenuItem1.Size = new System.Drawing.Size(140, 6);
            // 
            // exitToolStripMenuItem
            // 
            this.exitToolStripMenuItem.Name = "exitToolStripMenuItem";
            this.exitToolStripMenuItem.Size = new System.Drawing.Size(143, 24);
            this.exitToolStripMenuItem.Text = "E&xit";
            this.exitToolStripMenuItem.Click += new System.EventHandler(this.menuExitClick);
            // 
            // toolbar
            // 
            this.toolbar.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.toolNavigate,
            this.toolOptions,
            this.toolHideWindow});
            this.toolbar.Location = new System.Drawing.Point(0, 0);
            this.toolbar.Name = "toolbar";
            this.toolbar.Size = new System.Drawing.Size(864, 26);
            this.toolbar.TabIndex = 1;
            this.toolbar.Text = "toolStrip1";
            // 
            // toolNavigate
            // 
            this.toolNavigate.DisplayStyle = System.Windows.Forms.ToolStripItemDisplayStyle.Image;
            this.toolNavigate.Image = ((System.Drawing.Image)(resources.GetObject("toolNavigate.Image")));
            this.toolNavigate.ImageTransparentColor = System.Drawing.Color.Silver;
            this.toolNavigate.Name = "toolNavigate";
            this.toolNavigate.Size = new System.Drawing.Size(23, 23);
            this.toolNavigate.Text = "Show pending users";
            this.toolNavigate.Click += new System.EventHandler(this.toolNavigate_Click);
            // 
            // toolOptions
            // 
            this.toolOptions.DisplayStyle = System.Windows.Forms.ToolStripItemDisplayStyle.Image;
            this.toolOptions.Image = ((System.Drawing.Image)(resources.GetObject("toolOptions.Image")));
            this.toolOptions.ImageTransparentColor = System.Drawing.Color.Silver;
            this.toolOptions.Name = "toolOptions";
            this.toolOptions.Size = new System.Drawing.Size(23, 23);
            this.toolOptions.Text = "Options..";
            this.toolOptions.Click += new System.EventHandler(this.optionsMenu_Click);
            // 
            // toolHideWindow
            // 
            this.toolHideWindow.Alignment = System.Windows.Forms.ToolStripItemAlignment.Right;
            this.toolHideWindow.DisplayStyle = System.Windows.Forms.ToolStripItemDisplayStyle.Text;
            this.toolHideWindow.Image = ((System.Drawing.Image)(resources.GetObject("toolHideWindow.Image")));
            this.toolHideWindow.ImageTransparentColor = System.Drawing.Color.Magenta;
            this.toolHideWindow.Name = "toolHideWindow";
            this.toolHideWindow.Size = new System.Drawing.Size(105, 23);
            this.toolHideWindow.Text = "Hide window";
            this.toolHideWindow.Click += new System.EventHandler(this.toolHideWindow_Click);
            // 
            // webBrowser1
            // 
            this.webBrowser1.Dock = System.Windows.Forms.DockStyle.Fill;
            this.webBrowser1.Location = new System.Drawing.Point(0, 26);
            this.webBrowser1.MinimumSize = new System.Drawing.Size(20, 20);
            this.webBrowser1.Name = "webBrowser1";
            this.webBrowser1.Size = new System.Drawing.Size(864, 459);
            this.webBrowser1.TabIndex = 2;
            this.webBrowser1.PreviewKeyDown += new System.Windows.Forms.PreviewKeyDownEventHandler(this.webBrowser1_PreviewKeyDown);
            // 
            // reloadPageTimer
            // 
            this.reloadPageTimer.Interval = 5000;
            this.reloadPageTimer.Tick += new System.EventHandler(this.timer1_Tick);
            // 
            // MainWindow
            // 
            this.AutoScaleBaseSize = new System.Drawing.Size(5, 13);
            this.ClientSize = new System.Drawing.Size(864, 485);
            this.Controls.Add(this.webBrowser1);
            this.Controls.Add(this.toolbar);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MinimizeBox = false;
            this.Name = "MainWindow";
            this.ShowInTaskbar = false;
            this.Text = "Internet Services - Web Messenger";
            this.Shown += new System.EventHandler(this.MainWindow_Shown);
            this.FormClosing += new System.Windows.Forms.FormClosingEventHandler(this.Client_FormClosing);
            this.GotFocus += new System.EventHandler(this.gotFocus);
            this.notifyMenu.ResumeLayout(false);
            this.toolbar.ResumeLayout(false);
            this.toolbar.PerformLayout();
            this.ResumeLayout(false);
            this.PerformLayout();

        }
        #endregion

        private System.Windows.Forms.ToolStrip toolbar;
        private System.Windows.Forms.ToolStripButton toolNavigate;
        private System.Windows.Forms.WebBrowser webBrowser1;
        private System.Windows.Forms.Timer reloadPageTimer;
        private System.Windows.Forms.ToolStripButton toolOptions;
        private System.Windows.Forms.ContextMenuStrip notifyMenu;
        private System.Windows.Forms.ToolStripMenuItem optionsToolStripMenuItem;
        private System.Windows.Forms.ToolStripSeparator toolStripMenuItem1;
        private System.Windows.Forms.ToolStripMenuItem exitToolStripMenuItem;
        private System.Windows.Forms.ToolStripButton toolHideWindow;

    }
}
