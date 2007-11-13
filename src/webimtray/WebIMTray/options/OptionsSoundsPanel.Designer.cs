namespace webImTray {
    partial class OptionsSoundsPanel {
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
            this.playSoundOnVisitor = new System.Windows.Forms.CheckBox();
            this.groupBox1.SuspendLayout();
            this.SuspendLayout();
            // 
            // groupBox1
            // 
            this.groupBox1.Controls.Add(this.playSoundOnVisitor);
            this.groupBox1.Font = new System.Drawing.Font("Tahoma", 9.75F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.groupBox1.Location = new System.Drawing.Point(3, 3);
            this.groupBox1.Name = "groupBox1";
            this.groupBox1.Padding = new System.Windows.Forms.Padding(12);
            this.groupBox1.Size = new System.Drawing.Size(368, 156);
            this.groupBox1.TabIndex = 2;
            this.groupBox1.TabStop = false;
            this.groupBox1.Text = "Notifications";
            // 
            // playSoundOnVisitor
            // 
            this.playSoundOnVisitor.AutoSize = true;
            this.playSoundOnVisitor.Enabled = false;
            this.playSoundOnVisitor.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(204)));
            this.playSoundOnVisitor.Location = new System.Drawing.Point(15, 28);
            this.playSoundOnVisitor.Name = "playSoundOnVisitor";
            this.playSoundOnVisitor.Size = new System.Drawing.Size(171, 17);
            this.playSoundOnVisitor.TabIndex = 0;
            this.playSoundOnVisitor.Text = "Play sound when visitor comes";
            this.playSoundOnVisitor.UseVisualStyleBackColor = true;
            // 
            // OptionsSoundsPanel
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.Controls.Add(this.groupBox1);
            this.Name = "OptionsSoundsPanel";
            this.Size = new System.Drawing.Size(374, 329);
            this.Load += new System.EventHandler(this.OptionsSoundsPanel_Load);
            this.groupBox1.ResumeLayout(false);
            this.groupBox1.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.GroupBox groupBox1;
        private System.Windows.Forms.CheckBox playSoundOnVisitor;
    }
}
