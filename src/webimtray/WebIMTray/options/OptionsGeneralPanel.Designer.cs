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
            this.groupBox1.Font = new System.Drawing.Font("Tahoma", 9.75F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.groupBox1.Location = new System.Drawing.Point(3, 3);
            this.groupBox1.Name = "groupBox1";
            this.groupBox1.Padding = new System.Windows.Forms.Padding(12);
            this.groupBox1.Size = new System.Drawing.Size(368, 106);
            this.groupBox1.TabIndex = 0;
            this.groupBox1.TabStop = false;
            this.groupBox1.Text = "Application";
            // 
            // hideWhenStarted
            // 
            this.hideWhenStarted.AutoSize = true;
            this.hideWhenStarted.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.hideWhenStarted.Location = new System.Drawing.Point(15, 76);
            this.hideWhenStarted.Name = "hideWhenStarted";
            this.hideWhenStarted.Size = new System.Drawing.Size(134, 17);
            this.hideWhenStarted.TabIndex = 3;
            this.hideWhenStarted.Text = "Hide window after start";
            this.hideWhenStarted.UseVisualStyleBackColor = true;
            this.hideWhenStarted.CheckedChanged += new System.EventHandler(this.checkboxChanged);
            // 
            // autoStart
            // 
            this.autoStart.AutoSize = true;
            this.autoStart.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.autoStart.Location = new System.Drawing.Point(15, 53);
            this.autoStart.Name = "autoStart";
            this.autoStart.Size = new System.Drawing.Size(225, 17);
            this.autoStart.TabIndex = 1;
            this.autoStart.Text = "Start automatically when starting Windows";
            this.autoStart.UseVisualStyleBackColor = true;
            this.autoStart.CheckedChanged += new System.EventHandler(this.checkboxChanged);
            // 
            // showInTaskBar
            // 
            this.showInTaskBar.AutoSize = true;
            this.showInTaskBar.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.showInTaskBar.Location = new System.Drawing.Point(15, 30);
            this.showInTaskBar.Name = "showInTaskBar";
            this.showInTaskBar.Size = new System.Drawing.Size(103, 17);
            this.showInTaskBar.TabIndex = 0;
            this.showInTaskBar.Text = "Show In taskbar";
            this.showInTaskBar.UseVisualStyleBackColor = true;
            this.showInTaskBar.CheckedChanged += new System.EventHandler(this.checkboxChanged);
            // 
            // groupBox2
            // 
            this.groupBox2.Controls.Add(this.textBox2);
            this.groupBox2.Controls.Add(this.textBox1);
            this.groupBox2.Controls.Add(this.showOptions);
            this.groupBox2.Controls.Add(this.showHide);
            this.groupBox2.Font = new System.Drawing.Font("Tahoma", 9.75F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.groupBox2.Location = new System.Drawing.Point(3, 115);
            this.groupBox2.Name = "groupBox2";
            this.groupBox2.Padding = new System.Windows.Forms.Padding(12);
            this.groupBox2.Size = new System.Drawing.Size(368, 89);
            this.groupBox2.TabIndex = 1;
            this.groupBox2.TabStop = false;
            this.groupBox2.Text = "Hotkeys";
            // 
            // textBox2
            // 
            this.textBox2.Enabled = false;
            this.textBox2.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.textBox2.Location = new System.Drawing.Point(167, 52);
            this.textBox2.Name = "textBox2";
            this.textBox2.Size = new System.Drawing.Size(100, 20);
            this.textBox2.TabIndex = 3;
            // 
            // textBox1
            // 
            this.textBox1.Enabled = false;
            this.textBox1.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.textBox1.Location = new System.Drawing.Point(167, 29);
            this.textBox1.Name = "textBox1";
            this.textBox1.Size = new System.Drawing.Size(100, 20);
            this.textBox1.TabIndex = 2;
            // 
            // showOptions
            // 
            this.showOptions.AutoSize = true;
            this.showOptions.Enabled = false;
            this.showOptions.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.showOptions.Location = new System.Drawing.Point(15, 54);
            this.showOptions.Name = "showOptions";
            this.showOptions.Size = new System.Drawing.Size(90, 17);
            this.showOptions.TabIndex = 1;
            this.showOptions.Text = "Show options";
            this.showOptions.UseVisualStyleBackColor = true;
            this.showOptions.CheckedChanged += new System.EventHandler(this.checkboxChanged);
            // 
            // showHide
            // 
            this.showHide.AutoSize = true;
            this.showHide.Enabled = false;
            this.showHide.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.showHide.Location = new System.Drawing.Point(15, 31);
            this.showHide.Name = "showHide";
            this.showHide.Size = new System.Drawing.Size(147, 17);
            this.showHide.TabIndex = 0;
            this.showHide.Text = "Show/Hide main window:";
            this.showHide.UseVisualStyleBackColor = true;
            this.showHide.CheckedChanged += new System.EventHandler(this.checkboxChanged);
            // 
            // languageBox
            // 
            this.languageBox.Controls.Add(this.radioRussian);
            this.languageBox.Controls.Add(this.radioEnglish);
            this.languageBox.Font = new System.Drawing.Font("Tahoma", 9.75F, System.Drawing.FontStyle.Bold);
            this.languageBox.Location = new System.Drawing.Point(3, 210);
            this.languageBox.Name = "languageBox";
            this.languageBox.Size = new System.Drawing.Size(371, 53);
            this.languageBox.TabIndex = 2;
            this.languageBox.TabStop = false;
            this.languageBox.Text = "Language";
            // 
            // radioRussian
            // 
            this.radioRussian.AutoSize = true;
            this.radioRussian.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F);
            this.radioRussian.Location = new System.Drawing.Point(80, 30);
            this.radioRussian.Name = "radioRussian";
            this.radioRussian.Size = new System.Drawing.Size(63, 17);
            this.radioRussian.TabIndex = 1;
            this.radioRussian.TabStop = true;
            this.radioRussian.Text = "Russian";
            this.radioRussian.UseVisualStyleBackColor = true;
            // 
            // radioEnglish
            // 
            this.radioEnglish.AutoSize = true;
            this.radioEnglish.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F);
            this.radioEnglish.Location = new System.Drawing.Point(15, 30);
            this.radioEnglish.Name = "radioEnglish";
            this.radioEnglish.Size = new System.Drawing.Size(59, 17);
            this.radioEnglish.TabIndex = 0;
            this.radioEnglish.TabStop = true;
            this.radioEnglish.Text = "English";
            this.radioEnglish.UseVisualStyleBackColor = true;
            this.radioEnglish.CheckedChanged += new System.EventHandler(this.radioEnglish_CheckedChanged);
            // 
            // OptionsGeneralPanel
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.Controls.Add(this.languageBox);
            this.Controls.Add(this.groupBox2);
            this.Controls.Add(this.groupBox1);
            this.Name = "OptionsGeneralPanel";
            this.Size = new System.Drawing.Size(374, 329);
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
