package org.mibew.api;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.mibew.api.handlers.UpdateHandler;
import org.xml.sax.SAXException;

/**
 * @author inspirer
 */
public class MibewTracker {

	private final MibewConnection fConnection;
	private final MibewTrackerListener fListener;
	private long fSince = 0;
	private long fLastUpdate = 0;

	private final Map<Long, MibewThread> fThreads;

	public MibewTracker(MibewConnection conn, MibewTrackerListener listener) {
		this.fConnection = conn;
		this.fListener = listener;
		this.fThreads = new HashMap<Long, MibewThread>();
	}

	public void update() throws IOException, SAXException, ParserConfigurationException {
		MibewResponse response = fConnection.request("operator/update.php", "since=" + fSince);
		SAXParser sp = SAXParserFactory.newInstance().newSAXParser();
		UpdateHandler handler = new UpdateHandler();
		sp.parse(new ByteArrayInputStream(response.getResponse()), handler);
		handleResponse(response, handler);
	}

	private void handleResponse(MibewResponse response, UpdateHandler handler) throws IOException {
		if (handler.getResponse() == UpdateHandler.UPD_ERROR) {
			throw new IOException("Update error: " + handler.getMessage());
		} else if (handler.getResponse() == UpdateHandler.UPD_THREADS) {
			fSince = handler.getRevision();
			fLastUpdate = handler.getTime();
			List<MibewThread> threads = handler.getThreads();
			if (threads != null && threads.size() > 0) {
				processUpdate(threads);
			}
		} else {
			throw new IOException("Update error: " + response.getResponseText());
		}
	}

	private void processUpdate(List<MibewThread> updated) {
		for (MibewThread mt : updated) {
			MibewThread existing = fThreads.get(mt.getId());
			boolean isClosed = mt.getState().equals("closed");
			if (existing == null) {
				if (!isClosed) {
					fThreads.put(mt.getId(), mt);
					fListener.threadCreated(mt);
				}
			} else if (isClosed) {
				fThreads.remove(mt.getId());
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
	
	public MibewThread[] getThreads() {
		Collection<MibewThread> values = fThreads.values();
		return values.toArray(new MibewThread[values.size()]);
	}
}
