package org.mibew.notifier;

import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

import javax.swing.JOptionPane;

import org.mibew.api.MibewAgentOptions;

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

		public JOptions(String[] args) {
			super(args);
		}

		@Override
		protected void handleError(String message) {
			JOptionPane.showMessageDialog(null, message);
		}
	}
}
