package org.mibew.api;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;

/**
 *  @author inspirer
 */
public class MibewResponse {
	
	public int code;
	public byte[] response;

	public MibewResponse(int code, byte[] response) {
		this.code = code;
		this.response = response;
	}

	public int getCode() {
		return code;
	}

	public byte[] getResponse() {
		return response;
	}
	
	public String getResponseText() {
		try {
			Reader r = new InputStreamReader(new ByteArrayInputStream(response), "UTF-8");
			StringBuilder sb = new StringBuilder();
			char[] c = new char[1024];
			int size = 0;
			while((size = r.read(c)) != -1) {
				sb.append(c, 0, size);
			}
			return sb.toString();
		} catch(IOException ex) {
			return "<exception is thrown: "+ex.toString()+">";
		}
	}

	@Override
	public String toString() {
		return
			"Response code: " + code + "\n" +
			"Text: " + getResponseText();
	}
}
