package org.mibew.api.handlers;

import java.util.Stack;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

/**
 *  @author inspirer
 */
public class LoginHandler extends DefaultHandler {
	
	private Stack<String> fPath = new Stack<String>();
	private String status = ""; 

	@Override
	public void startElement(String uri, String localName, String name,
			Attributes attributes) throws SAXException {
		fPath.push(name);
	}
	
	@Override
	public void endElement(String uri, String localName, String name)
			throws SAXException {
		fPath.pop();
	}
	
	@Override
	public void characters(char[] ch, int start, int length) throws SAXException {
		String current = fPath.peek();
		if(fPath.size() != 2 || !current.equals("status") || !fPath.get(0).equals("login")) {
			throw new SAXException("unexpected characters");
		}
		status += new String(ch,start,length);
	}
	
	public String getStatus() {
		return status;
	}
}
