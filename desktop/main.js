// OPESBooks — Electron desktop wrapper
// Loads the cloud app in a native window. Offline-first is handled by the web
// app itself: the PWA service worker caches the shell + data, and the in-app
// outbox queues writes while offline and replays them on reconnect.
//
// Config resolution order for the cloud URL:
//   1. OPES_APP_URL environment variable
//   2. desktop/config.json  -> { "appUrl": "https://app.opesbooks.cm" }
//   3. default below
const { app, BrowserWindow, shell, Menu, dialog } = require('electron');
const path = require('path');
const fs = require('fs');

const DEFAULT_URL = 'https://app.opesbooks.cm';

function resolveAppUrl() {
  if (process.env.OPES_APP_URL) return process.env.OPES_APP_URL;
  try {
    const cfg = JSON.parse(fs.readFileSync(path.join(__dirname, 'config.json'), 'utf8'));
    if (cfg.appUrl) return cfg.appUrl;
  } catch (_) { /* no config file — fall through */ }
  return DEFAULT_URL;
}

const APP_URL = resolveAppUrl();
let mainWindow = null;

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1280,
    height: 820,
    minWidth: 960,
    minHeight: 600,
    backgroundColor: '#010048',
    title: 'OPESBooks',
    icon: path.join(__dirname, 'build', 'icon.png'),
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      // A persistent partition keeps the Sanctum token + service-worker cache
      // across launches, so users stay logged in and work offline.
      partition: 'persist:opesbooks',
      contextIsolation: true,
      nodeIntegration: false,
    },
    show: false,
  });

  mainWindow.once('ready-to-show', () => mainWindow.show());

  // Open external links (mailto:, https to other sites) in the default browser.
  mainWindow.webContents.setWindowOpenHandler(({ url }) => {
    if (!url.startsWith(APP_URL)) { shell.openExternal(url); return { action: 'deny' }; }
    return { action: 'allow' };
  });

  // First-launch-while-offline fallback (no service-worker cache yet).
  mainWindow.webContents.on('did-fail-load', (_e, errorCode, _desc, validatedURL, isMainFrame) => {
    // -3 = ERR_ABORTED (normal during redirects); ignore.
    if (isMainFrame && errorCode !== -3) {
      mainWindow.loadFile(path.join(__dirname, 'offline.html'));
    }
  });

  mainWindow.loadURL(APP_URL).catch(() => {
    mainWindow.loadFile(path.join(__dirname, 'offline.html'));
  });
}

function buildMenu() {
  const template = [
    {
      label: 'Fichier',
      submenu: [
        { label: 'Recharger', accelerator: 'CmdOrCtrl+R', click: () => mainWindow && mainWindow.loadURL(APP_URL) },
        { type: 'separator' },
        { role: 'quit', label: 'Quitter' },
      ],
    },
    { label: 'Édition', submenu: [ { role: 'cut' }, { role: 'copy' }, { role: 'paste' }, { role: 'selectAll' } ] },
    {
      label: 'Affichage',
      submenu: [ { role: 'resetZoom' }, { role: 'zoomIn' }, { role: 'zoomOut' }, { type: 'separator' }, { role: 'togglefullscreen' } ],
    },
    {
      label: 'Aide',
      submenu: [
        { label: 'À propos d\'OPESBooks', click: () => dialog.showMessageBox(mainWindow, {
            type: 'info', title: 'OPESBooks',
            message: 'OPESBooks Desktop', detail: `Connecté à : ${APP_URL}\n© Opesware, Douala, Cameroun`,
        }) },
      ],
    },
  ];
  Menu.setApplicationMenu(Menu.buildFromTemplate(template));
}

// Single-instance lock so a second launch focuses the existing window.
if (!app.requestSingleInstanceLock()) {
  app.quit();
} else {
  app.on('second-instance', () => {
    if (mainWindow) { if (mainWindow.isMinimized()) mainWindow.restore(); mainWindow.focus(); }
  });

  app.whenReady().then(() => {
    buildMenu();
    createWindow();
    app.on('activate', () => { if (BrowserWindow.getAllWindows().length === 0) createWindow(); });
  });

  app.on('window-all-closed', () => { if (process.platform !== 'darwin') app.quit(); });
}
