// Minimal, safe bridge. Exposes a tiny read-only flag the web app can use to
// detect it's running inside the desktop shell (e.g. to tweak copy/branding).
const { contextBridge } = require('electron');

contextBridge.exposeInMainWorld('opesDesktop', {
  isDesktop: true,
  platform: process.platform,
  version: process.versions.electron,
});
