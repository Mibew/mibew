package org.mibew.api;

import java.io.UnsupportedEncodingException;
import java.net.MalformedURLException;
import java.security.NoSuchAlgorithmException;
import java.util.LinkedList;
import java.util.List;

import javax.xml.parsers.ParserConfigurationException;

import org.xml.sax.SAXException;

/**
 *  @author inspirer
 */
public class MibewAgent {
	
	private final MibewAgentOptions fOptions;
	private final MibewAgentListener fListener;
	private final MibewUpdateThread fThread;
	private volatile boolean started;
	
	public MibewAgent(MibewAgentOptions options, MibewAgentListener listener) {
		fOptions = options;
		fListener = listener;
		fThread = new MibewUpdateThread();
		started = false;
	}
	
	public synchronized void launch() {
		if(!started) {
			fThread.start();
			started = true;
		}
	}
	
	public synchronized void stop() {
		if(started) {
			fThread.disconnect();
			started = false;
		}
	}
	
	public boolean isOnline() {
		return fThread.isOnline();
	}
	
	protected void logError(String message, Throwable th) {
		System.err.println(message);
	}
	
	public MibewAgentOptions getOptions() {
		return fOptions;
	}

	private class MibewUpdateThread extends Thread {
		
		private volatile boolean fExiting;
		private volatile boolean isOnline = false;
		private final Object fSync = new Object();
		
		public MibewUpdateThread() {
			setName("Mibew Connection thread");
			fExiting = false;
		}
		
		public void disconnect() {
			synchronized (fSync) {
				fExiting = true;
				fSync.notifyAll();
			}
		}

		@Override
		public void run() {
			while(!fExiting) {
				try {
					connectAndTrack();
				} catch(InterruptedException ex) {
					/* ignore */
				} catch(Throwable th) {
					logError(th.getMessage(), th);
				}
			}
			setOnline(false);
		}
		
		private void setOnline(boolean online) {
			if(isOnline != online) {
				isOnline = online;
				fListener.onlineStateChanged(online);
			}
		}
		
		public boolean isOnline() {
			return isOnline;
		}
		
		private void connectAndTrack() throws InterruptedException, UnsupportedEncodingException, NoSuchAlgorithmException, MalformedURLException, ParserConfigurationException, SAXException {
			setOnline(false);
			MibewConnection conn = new MibewConnection(fOptions.getUrl(), fOptions.getLogin(), fOptions.getPassword()) {
				@Override
				protected void handleError(String message, Exception ex) {
					logError(message, ex);
				}
			};
			if(!conn.connect()) {
				logError("Wrong server, login or password.", null);
				interruptableSleep(fOptions.getPollingInterval() * 3);
				return;
			}
			final List<MibewThread> createdThreads = new LinkedList<MibewThread>();
			MibewTracker mt = new MibewTracker(conn, new MibewTrackerListener(){
				@Override
				public void threadCreated(MibewThread thread) {
					createdThreads.add(thread);
				}
			});
			long maxTime = System.currentTimeMillis() + fOptions.getConnectionRefreshTimeout()*1000;
			
			int errorsCount = 0;
			while(!fExiting && (System.currentTimeMillis() < maxTime)) {
				try {
					createdThreads.clear();
					mt.update();
					fListener.updated(mt.getThreads(), createdThreads.toArray(new MibewThread[createdThreads.size()]));
					errorsCount = 0;
					setOnline(true);
				} catch (Throwable th) {
					setOnline(false);
					errorsCount++;
					logError("not updated", th);
					interruptableSleep(errorsCount < 10 ? fOptions.getPollingInterval() / 2 : fOptions.getPollingInterval() * 2);
					continue;
				}
				interruptableSleep(fOptions.getPollingInterval());
			}			

			conn.disconnect();
		}
		
		private void interruptableSleep(long millis) throws InterruptedException {
			synchronized (fSync ) {
				if(fExiting) {
					return;
				}
				fSync.wait(millis);
			}
		}
	}
}
