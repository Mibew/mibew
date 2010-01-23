package org.mibew.api;

import java.io.IOException;
import java.util.Properties;

/**
 *  @author inspirer
 */
public class MibewAgentOptions {

	private String fUrl;
	private String fLogin;
	private String fPassword;
	private int fConnectionRefreshTimeout = 900; 	// in seconds (15 minutes by default)
	private int fPollingInterval = 2000;			// 2 sec (in milliseconds)
	
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

	private static String getProperty(Properties p, String name, String defaultValue) throws IOException {
		String result = p.getProperty(name);
		if(result == null || result.trim().length() == 0) {
			if(defaultValue != null) {
				return defaultValue;
			}
			throw new IOException("No '"+name+"' property");
		}
		return result;
	}
	
	public static MibewAgentOptions create(Properties p) throws IOException {
		String url = getProperty(p, "mibew.host", null);
		String login = getProperty(p, "mibew.login", null);
		String password = getProperty(p, "mibew.password", null);
		
		return new MibewAgentOptions(url, login, password); 
	}
}
