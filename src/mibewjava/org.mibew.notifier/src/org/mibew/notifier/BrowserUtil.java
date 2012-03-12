package org.mibew.notifier;

import java.io.IOException;
import java.lang.reflect.Method;
import java.util.Arrays;

public class BrowserUtil {

	static final String[] browsers = { "firefox", "opera", "konqueror", "epiphany", "seamonkey", "galeon",
			"kazehakase", "mozilla", "netscape" };

	/**
	 * Bare Bones Browser Launch
	 * Version 2.0 (May 26, 2009)
	 * By Dem Pilafian 
	 * @param url
	 */
	public static void openURL(String url) throws IOException {
		String osName = System.getProperty("os.name");
		try {
			if (osName.startsWith("Mac OS")) {
				Class<?> fileMgr = Class.forName("com.apple.eio.FileManager");
				Method openURL = fileMgr.getDeclaredMethod("openURL", new Class[] { String.class });
				openURL.invoke(null, new Object[] { url });
			} else if (osName.startsWith("Windows")) {
				Runtime.getRuntime().exec("rundll32 url.dll,FileProtocolHandler " + url);
			} else { // assume Unix or Linux
				boolean found = false;
				for (String browser : browsers)
					if (!found) {
						found = Runtime.getRuntime().exec(new String[] { "which", browser }).waitFor() == 0;
						if (found)
							Runtime.getRuntime().exec(new String[] { browser, url });
					}
				if (!found)
					throw new Exception(Arrays.toString(browsers));
			}
		} catch (Throwable th) {
			throw new IOException("Error attempting to launch web browser\n" + th.toString());
		}
	}
}