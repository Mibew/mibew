package org.mibew.api;


/**
 *  @author inspirer
 */
public abstract class MibewTrackerListener {
	
	public void threadCreated(MibewThread thread) {
	}
	
	public void threadChanged(MibewThread thread) {
	}

	public void threadClosed(MibewThread thread) {
	}
	
}
