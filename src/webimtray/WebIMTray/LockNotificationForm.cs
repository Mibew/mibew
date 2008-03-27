using System;
using System.Drawing;
using System.Runtime.InteropServices;
using System.Windows.Forms;

namespace webImTray {

    /// <summary>
    /// Base class for a form that wants to be notified of Windows
    /// session lock / unlock events
    /// </summary>
    public class LockNotificationForm : Form {
        // from wtsapi32.h
        private const int NotifyForThisSession = 0;

        // from winuser.h
        private const int SessionChangeMessage = 0x02B1;
        private const int SessionLockParam = 0x7;
        private const int SessionUnlockParam = 0x8;

        [DllImport("wtsapi32.dll")]
        private static extern bool WTSRegisterSessionNotification(IntPtr hWnd, int dwFlags);

        [DllImport("wtsapi32.dll")]
        private static extern bool WTSUnRegisterSessionNotification(IntPtr hWnd);

        // flag to indicate if we've registered for notifications or not
        private bool registered = false;

        /// <summary>
        /// Is this form receiving lock / unlock notifications
        /// </summary>
        protected bool ReceivingLockNotifications {
            get { return registered; }
        }

        /// <summary>
        /// Unregister for event notifications
        /// </summary>
        protected override void Dispose(bool disposing) {
            if (registered) {
                WTSUnRegisterSessionNotification(Handle);
                registered = false;
            }

            base.Dispose(disposing);
            return;
        }

        /// <summary>
        /// Register for event notifications
        /// </summary>
        protected override void OnHandleCreated(EventArgs e) {
            base.OnHandleCreated(e);

            // WtsRegisterSessionNotification requires Windows XP or higher
            bool haveXp = Environment.OSVersion.Platform == PlatformID.Win32NT &&
                                (Environment.OSVersion.Version.Major > 5 ||
                                    (Environment.OSVersion.Version.Major == 5 &&
                                     Environment.OSVersion.Version.Minor >= 1));

            if (haveXp)
                registered = WTSRegisterSessionNotification(Handle, NotifyForThisSession);

            return;
        }

        /// <summary>
        /// The windows session has been locked
        /// </summary>
        protected virtual void OnSessionLock() {
            return;
        }

        /// <summary>
        /// The windows session has been unlocked
        /// </summary>
        protected virtual void OnSessionUnlock() {
            return;
        }

        /// <summary>
        /// Process windows messages
        /// </summary>
        protected override void WndProc(ref Message m) {
            // check for session change notifications
            if (m.Msg == SessionChangeMessage) {
                if (m.WParam.ToInt32() == SessionLockParam)
                    OnSessionLock();
                else if (m.WParam.ToInt32() == SessionUnlockParam)
                    OnSessionUnlock();
            }

            base.WndProc(ref m);
            return;
        }
    }
}
