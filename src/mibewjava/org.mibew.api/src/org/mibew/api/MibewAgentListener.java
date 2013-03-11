package org.mibew.api;

public abstract class MibewAgentListener {

	protected void onlineStateChanged(boolean isOnline) {
	}

	protected void updated(MibewThread[] all, MibewThread[] created) {
	}
}
