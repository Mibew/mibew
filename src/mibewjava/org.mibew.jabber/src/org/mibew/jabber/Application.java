package org.mibew.jabber;

import java.io.IOException;
import java.security.NoSuchAlgorithmException;

import javax.xml.parsers.ParserConfigurationException;

import org.jivesoftware.smack.Chat;
import org.jivesoftware.smack.MessageListener;
import org.jivesoftware.smack.XMPPConnection;
import org.jivesoftware.smack.XMPPException;
import org.jivesoftware.smack.packet.Message;
import org.mibew.api.MibewConnection;
import org.mibew.api.MibewThread;
import org.mibew.api.MibewTracker;
import org.mibew.api.MibewTrackerListener;
import org.xml.sax.SAXException;

/**
 *  @author inspirer
 */
public class Application {

	public static void main(String[] args) throws NoSuchAlgorithmException, IOException, XMPPException, InterruptedException, ParserConfigurationException, SAXException {
		System.out.println("Mibew Jabber transport application");

		Parameters p = new Parameters(args);
		if(!p.load()) {
			return;
		}
		
		XMPPConnection connection = new XMPPConnection(p.fJabberServer);
		connection.connect();
		connection.login(p.fJabberLogin, p.fJabberPassword);
		final Chat chat = connection.getChatManager().createChat(p.fJabberAdmin, new MessageListener() {

		    public void processMessage(Chat chat, Message message) {
		        System.out.println("Received message: " + message.getThread() + " " + message.getBody());
		    }
		});

		MibewConnection conn = new MibewConnection("http://localhost:8080/webim/", "admin", "1");
		if(!conn.connect()) {
			System.err.println("Wrong server, login or password.");
			return;
		}
		MibewTracker mt = new MibewTracker(conn, new MibewTrackerListener(){
			
			@Override
			public void threadCreated(MibewThread thread) {
				try {
					chat.sendMessage(thread.fId + ": " + thread.fAddress + " " + thread.fClientName);
				} catch (XMPPException e) {
					e.printStackTrace();
				}
			}
			
		});
		mt.track();

		connection.disconnect();
	}
}
