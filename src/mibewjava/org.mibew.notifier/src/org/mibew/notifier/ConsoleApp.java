package org.mibew.notifier;

import org.mibew.api.MibewAgent;
import org.mibew.api.MibewAgentListener;

public class ConsoleApp {

	public static void main(String[] args) {
		Options options = new Options(args);
		if(!options.load()) {
			return;
		}

		MibewAgent agent = new MibewAgent(options.getAgentOptions(), new MibewAgentListener() {
			@Override
			protected void onlineStateChanged(boolean isOnline) {
				System.out.println("now " + (isOnline ? "online" : "offline"));
			}
		});
		agent.launch();
		try {
			Thread.sleep(3500);
		} catch (InterruptedException e) {
		}
		
		agent.stop();
	}
}
