package org.mibew.jabber;

import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

/**
 *  @author inspirer
 */
public class Parameters {
	
	private final String[] fArguments;
	
	public String fJabberServer;
	public String fJabberLogin;
	public String fJabberPassword;
	
	public String fJabberAdmin;

	public Parameters(String[] args) {
		this.fArguments = args;
	}
	
	private String getProperty(Properties p, String name) throws IOException {
		String result = p.getProperty(name);
		if(result == null || result.trim().length() == 0) {
			throw new IOException("No '"+name+"' property");
		}
		return result;
	}

	public boolean load() {
		try {
			InputStream is = getClass().getClassLoader().getResourceAsStream("mibew.ini");
			if(is != null) {
				Properties p = new Properties();
				p.load(is);
				
				fJabberServer = getProperty(p, "jabber.host");
				fJabberLogin = getProperty(p, "jabber.login");
				fJabberPassword = getProperty(p, "jabber.password");
				fJabberAdmin = getProperty(p, "jabber.admin");
				
				return true; 
			}
		} catch (IOException e) {
			System.err.println(e.getMessage());
			return false;
		}
		
		System.err.println("Cannot find mibew.ini");
		return false;
	}
}
