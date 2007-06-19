package net.sf.webim.converter;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;

import net.sf.webim.converter.parser.Parser;

public class JspConverter {
	
	public static void main(String[] args) {
		String toProcess = getFileContents("C:\\projects\\sf\\webim\\src\\converter\\test.xml");
		
		Parser p = new Parser();
		String result = p.parse(toProcess);
		
		System.out.println(">>>\n" + result + "<<<");
		
	}	
	
	private static String getFileContents(String file) {
		StringBuffer contents = new StringBuffer();
		char[] buffer = new char[2048];
		int count;
		try {
			Reader in = new InputStreamReader(new FileInputStream(file));
			try {
				while ((count = in.read(buffer)) > 0) {
					contents.append(buffer, 0, count);
				}
			} finally {
				in.close();
			}
		} catch (IOException ioe) {
			return null;
		}
		return contents.toString();
	}
}
