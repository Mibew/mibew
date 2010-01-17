package org.mibew.api;

import java.io.ByteArrayInputStream;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.mibew.api.handlers.UpdateHandler;

/**
 *  @author inspirer
 */
public class MibewTracker {
	
	private final MibewConnection fConnection;
	private final MibewTrackerListener fListener;
	private long fSince = 0;
	private long fLastUpdate = 0;
	
	private final Map<Long,MibewThread> fThreads;

	public MibewTracker(MibewConnection conn, MibewTrackerListener listener) {
		this.fConnection = conn;
		this.fListener = listener;
		this.fThreads = new HashMap<Long, MibewThread>();
	}
	
	public void track() throws InterruptedException {
		for(int i = 0; i < 5; i++) {
			try {
				MibewResponse response = fConnection.request("operator/update.php", "since="+fSince);
				SAXParser sp = SAXParserFactory.newInstance().newSAXParser();
				UpdateHandler handler = new UpdateHandler();
				sp.parse(new ByteArrayInputStream(response.getResponse()), handler);
				handleResponse(response, handler);
			} catch(Exception e) {
				System.err.println("update exception: " + e.getMessage());
			}
			Thread.sleep(1000);
		}
	}

	private void handleResponse(MibewResponse response, UpdateHandler handler) {
		if(handler.getResponse() == UpdateHandler.UPD_ERROR) {
			System.out.println("Update error: " + handler.getMessage());
		} else if(handler.getResponse() == UpdateHandler.UPD_THREADS) {
			System.out.println("Updated.... " + handler.getRevision());
			fSince = handler.getRevision();
			fLastUpdate = handler.getTime();
			List<MibewThread> threads = handler.getThreads();
			if(threads != null && threads.size() > 0) {
				processUpdate(threads);
			}
		} else {
			System.out.println("Update error");
			System.out.println(response.getResponseText());
		}
	}
	
	private void processUpdate(List<MibewThread> updated) {
		for(MibewThread mt : updated) {
			MibewThread existing = fThreads.get(mt.fId);
			boolean isClosed = mt.fState.equals("closed");
			if(existing == null) {
				if(!isClosed) {
					fThreads.put(mt.fId, mt);
					fListener.threadCreated(mt);
				}
			} else if(isClosed) {
				fThreads.remove(mt.fId);
				fListener.threadClosed(existing);
			} else {
				existing.updateFrom(mt);
				fListener.threadChanged(existing);
			}
		}
	}

	public long getLastUpdate() {
		return fLastUpdate;
	}
}
