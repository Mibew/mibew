namespace webImTray {
    partial class OptionsConnectionPanel {
        /// <summary> 
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary> 
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing) {
            if (disposing && (components != null)) {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Component Designer generated code

        /// <summary> 
        /// Required method for Designer support - do not modify 
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent() {
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(OptionsConnectionPanel));
            this.groupBox1 = new System.Windows.Forms.GroupBox();
            this.label2 = new System.Windows.Forms.Label();
            this.forceRefreshTime = new System.Windows.Forms.NumericUpDown();
            this.forceRefresh = new System.Windows.Forms.CheckBox();
            this.autoDesconnectOnSS = new System.Windows.Forms.CheckBox();
            this.webimServer = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.autoDisconnect = new System.Windows.Forms.CheckBox();
            this.groupBox2 = new System.Windows.Forms.GroupBox();
            this.showUserPreferences = new System.Windows.Forms.LinkLabel();
            this.groupBox1.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.forceRefreshTime)).BeginInit();
            this.groupBox2.SuspendLayout();
            this.SuspendLayout();
            // 
            // groupBox1
            // 
            this.groupBox1.AccessibleDescription = null;
            this.groupBox1.AccessibleName = null;
            resources.ApplyResources(this.groupBox1, "groupBox1");
            this.groupBox1.BackgroundImage = null;
            this.groupBox1.Controls.Add(this.label2);
            this.groupBox1.Controls.Add(this.forceRefreshTime);
            this.groupBox1.Controls.Add(this.forceRefresh);
            this.groupBox1.Controls.Add(this.autoDesconnectOnSS);
            this.groupBox1.Controls.Add(this.webimServer);
            this.groupBox1.Controls.Add(this.label1);
            this.groupBox1.Controls.Add(this.autoDisconnect);
            this.groupBox1.Name = "groupBox1";
            this.groupBox1.TabStop = false;
            // 
            // label2
            // 
            this.label2.AccessibleDescription = null;
            this.label2.AccessibleName = null;
            resources.ApplyResources(this.label2, "label2");
            this.label2.Name = "label2";
            // 
            // forceRefreshTime
            // 
            this.forceRefreshTime.AccessibleDescription = null;
            this.forceRefreshTime.AccessibleName = null;
            resources.ApplyResources(this.forceRefreshTime, "forceRefreshTime");
            this.forceRefreshTime.Maximum = new decimal(new int[] {
            120,
            0,
            0,
            0});
            this.forceRefreshTime.Minimum = new decimal(new int[] {
            5,
            0,
            0,
            0});
            this.forceRefreshTime.Name = "forceRefreshTime";
            this.forceRefreshTime.Value = new decimal(new int[] {
            15,
            0,
            0,
            0});
            this.forceRefreshTime.ValueChanged += new System.EventHandler(this.forceRefreshTime_Changed);
            // 
            // forceRefresh
            // 
            this.forceRefresh.AccessibleDescription = null;
            this.forceRefresh.AccessibleName = null;
            resources.ApplyResources(this.forceRefresh, "forceRefresh");
            this.forceRefresh.BackgroundImage = null;
            this.forceRefresh.Name = "forceRefresh";
            this.forceRefresh.UseVisualStyleBackColor = true;
            this.forceRefresh.CheckedChanged += new System.EventHandler(this.forceRefresh_CheckedChanged);
            // 
            // autoDesconnectOnSS
            // 
            this.autoDesconnectOnSS.AccessibleDescription = null;
            this.autoDesconnectOnSS.AccessibleName = null;
            resources.ApplyResources(this.autoDesconnectOnSS, "autoDesconnectOnSS");
            this.autoDesconnectOnSS.BackgroundImage = null;
            this.autoDesconnectOnSS.Name = "autoDesconnectOnSS";
            this.autoDesconnectOnSS.UseVisualStyleBackColor = true;
            // 
            // webimServer
            // 
            this.webimServer.AccessibleDescription = null;
            this.webimServer.AccessibleName = null;
            resources.ApplyResources(this.webimServer, "webimServer");
            this.webimServer.BackgroundImage = null;
            this.webimServer.Name = "webimServer";
            this.webimServer.TextChanged += new System.EventHandler(this.webimServer_TextChanged);
            // 
            // label1
            // 
            this.label1.AccessibleDescription = null;
            this.label1.AccessibleName = null;
            resources.ApplyResources(this.label1, "label1");
            this.label1.Name = "label1";
            // 
            // autoDisconnect
            // 
            this.autoDisconnect.AccessibleDescription = null;
            this.autoDisconnect.AccessibleName = null;
            resources.ApplyResources(this.autoDisconnect, "autoDisconnect");
            this.autoDisconnect.BackgroundImage = null;
            this.autoDisconnect.Name = "autoDisconnect";
            this.autoDisconnect.UseVisualStyleBackColor = true;
            this.autoDisconnect.CheckedChanged += new System.EventHandler(this.autoDisconnect_CheckedChanged);
            // 
            // groupBox2
            // 
            this.groupBox2.AccessibleDescription = null;
            this.groupBox2.AccessibleName = null;
            resources.ApplyResources(this.groupBox2, "groupBox2");
            this.groupBox2.BackgroundImage = null;
            this.groupBox2.Controls.Add(this.showUserPreferences);
            this.groupBox2.Name = "groupBox2";
            this.groupBox2.TabStop = false;
            // 
            // showUserPreferences
            // 
            this.showUserPreferences.AccessibleDescription = null;
            this.showUserPreferences.AccessibleName = null;
            resources.ApplyResources(this.showUserPreferences, "showUserPreferences");
            this.showUserPreferences.Name = "showUserPreferences";
            this.showUserPreferences.TabStop = true;
            this.showUserPreferences.LinkClicked += new System.Windows.Forms.LinkLabelLinkClickedEventHandler(this.showUserPropertiesOnline);
            // 
            // OptionsConnectionPanel
            // 
            this.AccessibleDescription = null;
            this.AccessibleName = null;
            resources.ApplyResources(this, "$this");
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.BackgroundImage = null;
            this.Controls.Add(this.groupBox2);
            this.Controls.Add(this.groupBox1);
            this.Font = null;
            this.Name = "OptionsConnectionPanel";
            this.groupBox1.ResumeLayout(false);
            this.groupBox1.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.forceRefreshTime)).EndInit();
            this.groupBox2.ResumeLayout(false);
            this.groupBox2.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.GroupBox groupBox1;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.CheckBox autoDisconnect;
        private System.Windows.Forms.CheckBox autoDesconnectOnSS;
        private System.Windows.Forms.TextBox webimServer;
        private System.Windows.Forms.GroupBox groupBox2;
        private System.Windows.Forms.LinkLabel showUserPreferences;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.NumericUpDown forceRefreshTime;
        private System.Windows.Forms.CheckBox forceRefresh;
    }
}
