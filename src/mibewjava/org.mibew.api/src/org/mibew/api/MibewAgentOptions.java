package org.mibew.api;

/**
 *  @author inspirer
 */
public class MibewAgentOptions {

	private String fUrl;
	private String fLogin;
	private String fPassword;
	private int fConnectionRefreshTimeout = 900; 	// 15 minutes
	private int fPollingInterval = 3000;			// 2 sec
	
	public MibewAgentOptions(String fUrl, String fLogin, String fPassword) {
		super();
		this.fUrl = fUrl;
		this.fLogin = fLogin;
		this.fPassword = fPassword;
	}

	public String getLogin() {
		return fLogin;
	}
	
	public String getPassword() {
		return fPassword;
	}
	
	public String getUrl() {
		return fUrl;
	}
	
	public int getConnectionRefreshTimeout() {
		return fConnectionRefreshTimeout;
	}
	
	public int getPollingInterval() {
		return fPollingInterval;
	}
}
