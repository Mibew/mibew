package org.mibew.notifier;

import org.mibew.api.MibewAgent;
import org.mibew.api.MibewAgentOptions;

public class NotifyApp {

	public static void main(String[] args) {
		TrayNotifier tn = new TrayNotifier();
		tn.init();
		
		MibewAgent agent = new MibewAgent(new MibewAgentOptions("http://localhost:8080/webim/", "admin", "1"), tn);
		agent.launch();
		
		tn.setAgent(agent);
	}
}
