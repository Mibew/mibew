package org.mibew.api;

import java.io.UnsupportedEncodingException;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

/**
 *  @author inspirer
 */
public class Utils {
	
	private static final String HEX_DIGITS = "0123456789abcdef";
	
	public static String md5(String string) throws NoSuchAlgorithmException, UnsupportedEncodingException {
		return md5(string.getBytes("utf-8"));
	}
	
	private static String md5(byte[] string) throws NoSuchAlgorithmException {
		MessageDigest md = MessageDigest.getInstance( "MD5" );
		md.update(string);
		byte[] digest = md.digest();
		StringBuilder sb = new StringBuilder();
		for ( byte b : digest ) {
			sb.append(HEX_DIGITS.charAt((b&0xff)>>4));
			sb.append(HEX_DIGITS.charAt(b&0xf));
        }
		return sb.toString();
	}
	
	
}
