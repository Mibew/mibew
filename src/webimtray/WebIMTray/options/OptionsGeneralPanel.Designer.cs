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
            this.groupBox2 = new System.Windows.Forms.GroupBox();
            this.textBox2 = new System.Windows.Forms.TextBox();
            this.textBox1 = new System.Windows.Forms.TextBox();
            this.showOptions = new System.Windows.Forms.CheckBox();
            this.showHide = new System.Windows.Forms.CheckBox();
            this.languageBox = new System.Windows.Forms.GroupBox();
            this.radioRussian = new System.Windows.Forms.RadioButton();
            this.radioEnglish = new System.Windows.Forms.RadioButton();
            this.groupBox1.SuspendLayout();
            this.groupBox2.SuspendLayout();
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
            // groupBox2
            // 
            this.groupBox2.Controls.Add(this.textBox2);
            this.groupBox2.Controls.Add(this.textBox1);
            this.groupBox2.Controls.Add(this.showOptions);
            this.groupBox2.Controls.Add(this.showHide);
            resources.ApplyResources(this.groupBox2, "groupBox2");
            this.groupBox2.Name = "groupBox2";
            this.groupBox2.TabStop = false;
            // 
            // textBox2
            // 
            resources.ApplyResources(this.textBox2, "textBox2");
            this.textBox2.Name = "textBox2";
            // 
            // textBox1
            // 
            resources.ApplyResources(this.textBox1, "textBox1");
            this.textBox1.Name = "textBox1";
            // 
            // showOptions
            // 
            resources.ApplyResources(this.showOptions, "showOptions");
            this.showOptions.Name = "showOptions";
            this.showOptions.UseVisualStyleBackColor = true;
            this.showOptions.CheckedChanged += new System.EventHandler(this.checkboxChanged);
            // 
            // showHide
            // 
            resources.ApplyResources(this.showHide, "showHide");
            this.showHide.Name = "showHide";
            this.showHide.UseVisualStyleBackColor = true;
            this.showHide.CheckedChanged += new System.EventHandler(this.checkboxChanged);
            // 
            // languageBox
            // 
            this.languageBox.Controls.Add(this.radioRussian);
            this.languageBox.Controls.Add(this.radioEnglish);
            resources.ApplyResources(this.languageBox, "languageBox");
            this.languageBox.Name = "languageBox";
            this.languageBox.TabStop = false;
            // 
            // radioRussian
            // 
            resources.ApplyResources(this.radioRussian, "radioRussian");
            this.radioRussian.Checked = true;
            this.radioRussian.Name = "radioRussian";
            this.radioRussian.TabStop = true;
            this.radioRussian.UseVisualStyleBackColor = true;
            this.radioRussian.CheckedChanged += new System.EventHandler(this.radioRussian_CheckedChanged);
            // 
            // radioEnglish
            // 
            resources.ApplyResources(this.radioEnglish, "radioEnglish");
            this.radioEnglish.Name = "radioEnglish";
            this.radioEnglish.UseVisualStyleBackColor = true;
            this.radioEnglish.CheckedChanged += new System.EventHandler(this.radioEnglish_CheckedChanged);
            // 
            // OptionsGeneralPanel
            // 
            resources.ApplyResources(this, "$this");
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.Controls.Add(this.languageBox);
            this.Controls.Add(this.groupBox2);
            this.Controls.Add(this.groupBox1);
            this.Name = "OptionsGeneralPanel";
            this.groupBox1.ResumeLayout(false);
            this.groupBox1.PerformLayout();
            this.groupBox2.ResumeLayout(false);
            this.groupBox2.PerformLayout();
            this.languageBox.ResumeLayout(false);
            this.languageBox.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.GroupBox groupBox1;
        private System.Windows.Forms.CheckBox showInTaskBar;
        private System.Windows.Forms.CheckBox autoStart;
        private System.Windows.Forms.CheckBox hideWhenStarted;
        private System.Windows.Forms.GroupBox groupBox2;
        private System.Windows.Forms.CheckBox showOptions;
        private System.Windows.Forms.CheckBox showHide;
        private System.Windows.Forms.TextBox textBox2;
        private System.Windows.Forms.TextBox textBox1;
        private System.Windows.Forms.GroupBox languageBox;
        private System.Windows.Forms.RadioButton radioRussian;
        private System.Windows.Forms.RadioButton radioEnglish;
    }
}
