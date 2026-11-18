using System;
using System.Diagnostics;
using System.IO;
using System.Runtime.InteropServices;
using System.Windows.Forms;

namespace MasayumeApp
{
    static class Program
    {
        [DllImport("user32.dll")]
        private static extern bool SetProcessDPIAware();

        [DllImport("shell32.dll", SetLastError = true)]
        private static extern void SetCurrentProcessExplicitAppUserModelID([MarshalAs(UnmanagedType.LPWStr)] string AppID);

        [STAThread]
        static void Main()
        {
            try
            {
                SetProcessDPIAware();
            }
            catch { }

            try
            {
                SetCurrentProcessExplicitAppUserModelID("Masayume.LPK.LMS.CBT.Desktop");
            }
            catch { }

            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);

            string targetUrl = "https://masayume.id";

            // Dedicated isolated profile directory for Masayume LMS
            string appData = Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData);
            string profileDir = Path.Combine(appData, "MasayumeLMS", "Profile");

            try
            {
                if (!Directory.Exists(profileDir))
                {
                    Directory.CreateDirectory(profileDir);
                }
            }
            catch { }

            string browserPath = FindChromiumBrowser();

            if (!string.IsNullOrEmpty(browserPath))
            {
                string arguments = string.Format(
                    "--app=\"{0}\" " +
                    "--user-data-dir=\"{1}\" " +
                    "--app-id=\"MasayumeLMS\" " +
                    "--window-size=1366,768 " +
                    "--start-maximized " +
                    "--enable-features=OverlayScrollbar " +
                    "--disable-features=Translate,ChromeWhatsNewUI " +
                    "--no-first-run " +
                    "--no-default-browser-check",
                    targetUrl,
                    profileDir
                );

                try
                {
                    ProcessStartInfo psi = new ProcessStartInfo
                    {
                        FileName = browserPath,
                        Arguments = arguments,
                        UseShellExecute = false
                    };
                    Process.Start(psi);
                    return;
                }
                catch (Exception ex)
                {
                    MessageBox.Show(
                        "Gagal meluncurkan aplikasi Masayume:\n" + ex.Message,
                        "Masayume LMS & CBT",
                        MessageBoxButtons.OK,
                        MessageBoxIcon.Error
                    );
                }
            }

            // Fallback jika tidak ditemukan Chromium engine
            try
            {
                Process.Start(targetUrl);
            }
            catch (Exception ex)
            {
                MessageBox.Show(
                    "Tidak dapat membuka alamat sistem:\n" + ex.Message,
                    "Masayume LMS & CBT",
                    MessageBoxButtons.OK,
                    MessageBoxIcon.Error
                );
            }
        }

        private static string FindChromiumBrowser()
        {
            string programFiles = Environment.GetFolderPath(Environment.SpecialFolder.ProgramFiles);
            string programFilesX86 = Environment.GetFolderPath(Environment.SpecialFolder.ProgramFilesX86);
            string localAppData = Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData);

            string[] candidatePaths = new string[]
            {
                // 1. Microsoft Edge (Bawaan Windows 10 & 11)
                Path.Combine(programFilesX86, @"Microsoft\Edge\Application\msedge.exe"),
                Path.Combine(programFiles, @"Microsoft\Edge\Application\msedge.exe"),
                Path.Combine(localAppData, @"Microsoft\Edge\Application\msedge.exe"),

                // 2. Google Chrome
                Path.Combine(programFiles, @"Google\Chrome\Application\chrome.exe"),
                Path.Combine(programFilesX86, @"Google\Chrome\Application\chrome.exe"),
                Path.Combine(localAppData, @"Google\Chrome\Application\chrome.exe"),

                // 3. Brave Browser
                Path.Combine(programFiles, @"BraveSoftware\Brave-Browser\Application\brave.exe"),
                Path.Combine(programFilesX86, @"BraveSoftware\Brave-Browser\Application\brave.exe"),
            };

            foreach (string path in candidatePaths)
            {
                if (!string.IsNullOrEmpty(path) && File.Exists(path))
                {
                    return path;
                }
            }

            return null;
        }
    }
}
