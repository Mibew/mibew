package org.mibew.notifier;

import org.eclipse.swt.SWT;
import org.eclipse.swt.graphics.Image;
import org.eclipse.swt.widgets.*;
import org.eclipse.swt.widgets.Event;
import org.eclipse.swt.widgets.Menu;
import org.eclipse.swt.widgets.MenuItem;
import org.mibew.api.MibewAgent;
import org.mibew.api.MibewAgentListener;
import org.mibew.api.MibewThread;

import java.awt.*;
import java.io.IOException;

public class MibewTray extends MibewAgentListener {

    private volatile boolean isStopped = false;

	private Image fImageOn;
    private Image fImageOff;
    private TrayItem fItem;
    private Menu fMenu;
    private MibewAgent fAgent;

    void initTray(Display display, Shell shell, MibewAgent agent) {
        fAgent = agent;
        fImageOn = new Image(display, getClass().getClassLoader().getResourceAsStream("org/mibew/notifier/tray_on.png"));
        fImageOff = new Image(display, getClass().getClassLoader().getResourceAsStream("org/mibew/notifier/tray_off.png"));

		final Tray tray = display.getSystemTray();
		if (tray == null) {
			System.out.println("The system tray is not available");
		} else {
            fItem = new TrayItem(tray, SWT.NONE);
			fItem.setToolTipText("SWT TrayItem");
			fItem.addListener(SWT.Show, new Listener() {
                public void handleEvent(Event event) {
                    System.out.println("show");
                }
            });
			fItem.addListener(SWT.Hide, new Listener() {
                public void handleEvent(Event event) {
                    System.out.println("hide");
                }
            });
            fMenu = new Menu(shell, SWT.POP_UP);
			for (int i = 0; i < 8; i++) {
				MenuItem mi = new MenuItem(fMenu, SWT.PUSH);
				mi.setText("Item" + i);
				mi.addListener(SWT.Selection, new Listener() {
					public void handleEvent(Event event) {
						System.out.println("selection " + event.widget);
					}
				});
			}
            Listener listener = new Listener() {
                public void handleEvent(Event event) {
                    fMenu.setVisible(true);
                }
            };
            fItem.addListener(SWT.MenuDetect, listener);
            fItem.addListener(SWT.Selection, listener);
			fItem.setImage(fImageOff);
		}
		shell.setBounds(50, 50, 300, 200);
		//shell.open();
	}
	
	@Override
	protected synchronized void onlineStateChanged(final boolean isOnline) {
        if(isStopped)
            return;

        Display.getDefault().asyncExec(new Runnable() {
            public void run() {
                if(isStopped)
                    return;

                fItem.setImage(isOnline ? fImageOn : fImageOff);
            }
        });
	}
	
	@Override
	protected synchronized void updated(final MibewThread[] all, final MibewThread[] created) {
        if(isStopped)
            return;

        Display.getDefault().asyncExec(new Runnable() {
            public void run() {
                if(isStopped)
                    return;

                for (MenuItem menuItem : fMenu.getItems()) {
                    menuItem.dispose();
                }
                for(MibewThread m : all) {
                    MenuItem mi = new MenuItem(fMenu, SWT.PUSH);
                    mi.setText(m.getClientName());
                    mi.addListener(SWT.Selection, new LinkActionListener(null, fAgent.getOptions().getUrl() + "operator/agent.php?thread=" + m.getId()));
                }

                if(created.length == 1) {
                    fItem.setToolTipText(created[0].getClientName() + "\n" + created[0].getFirstMessage());
                } else if(created.length > 1) {
                    fItem.setToolTipText("New " + created.length + " visitors");
                }

            }
        });
    }
	
	synchronized void dispose() {
        isStopped = true;
        fItem.dispose();
		fImageOn.dispose();
        fImageOff.dispose();
	}

    private static class LinkActionListener implements Listener {
        private final Shell shell;
        private final String link;

		public LinkActionListener(Shell shell, String link) {
            this.shell = shell;
            this.link = link;
		}

		public void handleEvent(Event event) {
			try {
				BrowserUtil.openURL(link);
			} catch (IOException e1) {
                MessageBox messageBox = new MessageBox(shell, SWT.OK | SWT.ICON_ERROR);
                messageBox.setText("Browser error");	//$NON-NLS-1$
                messageBox.setMessage(e1.getMessage());
                messageBox.open();
			}
		}
	}}
