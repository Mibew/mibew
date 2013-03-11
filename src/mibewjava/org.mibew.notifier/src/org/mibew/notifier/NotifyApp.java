package org.mibew.notifier;

import org.eclipse.swt.widgets.Display;
import org.eclipse.swt.widgets.Shell;
import org.mibew.api.MibewAgent;
import org.mibew.notifier.Options.JOptions;

public class NotifyApp {

	public static void main(String[] args) {
        Display display = new Display();
        Shell shell = new Shell(display);

        Options options = new JOptions(shell, args);
		if (!options.load()) {
			return;
		}

		MibewTray tray = new MibewTray();
		MibewAgent agent = new MibewAgent(options.getAgentOptions(), tray);
		agent.launch();

		tray.initTray(display, shell, agent);
	
		while (!shell.isDisposed()) {
			if (!display.readAndDispatch())
				display.sleep();
		}
        tray.dispose();
        agent.stop();
		display.dispose();
        System.exit(0);
	}
}
