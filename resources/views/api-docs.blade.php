<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPESBooks API Reference</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #0f172a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .topbar { display: none !important; }
        #swagger-ui .swagger-ui { background: #0f172a; }

        /* Brand header */
        .api-header {
            background: linear-gradient(135deg, #0A192F 0%, #0f172a 100%);
            border-bottom: 1px solid rgba(245,158,11,0.2);
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .api-header .logo {
            font-size: 22px;
            font-weight: 900;
            color: #fff;
            letter-spacing: -1px;
        }
        .api-header .logo span { color: #F59E0B; }
        .api-header .badge {
            background: rgba(245,158,11,0.12);
            border: 1px solid rgba(245,158,11,0.3);
            color: #F59E0B;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .api-header nav a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            margin-left: 24px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .api-header nav a:hover { color: #F59E0B; }

        #swagger-ui {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Dark theme overrides */
        .swagger-ui .info .title { color: #f1f5f9 !important; }
        .swagger-ui .info p, .swagger-ui .info li { color: #94a3b8 !important; }
        .swagger-ui .info a { color: #F59E0B !important; }
        .swagger-ui .scheme-container { background: #1e293b !important; padding: 16px !important; border-radius: 8px !important; }
        .swagger-ui .opblock-tag { color: #e2e8f0 !important; border-color: rgba(255,255,255,0.08) !important; }
        .swagger-ui .opblock { border-radius: 8px !important; margin-bottom: 8px !important; }
        .swagger-ui .opblock .opblock-summary-path { color: #f1f5f9 !important; }
        .swagger-ui .opblock .opblock-summary-description { color: #94a3b8 !important; }
        .swagger-ui section.models { background: #1e293b !important; border-radius: 8px !important; }
        .swagger-ui .model-title { color: #f1f5f9 !important; }
        .swagger-ui .model { color: #cbd5e1 !important; }
        .swagger-ui table thead tr th { color: #94a3b8 !important; border-color: rgba(255,255,255,0.08) !important; }
        .swagger-ui .btn.authorize { background: #F59E0B !important; border-color: #F59E0B !important; color: #0A192F !important; font-weight: 700 !important; }
    </style>
</head>
<body>

<div class="api-header">
    <div style="display:flex;align-items:center;gap:16px">
        <div class="logo">OPES<span>BOOKS</span></div>
        <span class="badge">API v1</span>
    </div>
    <nav>
        <a href="/app">← Dashboard</a>
        <a href="/openapi.yaml">Download YAML</a>
        <a href="https://opesbooks.cm" target="_blank">opesbooks.cm</a>
    </nav>
</div>

<div id="swagger-ui"></div>

<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script>
    SwaggerUIBundle({
        url: '/openapi.yaml',
        dom_id: '#swagger-ui',
        presets: [SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset],
        layout: 'BaseLayout',
        deepLinking: true,
        displayRequestDuration: true,
        filter: true,
        tryItOutEnabled: true,
        requestInterceptor: (req) => {
            // Persist auth token across try-it-out calls
            const token = localStorage.getItem('opes_token');
            if (token && !req.headers['Authorization']) {
                req.headers['Authorization'] = 'Bearer ' + token;
            }
            return req;
        },
    });
</script>
</body>
</html>
