using System;
using System.Diagnostics;
using System.IO;
using System.Windows.Forms;
using Microsoft.Win32;

namespace MasayumeInstaller
{
    static class Uninstaller
    {
        [STAThread]
        static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);

            DialogResult confirm = MessageBox.Show(
                "Apakah Anda yakin ingin menghapus MASAYUME - LMS & CBT beserta seluruh shortcut dari komputer Anda?",
                "Penghapusan MASAYUME LMS & CBT",
                MessageBoxButtons.YesNo,
                MessageBoxIcon.Question
            );

            if (confirm != DialogResult.Yes)
            {
                return;
            }

            try
            {
                // 1. Hapus Shortcut Desktop
                string desktopPath = Environment.GetFolderPath(Environment.SpecialFolder.Desktop);
                string desktopLnk = Path.Combine(desktopPath, "MASAYUME - LMS & CBT.lnk");
                if (File.Exists(desktopLnk))
                {
                    File.Delete(desktopLnk);
                }

                // 2. Hapus Shortcut Start Menu
                string startMenuPath = Environment.GetFolderPath(Environment.SpecialFolder.Programs);
                string startMenuLnk = Path.Combine(startMenuPath, "MASAYUME - LMS & CBT.lnk");
                if (File.Exists(startMenuLnk))
                {
                    File.Delete(startMenuLnk);
                }

                // 3. Hapus Registry Uninstall
                try
                {
                    Registry.CurrentUser.DeleteSubKeyTree(@"Software\Microsoft\Windows\CurrentVersion\Uninstall\MasayumeLMS", false);
                }
                catch { }

                // 4. Tanya apakah ingin menghapus data sesi/profil
                string appData = Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData);
                string profileDir = Path.Combine(appData, "MasayumeLMS");

                DialogResult deleteData = MessageBox.Show(
                    "Apakah Anda juga ingin menghapus cache data dan riwayat login tersimpan di komputer ini?",
                    "Hapus Data Masayume",
                    MessageBoxButtons.YesNo,
                    MessageBoxIcon.Question
                );

                if (deleteData == DialogResult.Yes && Directory.Exists(profileDir))
                {
                    try
                    {
                        Directory.Delete(profileDir, true);
                    }
                    catch { }
                }

                string currentDir = AppDomain.CurrentDomain.BaseDirectory.TrimEnd('\\');

                MessageBox.Show(
                    "MASAYUME - LMS & CBT telah berhasil dihapus dari komputer Anda.",
                    "Penghapusan Selesai",
                    MessageBoxButtons.OK,
                    MessageBoxIcon.Information
                );

                // Self-delete script untuk menghapus sisa folder instalasi
                string batFile = Path.Combine(Path.GetTempPath(), "remove_masayume.bat");
                string batContent = string.Format(
                    "@echo off\r\n" +
                    "timeout /t 1 /nobreak > nul\r\n" +
                    "rmdir /s /q \"{0}\"\r\n" +
                    "del \"%~f0\"\r\n",
                    currentDir
                );
                File.WriteAllText(batFile, batContent);

                ProcessStartInfo psi = new ProcessStartInfo
                {
                    FileName = batFile,
                    CreateNoWindow = true,
                    UseShellExecute = false
                };
                Process.Start(psi);
            }
            catch (Exception ex)
            {
                MessageBox.Show(
                    "Terjadi kesalahan saat menghapus aplikasi:\n" + ex.Message,
                    "Kesalahan",
                    MessageBoxButtons.OK,
                    MessageBoxIcon.Error
                );
            }
        }
    }
}
