const { app, BrowserWindow, Menu, globalShortcut, shell } = require('electron');
const path = require('path');

let mainWindow;

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1366,
        height: 768,
        minWidth: 1024,
        minHeight: 650,
        title: 'MASAYUME - Sistem LMS & CBT LPK Penyaluran Jepang',
        icon: path.join(__dirname, '../app.ico'),
        backgroundColor: '#0f172a',
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js'),
        },
    });

    mainWindow.maximize();

    // Load Live Web LMS
    mainWindow.loadURL('https://masayume.id');

    // Handle external links (open in default system browser)
    mainWindow.webContents.setWindowOpenHandler(({ url }) => {
        if (!url.startsWith('https://masayume.id')) {
            shell.openExternal(url);
            return { action: 'deny' };
        }
        return { action: 'allow' };
    });

    // Custom Menu
    const template = [
        {
            label: 'Aplikasi',
            submenu: [
                {
                    label: 'Beranda (Home)',
                    accelerator: 'CmdOrCtrl+H',
                    click: () => mainWindow.loadURL('https://masayume.id/dashboard'),
                },
                {
                    label: 'Muat Ulang (Refresh)',
                    accelerator: 'F5',
                    click: () => mainWindow.reload(),
                },
                { type: 'separator' },
                {
                    label: 'Keluar',
                    accelerator: 'CmdOrCtrl+Q',
                    click: () => app.quit(),
                },
            ],
        },
        {
            label: 'Tampilan',
            submenu: [
                {
                    label: 'Layar Penuh (Fullscreen CBT)',
                    accelerator: 'F11',
                    click: () => mainWindow.setFullScreen(!mainWindow.isFullScreen()),
                },
                { type: 'separator' },
                { role: 'zoomIn', label: 'Perbesar (+)' },
                { role: 'zoomOut', label: 'Perkecil (-)' },
                { role: 'resetZoom', label: 'Ukuran Normal (100%)' },
            ],
        },
    ];

    const menu = Menu.buildFromTemplate(template);
    Menu.setApplicationMenu(menu);

    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}

// Single instance lock
const gotTheLock = app.requestSingleInstanceLock();
if (!gotTheLock) {
    app.quit();
} else {
    app.on('second-instance', () => {
        if (mainWindow) {
            if (mainWindow.isMinimized()) mainWindow.restore();
            mainWindow.focus();
        }
    });

    app.whenReady().then(() => {
        createWindow();

        app.on('activate', () => {
            if (BrowserWindow.getAllWindows().length === 0) createWindow();
        });
    });
}

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') app.quit();
});
