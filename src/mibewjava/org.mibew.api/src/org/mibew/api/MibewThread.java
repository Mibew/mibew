package org.mibew.api;

/**
 *  @author inspirer
 */
public class MibewThread {

	public final long fId;
	public String fState;
	public String fClientName = "";
	public String fAgent = "";
	public String fAddress = "";
	public String fFirstMessage = "";
	public boolean fCanOpen = false;
	public boolean fCanView = false;
	public boolean fCanBan = false;
	public String fStateText;

	public MibewThread(long id, String state) {
		fId = id;
		fState = state;
	}
	
	public void updateFrom(MibewThread updated) {
		if(fId != updated.fId) {
			throw new IllegalArgumentException("different threads");
		}
		fState = updated.fState;
		fClientName = updated.fClientName;
		fAgent = updated.fAgent;
		fAddress = updated.fAddress;
		fFirstMessage = updated.fFirstMessage;
		fCanOpen = updated.fCanOpen;
		fCanView = updated.fCanView;
		fCanBan = updated.fCanBan;
		fStateText = updated.fStateText;
	}
}
