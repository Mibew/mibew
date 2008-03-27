using System;
using System.Collections.Generic;
using System.Text;
using System.Windows.Forms;

namespace webImTray {
    class Options {

        public const string DEFAULT_SERVER = "http://localhost/webim";
        public const string PENDING_USERS_PAGE = "/operator/users.php";
        public const string SETTINGS_PAGE = "/operator/operators.php";
        private const string HTTP_PREFIX = "http://";

        public static string WebIMServer {
            get {
                String server = Application.UserAppDataRegistry.GetValue("server", DEFAULT_SERVER).ToString();
                while (server.EndsWith("/")) {
                    server = server.Substring(0, server.Length - 1);
                }
                if (!server.StartsWith(HTTP_PREFIX)) {
                    return DEFAULT_SERVER;
                }
                return server;
            }
            set {
                if (!value.StartsWith(HTTP_PREFIX))
                    return;
                Application.UserAppDataRegistry.SetValue("server", value.ToString());
            }
        }

        public static decimal ForceRefreshTime {
            get {
                return Decimal.Parse(Application.UserAppDataRegistry.GetValue("refreshtime", "15").ToString());
            }
            set {
                Application.UserAppDataRegistry.SetValue("refreshtime", value.ToString());
            }
        }

        public static bool DisconnectOnLock {
            get {
                return Application.UserAppDataRegistry.GetValue("disconnectonlock", "true").ToString().ToLower().Equals("true");
            }
            set {
                Application.UserAppDataRegistry.SetValue("disconnectonlock", value.ToString());
            }
        }

        public static bool ShowInTaskBar {
            get {
                return Application.UserAppDataRegistry.GetValue("showintaskbar", "false").ToString().ToLower().Equals("true");
            }
            set {
                Application.UserAppDataRegistry.SetValue("showintaskbar", value.ToString());
            }
        }

        public static bool HideAfterStart {
            get {
                return Application.UserAppDataRegistry.GetValue("hideafterstart", "false").ToString().ToLower().Equals("true");
            }
            set {
                Application.UserAppDataRegistry.SetValue("hideafterstart", value.ToString());
            }
        }

        private const string autoRunUserRegistry = "Software\\Microsoft\\Windows\\CurrentVersion\\Run";
        private const string autoRunRegistry = "HKEY_CURRENT_USER\\" + autoRunUserRegistry;
        private const string autoRunKey = "webimtray.exe";

        public static bool AutoStart {
            get {
                return Microsoft.Win32.Registry.GetValue(autoRunRegistry, autoRunKey, "").ToString().Length > 0;
            }
            set {
                if (value) {
                    Microsoft.Win32.Registry.SetValue(autoRunRegistry, autoRunKey, Application.ExecutablePath);
                } else {
                    try {
                        Microsoft.Win32.RegistryKey key = Microsoft.Win32.Registry.CurrentUser.OpenSubKey(autoRunUserRegistry, true);
                        key.DeleteValue(autoRunKey, false);
                        key.Close();
                    }
                    catch (Exception) {
                        Microsoft.Win32.Registry.SetValue(autoRunRegistry, autoRunKey, "");
                    }
                }
            }
        }

        public static bool HideMainWindow {
            get {
                if (forceShowWindow)
                    return false;
                return HideAfterStart;
            }
        }


        static bool forceShowWindow = false;

        internal static void parseParameters(string[] args) {
            if (args.Length == 1 && args[0].Equals("/show"))
                forceShowWindow = true;
        }
    }
}
