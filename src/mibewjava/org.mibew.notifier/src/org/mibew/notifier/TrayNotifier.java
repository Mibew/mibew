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

import org.mibew.api.MibewAgent;
import org.mibew.api.MibewAgentListener;
import org.mibew.api.MibewThread;

public class TrayNotifier extends MibewAgentListener {

	private TrayIcon trayIcon;
	private MibewAgent agent;
	
	private Image online;
	private Image offline;

	public TrayNotifier() {
	}

	public void init() {
		if (SystemTray.isSupported()) {

			SystemTray tray = SystemTray.getSystemTray();
			online = Toolkit.getDefaultToolkit().getImage(this.getClass().getResource("tray_on.png"));
			offline = Toolkit.getDefaultToolkit().getImage(this.getClass().getResource("tray_off.png"));

			PopupMenu popup = new PopupMenu();
			MenuItem exitItem = new MenuItem("Exit", new MenuShortcut(KeyEvent.VK_X));
			exitItem.addActionListener(new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					if(agent != null) {
						agent.stop();
					}
					System.exit(0);
				}
			});
			
			popup.add(exitItem);
			trayIcon = new TrayIcon(offline, "Mibew Notifier", popup);
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

	@Override
	protected void onlineStateChanged(boolean isOnline) {
		trayIcon.setImage(isOnline ? online : offline);
	}

	@Override
	protected void updated(MibewThread[] all, MibewThread[] created) {
	}
	
	public void setAgent(MibewAgent agent) {
		this.agent = agent;
	}
}
