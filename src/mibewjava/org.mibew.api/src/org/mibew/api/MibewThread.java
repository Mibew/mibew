package org.mibew.api;

import java.text.MessageFormat;

/**
 *  @author inspirer
 */
public class MibewThread implements Comparable<MibewThread> {

	private final long fId;
	private String fState;
	private String fClientName = "";
	private String fAgent = "";
	private String fAddress = "";
	private String fFirstMessage = "";
	private boolean fCanOpen = false;
	private boolean fCanView = false;
	private boolean fCanBan = false;
	private String fStateText;
	private long fWaitingTime;

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
		fWaitingTime = updated.fWaitingTime;
	}
	
	public long getId() {
		return fId;
	}
	
	public String getState() {
		return fState;
	}
	
	public String getStateText() {
		return fStateText;
	}
	
	public void setStateText(String stateText) {
		fStateText = stateText;
	}
	
	public String getAddress() {
		return fAddress;
	}
	
	public void setAddress(String address) {
		fAddress = address;
	}
	
	public String getAgent() {
		return fAgent;
	}
	
	public void setAgent(String agent) {
		fAgent = agent;
	}
	
	public String getClientName() {
		return fClientName;
	}
	
	public void setClientName(String clientName) {
		fClientName = clientName;
	}
	
	public String getFirstMessage() {
		return fFirstMessage;
	}
	
	public void setFirstMessage(String firstMessage) {
		fFirstMessage = firstMessage;
	}

	public boolean isCanBan() {
		return fCanBan;
	}
	
	public void setCanBan(boolean canBan) {
		fCanBan = canBan;
	}
	
	public boolean isCanOpen() {
		return fCanOpen;
	}
	
	public void setCanOpen(boolean canOpen) {
		fCanOpen = canOpen;
	}
	
	public boolean isCanView() {
		return fCanView;
	}
	
	public void setCanView(boolean canView) {
		fCanView = canView;
	}

	public long getWaitingTime() {
		return fWaitingTime;
	}
	
	public void setWaitingTime(long value) {
		fWaitingTime = value;		
	}
	
	@Override
	public int compareTo(MibewThread o) {
		int res = index(this).compareTo(index(o));
		if(res != 0) {
			return res;
		}
		return getClientName().compareTo(o.getClientName());
	}
	
	private Integer index(MibewThread th) {
		if("prio".equals(th.getState())) {
			return -1;
		}
		if("wait".equals(th.getState())) {
			return 0;
		}
		return 1;
	}
	
	@Override
	public String toString() {
		StringBuilder sb = new StringBuilder();
		boolean isChat = "chat".equals(getState()); 
		if(isChat) {
			sb.append("(chat) ");
		}
		sb.append(getClientName());
		if(!isCanOpen() && isCanView()) {
			sb.append(" (view only)");
		}
		if(!isChat) {
			sb.append(" - ");
			sb.append(formatWaitingTime((System.currentTimeMillis() - getWaitingTime())/1000));
		}
		return sb.toString();
	}
	
	private static String atLeast2(long i) {
		return i < 10 ? "0" + i : Long.toString(i);
 	}
	
	private static String formatWaitingTime(long time) {
		String s = atLeast2(time/60%60) + ":" + atLeast2(time%60);
		if(time >= 3600) {
			s = atLeast2(time/3600%24) + ":" + s;
			if(time >= 24*3600) {
				s = time/24/3600 + "d, " + s;
			}
		}
		return s;
	}
}
