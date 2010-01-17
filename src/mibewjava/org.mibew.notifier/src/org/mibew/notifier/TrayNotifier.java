package org.mibew.notifier;

import java.awt.AWTException;
import java.awt.Image;
import java.awt.MenuItem;
import java.awt.MenuShortcut;
import java.awt.PopupMenu;
import java.awt.SystemTray;
import java.awt.Toolkit;
import java.awt.TrayIcon;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.KeyEvent;
import java.net.URL;

public class TrayNotifier {

	private TrayIcon trayIcon;

	public TrayNotifier() {
	}

	public void init() {
		if (SystemTray.isSupported()) {

			SystemTray tray = SystemTray.getSystemTray();
			URL url = this.getClass().getResource("tray.png");
			Image image = Toolkit.getDefaultToolkit().getImage(url);

			PopupMenu popup = new PopupMenu();
			MenuItem exitItem = new MenuItem("Exit", new MenuShortcut(KeyEvent.VK_X));
			exitItem.addActionListener(new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					System.exit(0);
				}
			});
			
			popup.add(exitItem);
			trayIcon = new TrayIcon(image, "Mibew Notifier", popup);
			trayIcon.setImageAutoSize(true);

			try {
				tray.add(trayIcon);
			} catch (AWTException e) {
				System.err.println("TrayIcon could not be added.");
				System.exit(1);
			}
		} else {
			System.err.println("TrayIcon could not be added.");
			System.exit(1);
		}
	}
	
	public void setStatus(boolean online) {
	}
}
