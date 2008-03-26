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
            this.groupBox1.Controls.Add(this.label2);
            this.groupBox1.Controls.Add(this.forceRefreshTime);
            this.groupBox1.Controls.Add(this.forceRefresh);
            this.groupBox1.Controls.Add(this.autoDesconnectOnSS);
            this.groupBox1.Controls.Add(this.webimServer);
            this.groupBox1.Controls.Add(this.label1);
            this.groupBox1.Controls.Add(this.autoDisconnect);
            this.groupBox1.Font = new System.Drawing.Font("Tahoma", 9.75F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.groupBox1.Location = new System.Drawing.Point(4, 4);
            this.groupBox1.Margin = new System.Windows.Forms.Padding(4);
            this.groupBox1.Name = "groupBox1";
            this.groupBox1.Padding = new System.Windows.Forms.Padding(16, 15, 16, 15);
            this.groupBox1.Size = new System.Drawing.Size(491, 250);
            this.groupBox1.TabIndex = 1;
            this.groupBox1.TabStop = false;
            this.groupBox1.Text = "Connection";
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.label2.Location = new System.Drawing.Point(319, 154);
            this.label2.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(57, 17);
            this.label2.TabIndex = 9;
            this.label2.Text = "minutes";
            // 
            // forceRefreshTime
            // 
            this.forceRefreshTime.Enabled = false;
            this.forceRefreshTime.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.forceRefreshTime.Location = new System.Drawing.Point(251, 151);
            this.forceRefreshTime.Margin = new System.Windows.Forms.Padding(4);
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
            this.forceRefreshTime.Size = new System.Drawing.Size(59, 23);
            this.forceRefreshTime.TabIndex = 8;
            this.forceRefreshTime.Value = new decimal(new int[] {
            15,
            0,
            0,
            0});
            this.forceRefreshTime.ValueChanged += new System.EventHandler(this.forceRefreshTime_Changed);
            // 
            // forceRefresh
            // 
            this.forceRefresh.AutoSize = true;
            this.forceRefresh.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.forceRefresh.Location = new System.Drawing.Point(23, 152);
            this.forceRefresh.Margin = new System.Windows.Forms.Padding(4);
            this.forceRefresh.Name = "forceRefresh";
            this.forceRefresh.Size = new System.Drawing.Size(187, 21);
            this.forceRefresh.TabIndex = 7;
            this.forceRefresh.Text = "Force page refresh every";
            this.forceRefresh.UseVisualStyleBackColor = true;
            this.forceRefresh.CheckedChanged += new System.EventHandler(this.forceRefresh_CheckedChanged);
            // 
            // autoDesconnectOnSS
            // 
            this.autoDesconnectOnSS.AutoSize = true;
            this.autoDesconnectOnSS.Enabled = false;
            this.autoDesconnectOnSS.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.autoDesconnectOnSS.Location = new System.Drawing.Point(23, 124);
            this.autoDesconnectOnSS.Margin = new System.Windows.Forms.Padding(4);
            this.autoDesconnectOnSS.Name = "autoDesconnectOnSS";
            this.autoDesconnectOnSS.Size = new System.Drawing.Size(280, 21);
            this.autoDesconnectOnSS.TabIndex = 6;
            this.autoDesconnectOnSS.Text = "Become idle if the screen saver is active";
            this.autoDesconnectOnSS.UseVisualStyleBackColor = true;
            // 
            // webimServer
            // 
            this.webimServer.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.webimServer.Location = new System.Drawing.Point(23, 56);
            this.webimServer.Margin = new System.Windows.Forms.Padding(4);
            this.webimServer.Name = "webimServer";
            this.webimServer.Size = new System.Drawing.Size(408, 23);
            this.webimServer.TabIndex = 5;
            this.webimServer.TextChanged += new System.EventHandler(this.webimServer_TextChanged);
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.label1.Location = new System.Drawing.Point(20, 35);
            this.label1.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(201, 17);
            this.label1.TabIndex = 4;
            this.label1.Text = "Web Instant Messenger server";
            // 
            // autoDisconnect
            // 
            this.autoDisconnect.AutoSize = true;
            this.autoDisconnect.Enabled = false;
            this.autoDisconnect.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.autoDisconnect.Location = new System.Drawing.Point(23, 96);
            this.autoDisconnect.Margin = new System.Windows.Forms.Padding(4);
            this.autoDisconnect.Name = "autoDisconnect";
            this.autoDisconnect.Size = new System.Drawing.Size(261, 21);
            this.autoDisconnect.TabIndex = 3;
            this.autoDisconnect.Text = "Become idle if the computer is locked";
            this.autoDisconnect.UseVisualStyleBackColor = true;
            // 
            // groupBox2
            // 
            this.groupBox2.Controls.Add(this.showUserPreferences);
            this.groupBox2.Font = new System.Drawing.Font("Tahoma", 9.75F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.groupBox2.Location = new System.Drawing.Point(4, 262);
            this.groupBox2.Margin = new System.Windows.Forms.Padding(4);
            this.groupBox2.Name = "groupBox2";
            this.groupBox2.Padding = new System.Windows.Forms.Padding(16, 15, 16, 15);
            this.groupBox2.Size = new System.Drawing.Size(491, 107);
            this.groupBox2.TabIndex = 2;
            this.groupBox2.TabStop = false;
            this.groupBox2.Text = "Operator preferences";
            // 
            // showUserPreferences
            // 
            this.showUserPreferences.AutoSize = true;
            this.showUserPreferences.Font = new System.Drawing.Font("Tahoma", 9F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.showUserPreferences.Location = new System.Drawing.Point(20, 33);
            this.showUserPreferences.Margin = new System.Windows.Forms.Padding(4, 0, 4, 0);
            this.showUserPreferences.Name = "showUserPreferences";
            this.showUserPreferences.Size = new System.Drawing.Size(296, 18);
            this.showUserPreferences.TabIndex = 0;
            this.showUserPreferences.TabStop = true;
            this.showUserPreferences.Text = "Click here to change your preferences online";
            this.showUserPreferences.LinkClicked += new System.Windows.Forms.LinkLabelLinkClickedEventHandler(this.showUserPropertiesOnline);
            // 
            // OptionsConnectionPanel
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(8F, 16F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.Controls.Add(this.groupBox2);
            this.Controls.Add(this.groupBox1);
            this.Margin = new System.Windows.Forms.Padding(4);
            this.Name = "OptionsConnectionPanel";
            this.Size = new System.Drawing.Size(499, 405);
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
