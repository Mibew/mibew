package org.mibew.notifier;

import org.mibew.api.MibewAgent;
import org.mibew.notifier.Options.JOptions;

public class NotifyApp {

	public static void main(String[] args) {
		Options options = new JOptions(args);
		if(!options.load()) {
			return;
		}
		
		TrayNotifier tn = new TrayNotifier();
		tn.init();
		
		MibewAgent agent = new MibewAgent(options.getAgentOptions(), tn);
		agent.launch();
		
		tn.setAgent(agent);
	}
}
