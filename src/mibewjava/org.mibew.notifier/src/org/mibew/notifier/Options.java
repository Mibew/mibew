package org.mibew.notifier;

import org.eclipse.swt.SWT;
import org.eclipse.swt.widgets.MessageBox;
import org.eclipse.swt.widgets.Shell;
import org.mibew.api.MibewAgentOptions;

import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

public class Options {

	private MibewAgentOptions agentOptions;
	private Properties myProperties;

	public Options(String[] args) {
	}

	public boolean load() {
		try {
			InputStream is = getClass().getClassLoader().getResourceAsStream("mibew.ini");
			if (is != null) {
				myProperties = new Properties();
				myProperties.load(is);
				agentOptions = MibewAgentOptions.create(myProperties);
				return true;
			} else {
				handleError("cannot find mibew.ini");
			}
		} catch (IOException e) {
			handleError(e.getMessage());
		}
		return false;
	}

	protected void handleError(String message) {
		System.err.println(message);
	}

	public MibewAgentOptions getAgentOptions() {
		return agentOptions;
	}

	public static class JOptions extends Options {

        private final Shell fShell;

        public JOptions(Shell shell, String[] args) {
			super(args);
            fShell = shell;
        }

		@Override
		protected void handleError(final String message) {
            MessageBox messageBox = new MessageBox(fShell, SWT.OK | SWT.ICON_ERROR);
            messageBox.setText("Options error");	//$NON-NLS-1$
            messageBox.setMessage(message);
            messageBox.open();
		}
	}
}
