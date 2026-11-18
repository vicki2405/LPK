using System;
using System.Drawing;
using System.IO;
using System.Reflection;
using System.Runtime.InteropServices;
using System.Threading;
using System.Windows.Forms;
using Microsoft.Win32;

namespace MasayumeInstaller
{
    public class SetupForm : Form
    {
        [DllImport("user32.dll")]
        private static extern bool SetProcessDPIAware();

        [DllImport("shell32.dll")]
        private static extern void SHChangeNotify(int wEventId, int uFlags, IntPtr dwItem1, IntPtr dwItem2);

        private Panel bannerPanel;
        private Label bannerTitle;
        private Label bannerSub;
        private Panel contentPanel;
        private Panel bottomPanel;
        private Button btnBack;
        private Button btnNext;
        private Button btnCancel;

        private int currentStep = 0;

        // Controls Step 1: Destination
        private TextBox txtDestPath;
        private Button btnBrowse;

        // Controls Step 2: Options
        private CheckBox chkDesktop;
        private CheckBox chkStartMenu;

        // Controls Step 3: Installing
        private ProgressBar prgBar;
        private Label lblStatus;

        // Controls Step 4: Finished
        private CheckBox chkLaunchNow;

        private string defaultInstallPath;

        public SetupForm()
        {
            try { SetProcessDPIAware(); } catch { }

            this.Text = "Setup - MASAYUME LMS & CBT";
            this.Size = new Size(540, 420);
            this.FormBorderStyle = FormBorderStyle.FixedDialog;
            this.MaximizeBox = false;
            this.MinimizeBox = true;
            this.StartPosition = FormStartPosition.CenterScreen;
            this.Font = new Font("Segoe UI", 9F, FontStyle.Regular);
            this.BackColor = Color.FromArgb(248, 250, 252);

            try
            {
                using (Stream stream = Assembly.GetExecutingAssembly().GetManifestResourceStream("app.ico"))
                {
                    if (stream != null) this.Icon = new Icon(stream);
                }
            }
            catch { }

            string localAppData = Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData);
            defaultInstallPath = Path.Combine(localAppData, @"Programs\MasayumeLMS");

            InitializeLayout();
            ShowStep(0);
        }

        private void InitializeLayout()
        {
            // Banner Top Panel
            bannerPanel = new Panel
            {
                Dock = DockStyle.Top,
                Height = 75,
                BackColor = Color.FromArgb(15, 23, 42) // Dark Navy
            };

            bannerTitle = new Label
            {
                Text = "MASAYUME - LMS & CBT Penyaluran Jepang",
                ForeColor = Color.White,
                Font = new Font("Segoe UI", 11F, FontStyle.Bold),
                Location = new Point(20, 16),
                AutoSize = true
            };

            bannerSub = new Label
            {
                Text = "Wisaya Pemasangan Aplikasi Komputer Resmi (https://masayume.id)",
                ForeColor = Color.FromArgb(148, 163, 184),
                Font = new Font("Segoe UI", 8.5F, FontStyle.Regular),
                Location = new Point(20, 42),
                AutoSize = true
            };

            bannerPanel.Controls.Add(bannerTitle);
            bannerPanel.Controls.Add(bannerSub);
            this.Controls.Add(bannerPanel);

            // Bottom Panel
            bottomPanel = new Panel
            {
                Dock = DockStyle.Bottom,
                Height = 55,
                BackColor = Color.FromArgb(241, 245, 249)
            };

            btnCancel = new Button
            {
                Text = "Batal",
                Size = new Size(85, 30),
                Location = new Point(425, 12),
                UseVisualStyleBackColor = true
            };
            btnCancel.Click += (s, e) => this.Close();

            btnNext = new Button
            {
                Text = "Lanjut >",
                Size = new Size(95, 30),
                Location = new Point(320, 12),
                BackColor = Color.FromArgb(2, 132, 199),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat
            };
            btnNext.Click += BtnNext_Click;

            btnBack = new Button
            {
                Text = "< Kembali",
                Size = new Size(85, 30),
                Location = new Point(225, 12),
                UseVisualStyleBackColor = true,
                Enabled = false
            };
            btnBack.Click += (s, e) => ShowStep(currentStep - 1);

            bottomPanel.Controls.Add(btnBack);
            bottomPanel.Controls.Add(btnNext);
            bottomPanel.Controls.Add(btnCancel);
            this.Controls.Add(bottomPanel);

            // Center Content Panel
            contentPanel = new Panel
            {
                Dock = DockStyle.Fill,
                Padding = new Padding(25)
            };
            this.Controls.Add(contentPanel);
            contentPanel.BringToFront();
        }

        private void ShowStep(int step)
        {
            currentStep = step;
            contentPanel.Controls.Clear();

            btnBack.Enabled = (step > 0 && step < 3);
            btnCancel.Enabled = (step != 3);

            if (step == 0) // Welcome
            {
                bannerSub.Text = "Selamat datang di Wisaya Pemasangan MASAYUME LMS & CBT";
                btnNext.Text = "Lanjut >";

                Label lblWelcome = new Label
                {
                    Text = "Selamat Datang di Pemasangan MASAYUME LMS & CBT",
                    Font = new Font("Segoe UI", 12F, FontStyle.Bold),
                    ForeColor = Color.FromArgb(15, 23, 42),
                    Location = new Point(25, 20),
                    AutoSize = true
                };

                Label lblDesc = new Label
                {
                    Text = "Program ini akan memasang aplikasi MASAYUME LMS & CBT di komputer Anda.\r\n\r\n" +
                           "Aplikasi ini memberikan akses langsung ke platform pembelajaran bahasa Jepang, modul kotoba, kartu kanji, dan ujian simulasi CBT N4 resmi tanpa perlu membuka browser manual.\r\n\r\n" +
                           "Klik 'Lanjut' untuk melanjutkan proses pemasangan.",
                    Font = new Font("Segoe UI", 9.5F),
                    ForeColor = Color.FromArgb(51, 65, 85),
                    Location = new Point(25, 60),
                    Size = new Size(470, 150)
                };

                contentPanel.Controls.Add(lblWelcome);
                contentPanel.Controls.Add(lblDesc);
            }
            else if (step == 1) // Destination Folder
            {
                bannerSub.Text = "Pilih lokasi folder tujuan untuk memasang program.";
                btnNext.Text = "Lanjut >";

                Label lblPrompt = new Label
                {
                    Text = "Setup akan memasang MASAYUME LMS & CBT ke dalam folder berikut.\r\n" +
                           "Untuk memilih folder lain, klik tombol 'Telusuri'.",
                    Location = new Point(25, 20),
                    Size = new Size(470, 45)
                };

                Label lblFolder = new Label
                {
                    Text = "Folder Tujuan:",
                    Font = new Font("Segoe UI", 9F, FontStyle.Bold),
                    Location = new Point(25, 75),
                    AutoSize = true
                };

                txtDestPath = new TextBox
                {
                    Text = defaultInstallPath,
                    Location = new Point(25, 100),
                    Size = new Size(365, 27),
                    ReadOnly = false
                };

                btnBrowse = new Button
                {
                    Text = "Telusuri...",
                    Location = new Point(400, 98),
                    Size = new Size(90, 29),
                    UseVisualStyleBackColor = true
                };
                btnBrowse.Click += (s, e) =>
                {
                    using (FolderBrowserDialog fbd = new FolderBrowserDialog())
                    {
                        fbd.SelectedPath = txtDestPath.Text;
                        if (fbd.ShowDialog() == DialogResult.OK)
                        {
                            txtDestPath.Text = Path.Combine(fbd.SelectedPath, "MasayumeLMS");
                        }
                    }
                };

                Label lblSpace = new Label
                {
                    Text = "Ruang penyimpanan yang dibutuhkan: ~2 MB\r\nSemua hak akses pengguna lokal didukung penuh.",
                    ForeColor = Color.FromArgb(100, 116, 139),
                    Location = new Point(25, 145),
                    Size = new Size(470, 40)
                };

                contentPanel.Controls.Add(lblPrompt);
                contentPanel.Controls.Add(lblFolder);
                contentPanel.Controls.Add(txtDestPath);
                contentPanel.Controls.Add(btnBrowse);
                contentPanel.Controls.Add(lblSpace);
            }
            else if (step == 2) // Options
            {
                bannerSub.Text = "Pilih opsi tambahan yang ingin Anda aktifkan.";
                btnNext.Text = "Pasang";

                Label lblOpt = new Label
                {
                    Text = "Pilih shortcut dan komponen yang ingin dibuat oleh Setup:",
                    Location = new Point(25, 20),
                    Size = new Size(470, 35)
                };

                chkDesktop = new CheckBox
                {
                    Text = "Buat jalan pintas (Shortcut) di Layar Desktop",
                    Checked = true,
                    Location = new Point(25, 65),
                    Size = new Size(450, 25),
                    Font = new Font("Segoe UI", 9.5F)
                };

                chkStartMenu = new CheckBox
                {
                    Text = "Buat jalan pintas di Menu Mulai (Start Menu Windows)",
                    Checked = true,
                    Location = new Point(25, 105),
                    Size = new Size(450, 25),
                    Font = new Font("Segoe UI", 9.5F)
                };

                Label lblNote = new Label
                {
                    Text = "Catatan: Program akan otomatis didaftarkan di daftar aplikasi resmi Windows (Apps & Features / Control Panel) sehingga dapat di-uninstall dengan mudah sewaktu-waktu.",
                    ForeColor = Color.FromArgb(100, 116, 139),
                    Location = new Point(25, 150),
                    Size = new Size(470, 60)
                };

                contentPanel.Controls.Add(lblOpt);
                contentPanel.Controls.Add(chkDesktop);
                contentPanel.Controls.Add(chkStartMenu);
                contentPanel.Controls.Add(lblNote);
            }
            else if (step == 3) // Installing Progress
            {
                bannerSub.Text = "Sedang memasang berkas ke komputer Anda...";
                btnNext.Enabled = false;
                btnBack.Enabled = false;
                btnCancel.Enabled = false;

                lblStatus = new Label
                {
                    Text = "Mengekstrak berkas program Masayume...",
                    Location = new Point(25, 40),
                    Size = new Size(470, 30),
                    Font = new Font("Segoe UI", 9.5F)
                };

                prgBar = new ProgressBar
                {
                    Location = new Point(25, 80),
                    Size = new Size(470, 25),
                    Style = ProgressBarStyle.Marquee,
                    MarqueeAnimationSpeed = 30
                };

                contentPanel.Controls.Add(lblStatus);
                contentPanel.Controls.Add(prgBar);

                // Run installation in background thread
                ThreadPool.QueueUserWorkItem((state) =>
                {
                    Thread.Sleep(800); // Visual polish
                    PerformInstallation();
                    this.Invoke((MethodInvoker)delegate
                    {
                        ShowStep(4);
                    });
                });
            }
            else if (step == 4) // Finish
            {
                bannerSub.Text = "Pemasangan telah selesai!";
                btnNext.Text = "Selesai";
                btnNext.Enabled = true;
                btnBack.Visible = false;
                btnCancel.Visible = false;

                Label lblDone = new Label
                {
                    Text = "Instalasi Berhasil Selesai!",
                    Font = new Font("Segoe UI", 13F, FontStyle.Bold),
                    ForeColor = Color.FromArgb(22, 101, 52), // Green
                    Location = new Point(25, 20),
                    AutoSize = true
                };

                Label lblFinishDesc = new Label
                {
                    Text = "MASAYUME LMS & CBT telah sukses dipasang di komputer Anda.\r\n\r\n" +
                           "Anda dapat menjalankan aplikasi ini kapan saja melalui jalan pintas Desktop atau Menu Mulai.",
                    Font = new Font("Segoe UI", 9.5F),
                    ForeColor = Color.FromArgb(51, 65, 85),
                    Location = new Point(25, 60),
                    Size = new Size(470, 75)
                };

                chkLaunchNow = new CheckBox
                {
                    Text = "Jalankan MASAYUME LMS & CBT sekarang",
                    Checked = true,
                    Font = new Font("Segoe UI", 10F, FontStyle.Bold),
                    ForeColor = Color.FromArgb(15, 23, 42),
                    Location = new Point(25, 145),
                    Size = new Size(450, 30)
                };

                contentPanel.Controls.Add(lblDone);
                contentPanel.Controls.Add(lblFinishDesc);
                contentPanel.Controls.Add(chkLaunchNow);
            }
        }

        private void BtnNext_Click(object sender, EventArgs e)
        {
            if (currentStep == 4) // Selesai
            {
                if (chkLaunchNow != null && chkLaunchNow.Checked)
                {
                    string targetExe = Path.Combine(txtDestPath.Text, "Masayume.exe");
                    if (File.Exists(targetExe))
                    {
                        try { System.Diagnostics.Process.Start(targetExe); } catch { }
                    }
                }
                this.Close();
                return;
            }

            ShowStep(currentStep + 1);
        }

        private void PerformInstallation()
        {
            string installDir = txtDestPath.Text.Trim();
            if (!Directory.Exists(installDir))
            {
                Directory.CreateDirectory(installDir);
            }

            // 1. Ekstrak Masayume.exe, uninstall.exe, masayume_3d.ico
            ExtractResource("Masayume.exe", Path.Combine(installDir, "Masayume.exe"));
            ExtractResource("uninstall.exe", Path.Combine(installDir, "uninstall.exe"));
            ExtractResource("masayume_3d.ico", Path.Combine(installDir, "masayume_3d.ico"));

            string exePath = Path.Combine(installDir, "Masayume.exe");
            string icoPath = Path.Combine(installDir, "masayume_3d.ico");

            // 2. Buat Shortcut Desktop (Hapus shortcut lama jika ada agar icon cache Windows ter-reset)
            if (chkDesktop.Checked)
            {
                string desktop = Environment.GetFolderPath(Environment.SpecialFolder.Desktop);
                string lnkPath = Path.Combine(desktop, "MASAYUME - LMS & CBT.lnk");
                try { if (File.Exists(lnkPath)) File.Delete(lnkPath); } catch { }
                CreateShortcut(
                    lnkPath,
                    exePath,
                    installDir,
                    icoPath,
                    "Aplikasi LMS & CBT Masayume Penyaluran Jepang"
                );
            }

            // 3. Buat Shortcut Start Menu
            if (chkStartMenu.Checked)
            {
                string programs = Environment.GetFolderPath(Environment.SpecialFolder.Programs);
                string lnkPath = Path.Combine(programs, "MASAYUME - LMS & CBT.lnk");
                try { if (File.Exists(lnkPath)) File.Delete(lnkPath); } catch { }
                CreateShortcut(
                    lnkPath,
                    exePath,
                    installDir,
                    icoPath,
                    "Aplikasi LMS & CBT Masayume Penyaluran Jepang"
                );
            }

            // 4. Force Windows Explorer to flush icon cache and redraw desktop icons immediately
            try
            {
                SHChangeNotify(0x08000000, 0x0000, IntPtr.Zero, IntPtr.Zero);
            }
            catch { }

            // 4. Daftarkan di Windows Registry (Add/Remove Programs)
            try
            {
                string uninstKey = @"Software\Microsoft\Windows\CurrentVersion\Uninstall\MasayumeLMS";
                using (RegistryKey key = Registry.CurrentUser.CreateSubKey(uninstKey))
                {
                    if (key != null)
                    {
                        key.SetValue("DisplayName", "MASAYUME - LMS & CBT Terpadu LPK Penyaluran Jepang");
                        key.SetValue("DisplayVersion", "1.0.0");
                        key.SetValue("Publisher", "LPK Masayume");
                        key.SetValue("DisplayIcon", icoPath + ",0");
                        key.SetValue("InstallLocation", installDir);
                        key.SetValue("UninstallString", "\"" + Path.Combine(installDir, "uninstall.exe") + "\"");
                        key.SetValue("HelpLink", "https://masayume.id");
                        key.SetValue("URLInfoAbout", "https://masayume.id");
                        key.SetValue("EstimatedSize", 18);
                    }
                }
            }
            catch { }
        }

        private void ExtractResource(string resName, string destPath)
        {
            Assembly asm = Assembly.GetExecutingAssembly();
            using (Stream s = asm.GetManifestResourceStream(resName))
            {
                if (s != null)
                {
                    using (FileStream fs = new FileStream(destPath, FileMode.Create, FileAccess.Write))
                    {
                        byte[] buffer = new byte[8192];
                        int bytesRead;
                        while ((bytesRead = s.Read(buffer, 0, buffer.Length)) > 0)
                        {
                            fs.Write(buffer, 0, bytesRead);
                        }
                    }
                }
            }
        }

        private void CreateShortcut(string shortcutPath, string targetPath, string workDir, string iconPath, string desc)
        {
            try
            {
                Type shellType = Type.GetTypeFromProgID("WScript.Shell");
                dynamic shell = Activator.CreateInstance(shellType);
                dynamic shortcut = shell.CreateShortcut(shortcutPath);
                shortcut.TargetPath = targetPath;
                shortcut.WorkingDirectory = workDir;
                shortcut.IconLocation = iconPath + ",0";
                shortcut.Description = desc;
                shortcut.Save();
            }
            catch { }
        }

        [STAThread]
        public static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new SetupForm());
        }
    }
}
