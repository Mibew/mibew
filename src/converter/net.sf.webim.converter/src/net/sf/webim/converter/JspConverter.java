package net.sf.webim.converter;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;

import net.sf.lapg.templates.api.EvaluationContext;
import net.sf.lapg.templates.api.impl.FolderTemplateLoader;
import net.sf.lapg.templates.api.impl.TemplateEnvironment;
import net.sf.lapg.templates.model.xml.XmlModel;
import net.sf.lapg.templates.model.xml.XmlNavigationFactory;
import net.sf.lapg.templates.model.xml.XmlNode;

public class JspConverter {

	public static void main(String[] args) {
		String toProcess = getFileContents("test/index.xml");

		XmlNode root = XmlModel.load(toProcess);

		ConverterHelper helper = new ConverterHelper();
		TemplateEnvironment env = new TemplateEnvironment(
				new XmlNavigationFactory(),
				new FolderTemplateLoader(new File("templates")));
		EvaluationContext context = new EvaluationContext(root.getChildren());
		context.setVariable("helper", helper);
		System.out.println(env.executeTemplate("conv.convertList", context, null));
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
