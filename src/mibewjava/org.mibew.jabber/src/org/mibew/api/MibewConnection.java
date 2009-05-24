package org.mibew.api;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.security.NoSuchAlgorithmException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.mibew.api.handlers.LoginHandler;

/**
 *  @author inspirer
 */
public class MibewConnection {

	private static final int REQUEST_TIMEOUT = 5000;

	private final String fUrl;

	private final Map<String,String> fCookies;

	/**
	 * @param url		Ex: http://yourserver.com/webim/
	 * @param login		admin
	 * @param password	operators password
	 */
	public MibewConnection(String url, String login, String password)
			throws UnsupportedEncodingException, NoSuchAlgorithmException,
			MalformedURLException {
		this.fUrl = url;
		this.fCookies = new HashMap<String,String>();
		this.fCookies.put("webim_lite", URLEncoder.encode(login + "," + Utils.md5(Utils.md5(password)), "UTF-8"));
	}

	/**
	 *  Connects to the server and tries to login, returns true if successful.
	 */
	public boolean connect() throws ParserConfigurationException {
		try {
			MibewResponse response = request("operator/autologin.php", "");
			SAXParser sp = SAXParserFactory.newInstance().newSAXParser();
			LoginHandler handler = new LoginHandler();
			sp.parse(new ByteArrayInputStream(response.getResponse()), handler);
			String status = handler.getStatus();
			return status.equals("OK");
		} catch(Exception e) {
			handleError(e.getMessage(), e);
			return false;
		}
	}

	public void disconnect() {
	}

	/**
	 * Request server.
	 * @param suburl			ex: operator/update.php
	 * @param urlParameters		post content
	 */
	public final synchronized MibewResponse request(String suburl, String urlParameters) throws IOException {
		HttpURLConnection connection = null;

		try {
			connection = (HttpURLConnection) new URL(fUrl+suburl).openConnection();
			connection.setConnectTimeout(REQUEST_TIMEOUT);
			connection.setReadTimeout(REQUEST_TIMEOUT);

			connection.setRequestMethod("POST");
			connection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
			connection.setRequestProperty("Content-Length", Integer.toString(urlParameters.getBytes().length));
			if(fCookies.size() > 0) {
				StringBuilder sb = new StringBuilder();
				for(Entry<String,String> cookie : fCookies.entrySet()) {
					if(sb.length() > 0) {
						sb.append("; ");
					}
					sb.append(cookie.getKey()+"="+cookie.getValue());
				}
				connection.addRequestProperty("Cookie", sb.toString());
			}
			

			connection.setUseCaches(false);
			connection.setDoInput(true);
			connection.setDoOutput(true);

			// create request
			DataOutputStream wr = new DataOutputStream(connection.getOutputStream());
			wr.writeBytes(urlParameters);
			wr.flush();
			wr.close();

			// read response
			InputStream is = connection.getInputStream();
			int len = connection.getContentLength();
			ByteArrayOutputStream buffer = new ByteArrayOutputStream(len < 256 ? 256 : len);
			byte b[] = new byte[1024];
			int size = 0;
			while((size=is.read(b)) >= 0) {
				buffer.write(b, 0, size);
			}
			is.close();

			// load cookies
			List<String> cookies = connection.getHeaderFields().get("Set-Cookie");
			if(cookies != null) {
				for(String cookie : cookies) {
					Matcher matcher = COOKIESET.matcher(cookie);
					if(matcher.find()) {
						String name = matcher.group(1);
						String value = matcher.group(2);
						fCookies.put(name, value);
					}
				}
			}
			return new MibewResponse(connection.getResponseCode(), buffer.toByteArray());
		} catch (Exception e) {
			if(e instanceof IOException) {
				throw (IOException)e;
			}
			throw new IOException("cannot connect: " + e.getMessage());
		} finally {
			if (connection != null) {
				connection.disconnect();
			}
		}
	}
	
	protected void handleError(String message, Exception ex) {
		System.err.println(message);
	}

	private static Pattern COOKIESET = Pattern.compile("\\A(\\w+)=([^;]+);");
}
