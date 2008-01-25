package net.sf.webim.converter;

import net.sf.lapg.templates.api.impl.DefaultStaticMethods;
import net.sf.lapg.templates.model.xml.XmlData;
import net.sf.lapg.templates.model.xml.XmlNode;


public class ConverterHelper extends DefaultStaticMethods {


	public boolean isNode(XmlNode element) {
		return true;
	}

	public boolean isNode(XmlData element) {
		return false;
	}

	public boolean containsColon(String s) {
		return s.indexOf(":") >= 0;
	}

	public String replaceColon(String s) {
		return s.replace(':', '.');
	}
}
