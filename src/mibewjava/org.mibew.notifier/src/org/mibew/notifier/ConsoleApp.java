package org.mibew.notifier;

import org.mibew.api.MibewAgent;
import org.mibew.api.MibewAgentListener;
import org.mibew.api.MibewAgentOptions;

public class ConsoleApp {

	public static void main(String[] args) {
		MibewAgent agent = new MibewAgent(new MibewAgentOptions("http://localhost:8080/webim/", "admin", "1"), new MibewAgentListener() {
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
