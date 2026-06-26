# OPESBooks Desktop (Electron)

A native desktop wrapper around the OPESBooks cloud app. It is **offline-first**:
after the first online login, the PWA service worker keeps the app shell + data
cached, and the in-app **outbox** queues every create/edit you make offline and
replays them automatically when the connection returns. Users log in with their
**normal cloud credentials** — the same account they use in the browser.

## How offline works (architecture)

```
┌─────────────────────────────┐        online        ┌──────────────────────┐
│  OPESBooks Desktop (Electron)│  ─────────────────▶  │   Cloud (Laravel API)│
│  • loads app.opesbooks.cm    │                      │   MySQL, queues, DGI │
│  • persistent session/cache  │  ◀─────────────────  │                      │
│  • service worker (shell)    │       sync           └──────────────────────┘
│  • outbox (offline writes)   │
└─────────────────────────────┘
        works with OR without internet
```

- **Reads offline:** served from the service-worker cache (last-known data).
- **Writes offline:** queued in `localStorage` (`opes_outbox`), shown as an
  "N pending" badge, and POSTed to the cloud the moment you reconnect
  (also retried on load and every 30s).
- **Login:** cloud credentials, validated against the cloud once; the Sanctum
  token is cached in the persistent partition so you stay signed in offline.

## Configure the cloud URL

Point the app at your cloud deployment in **one** of these ways:

1. `desktop/config.json` → `{ "appUrl": "https://app.opesbooks.cm" }`
2. Environment variable `OPES_APP_URL` (overrides config.json)

## Run in development

```bash
cd desktop
npm install
# point at your local Laravel server:
npm run dev          # OPES_APP_URL=http://127.0.0.1:8000 electron .
```

## Build installers

```bash
cd desktop
npm install
npm run build:win    # → desktop/dist/OPESBooks Setup x.y.z.exe  (NSIS installer)
npm run build:mac    # → .dmg   (run on macOS)
npm run build:linux  # → .AppImage / .deb
```

The Windows build produces a normal double-click installer your subscribers
download and run. The icon is `desktop/build/icon.png` (replace with your own
512×512 PNG to rebrand).

## Code signing (production)

For a trusted install (no SmartScreen warning), sign the Windows build with an
EV/OV code-signing certificate via electron-builder env vars
(`CSC_LINK`, `CSC_KEY_PASSWORD`) — see electron-builder docs. macOS needs an
Apple Developer ID + notarization.

## Auto-update (optional, recommended)

Add `electron-updater` + an update feed (e.g. GitHub Releases or an S3 bucket)
so installed apps update themselves. Wire `autoUpdater.checkForUpdatesAndNotify()`
in `main.js` on `app.whenReady()`.
