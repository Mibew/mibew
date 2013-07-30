using System;

namespace webImTray {
    partial class OptionsGeneralPanel {
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(OptionsGeneralPanel));
            this.groupBox1 = new System.Windows.Forms.GroupBox();
            this.hideWhenStarted = new System.Windows.Forms.CheckBox();
            this.autoStart = new System.Windows.Forms.CheckBox();
            this.showInTaskBar = new System.Windows.Forms.CheckBox();
            this.languageBox = new System.Windows.Forms.GroupBox();
            this.languageSelector = new System.Windows.Forms.ComboBox();
            this.groupBox1.SuspendLayout();
            this.languageBox.SuspendLayout();
            this.SuspendLayout();
            // 
            // groupBox1
            // 
            this.groupBox1.Controls.Add(this.hideWhenStarted);
            this.groupBox1.Controls.Add(this.autoStart);
            this.groupBox1.Controls.Add(this.showInTaskBar);
            resources.ApplyResources(this.groupBox1, "groupBox1");
            this.groupBox1.Name = "groupBox1";
            this.groupBox1.TabStop = false;
            // 
            // hideWhenStarted
            // 
            resources.ApplyResources(this.hideWhenStarted, "hideWhenStarted");
            this.hideWhenStarted.Name = "hideWhenStarted";
            this.hideWhenStarted.UseVisualStyleBackColor = true;
            this.hideWhenStarted.CheckedChanged += new System.EventHandler(this.checkboxChanged);
            // 
            // autoStart
            // 
            resources.ApplyResources(this.autoStart, "autoStart");
            this.autoStart.Name = "autoStart";
            this.autoStart.UseVisualStyleBackColor = true;
            this.autoStart.CheckedChanged += new System.EventHandler(this.checkboxChanged);
            // 
            // showInTaskBar
            // 
            resources.ApplyResources(this.showInTaskBar, "showInTaskBar");
            this.showInTaskBar.Name = "showInTaskBar";
            this.showInTaskBar.UseVisualStyleBackColor = true;
            this.showInTaskBar.CheckedChanged += new System.EventHandler(this.checkboxChanged);
            // 
            // languageBox
            // 
            this.languageBox.Controls.Add(this.languageSelector);
            resources.ApplyResources(this.languageBox, "languageBox");
            this.languageBox.Name = "languageBox";
            this.languageBox.TabStop = false;
            // 
            // languageSelector
            // 
            this.languageSelector.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            resources.ApplyResources(this.languageSelector, "languageSelector");
            this.languageSelector.FormattingEnabled = true;
            this.languageSelector.Name = "languageSelector";
            // 
            // OptionsGeneralPanel
            // 
            resources.ApplyResources(this, "$this");
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.Controls.Add(this.languageBox);
            this.Controls.Add(this.groupBox1);
            this.Name = "OptionsGeneralPanel";
            this.groupBox1.ResumeLayout(false);
            this.groupBox1.PerformLayout();
            this.languageBox.ResumeLayout(false);
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.GroupBox groupBox1;
        private System.Windows.Forms.CheckBox showInTaskBar;
        private System.Windows.Forms.CheckBox autoStart;
        private System.Windows.Forms.CheckBox hideWhenStarted;
        private System.Windows.Forms.GroupBox languageBox;
        private System.Windows.Forms.ComboBox languageSelector;
    }
}
