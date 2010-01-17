package org.mibew.notifier;

import java.awt.AWTException;
import java.awt.Image;
import java.awt.MenuItem;
import java.awt.MenuShortcut;
import java.awt.PopupMenu;
import java.awt.SystemTray;
import java.awt.Toolkit;
import java.awt.TrayIcon;
import java.awt.TrayIcon.MessageType;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.KeyEvent;

import org.mibew.api.MibewAgent;
import org.mibew.api.MibewAgentListener;
import org.mibew.api.MibewThread;

public class TrayNotifier extends MibewAgentListener {

	private TrayIcon trayIcon;
	private MibewAgent agent;
	
	private Image online;
	private Image offline;
	private ActionListener fExit;

	public TrayNotifier() {
	}

	public void init() {
		if (SystemTray.isSupported()) {

			SystemTray tray = SystemTray.getSystemTray();
			online = Toolkit.getDefaultToolkit().getImage(this.getClass().getResource("tray_on.png"));
			offline = Toolkit.getDefaultToolkit().getImage(this.getClass().getResource("tray_off.png"));
			fExit = new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					if(agent != null) {
						agent.stop();
					}
					System.exit(0);
				}
			};
			
			PopupMenu pm = new PopupMenu();
			MenuItem exitItem = new MenuItem("Exit", new MenuShortcut(KeyEvent.VK_X));
			exitItem.addActionListener(fExit);
			pm.add(exitItem);
			trayIcon = new TrayIcon(offline, "Mibew Notifier", pm);
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
		PopupMenu pm = new PopupMenu();
		for(MibewThread mt : all) {
			MenuItem mi = new MenuItem(mt.fClientName);
			pm.add(mi);
		}
		if(all.length > 0) {
			pm.addSeparator();
		}
		MenuItem exitItem = new MenuItem("Exit", new MenuShortcut(KeyEvent.VK_X));
		exitItem.addActionListener(fExit);
		pm.add(exitItem);
		trayIcon.setPopupMenu(pm);
		
		if(created.length == 1) {
			trayIcon.displayMessage("New visitor", created[0].fClientName + "\n" + created[0].fFirstMessage, MessageType.INFO);
		} else if(created.length > 1) {
			trayIcon.displayMessage("New visitors", "New " + created.length + " visitors", MessageType.INFO);
		}
	}
	
	public void setAgent(MibewAgent agent) {
		this.agent = agent;
	}
}
