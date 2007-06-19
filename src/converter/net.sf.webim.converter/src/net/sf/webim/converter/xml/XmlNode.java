package net.sf.webim.converter.xml;

import java.util.List;

public class XmlNode {
	
	private String tagName;
	private List<XmlArgument> arguments;

	public XmlNode(String tagName, List<XmlArgument> arguments) {
		this.tagName = tagName;
		this.arguments = arguments;
	}

	public String getTagName() {
		return tagName;
	}
}
