<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opes Books — Cabinet Comptable</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --c-bg:           #0B1120;
            --c-surface:      #151F2E;
            --c-raised:       #1C2A3A;
            --c-border:       #253347;
            --c-border-strong:#334155;
            --c-accent:       #F59E0B;
            --c-accent-dim:   #D97706;
            --c-text:         #F0F4FA;
            --c-muted:        #8B9EC0;
            --c-faint:        #4E647E;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; background: var(--c-bg); color: var(--c-text); min-height: 100vh; }
        .btn-primary { background: var(--c-accent); color: #0B1120; font-weight: 600; padding: 0.5rem 1.25rem; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem; transition: background 0.15s; }
        .btn-primary:hover { background: var(--c-accent-dim); }
        .btn-ghost { background: transparent; color: var(--c-muted); border: 1px solid var(--c-border); padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer; font-size: 0.875rem; transition: all 0.15s; }
        .btn-ghost:hover { border-color: var(--c-border-strong); color: var(--c-text); }
        .card { background: var(--c-surface); border: 1px solid var(--c-border); border-radius: 0.75rem; }
        .badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid transparent; }
        .badge-ok      { background: rgba(34,197,94,0.12); color: #4ade80; border-color: rgba(34,197,94,0.25); }
        .badge-warning { background: rgba(245,158,11,0.12); color: #fbbf24; border-color: rgba(245,158,11,0.25); }
        .badge-overdue { background: rgba(239,68,68,0.12); color: #f87171; border-color: rgba(239,68,68,0.25); }
        .badge-unknown { background: rgba(100,116,139,0.12); color: #94a3b8; border-color: rgba(100,116,139,0.25); }
        .badge-pending { background: rgba(59,130,246,0.12); color: #60a5fa; border-color: rgba(59,130,246,0.25); }
        input, select, textarea { background: var(--c-raised); border: 1px solid var(--c-border); color: var(--c-text); border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; width: 100%; outline: none; transition: border-color 0.15s; }
        input:focus, select:focus, textarea:focus { border-color: var(--c-accent); }
        label { font-size: 0.8rem; color: var(--c-muted); display: block; margin-bottom: 0.35rem; }
        .form-group { margin-bottom: 1rem; }
        .tab-btn { padding: 0.5rem 1.25rem; border-radius: 0.5rem; border: none; background: transparent; color: var(--c-muted); cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: all 0.15s; }
        .tab-btn.active { background: var(--c-raised); color: var(--c-text); }
        .tab-btn:hover:not(.active) { color: var(--c-text); }
        .skeleton { background: var(--c-raised); border-radius: 0.375rem; animation: pulse 1.5s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .urgency-high   { color: #f87171; }
        .urgency-medium { color: #fbbf24; }
        .urgency-low    { color: var(--c-muted); }
    </style>
</head>
<body x-data="firmApp()" x-init="init()">

{{-- ── Top Nav ──────────────────────────────────────────────────────────── --}}
<nav style="background:var(--c-surface);border-bottom:1px solid var(--c-border);position:sticky;top:0;z-index:40">
    <div style="max-width:1280px;margin:auto;padding:0 1.5rem;display:flex;align-items:center;gap:1rem;height:3.5rem">
        <span style="font-weight:700;font-size:1.05rem;color:var(--c-accent)">Opes Books</span>
        <span style="background:var(--c-raised);border:1px solid var(--c-border);color:var(--c-muted);font-size:0.7rem;padding:0.1rem 0.5rem;border-radius:9999px;font-weight:600">CABINET</span>
        <span style="flex:1"></span>
        <template x-if="firm">
            <span style="font-size:0.85rem;color:var(--c-muted)" x-text="firm.name"></span>
        </template>
        <a href="/app" style="font-size:0.8rem;color:var(--c-muted);text-decoration:none" title="Retour à la comptabilité">← /app</a>
        <button class="btn-ghost" style="font-size:0.8rem" @click="logout()">Déconnexion</button>
    </div>
</nav>

{{-- ── Setup wizard (no firm yet) ──────────────────────────────────────── --}}
<div x-show="loaded && !firm" style="max-width:520px;margin:6rem auto;padding:0 1.5rem">
    <div class="card" style="padding:2rem">
        <h1 style="font-size:1.25rem;font-weight:700;margin-bottom:0.5rem">Créer votre Cabinet</h1>
        <p style="color:var(--c-muted);font-size:0.875rem;margin-bottom:1.75rem">Gérez plusieurs sociétés clientes depuis un seul espace professionnel.</p>
        <div class="form-group">
            <label>Raison sociale *</label>
            <input x-model="setup.name" placeholder="Cabinet ABC & Associés" />
        </div>
        <div class="form-group">
            <label>N° OECAM (optionnel)</label>
            <input x-model="setup.oecam_number" placeholder="OEC-2024-XXXX" />
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
                <label>Email</label>
                <input x-model="setup.email" type="email" />
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input x-model="setup.phone" placeholder="+237 6..." />
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
                <label>Adresse</label>
                <input x-model="setup.address" />
            </div>
            <div class="form-group">
                <label>Ville</label>
                <input x-model="setup.city" placeholder="Douala" />
            </div>
        </div>
        <button class="btn-primary" style="width:100%;margin-top:0.5rem" @click="createFirm()" :disabled="creating">
            <span x-show="!creating">Créer le Cabinet</span>
            <span x-show="creating">Création…</span>
        </button>
        <p x-show="setupError" x-text="setupError" style="color:#f87171;font-size:0.8rem;margin-top:0.75rem"></p>
    </div>
</div>

{{-- ── Main portal ──────────────────────────────────────────────────────── --}}
<div x-show="loaded && firm" style="max-width:1280px;margin:0 auto;padding:1.5rem">

    {{-- Firm header --}}
    <div style="display:flex;align-items:flex-start;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap">
        <div style="flex:1;min-width:0">
            <h1 style="font-size:1.5rem;font-weight:700" x-text="firm?.name"></h1>
            <p style="color:var(--c-muted);font-size:0.85rem;margin-top:0.2rem">
                <span x-show="firm?.oecam_number">OECAM: <span x-text="firm?.oecam_number"></span> · </span>
                <span x-text="firm?.city"></span>
            </p>
        </div>
        <button class="btn-primary" @click="showAddClient = true">+ Ajouter un client</button>
    </div>

    {{-- KPI strip --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem">
        <div class="card" style="padding:1rem">
            <p style="font-size:0.75rem;color:var(--c-muted);margin-bottom:0.25rem">Clients actifs</p>
            <p style="font-size:1.75rem;font-weight:700" x-text="stats.total_clients ?? '—'"></p>
            <p style="font-size:0.7rem;color:var(--c-faint)" x-text="'/ ' + (firm?.max_clients ?? '—') + ' max'"></p>
        </div>
        <div class="card" style="padding:1rem">
            <p style="font-size:0.75rem;color:var(--c-muted);margin-bottom:0.25rem">Déclarations en retard</p>
            <p style="font-size:1.75rem;font-weight:700" :style="stats.overdue_filings > 0 ? 'color:#f87171' : ''" x-text="stats.overdue_filings ?? '—'"></p>
        </div>
        <div class="card" style="padding:1rem">
            <p style="font-size:0.75rem;color:var(--c-muted);margin-bottom:0.25rem">DGI sync en attente</p>
            <p style="font-size:1.75rem;font-weight:700" :style="stats.dgi_sync_pending > 0 ? 'color:#fbbf24' : ''" x-text="stats.dgi_sync_pending ?? '—'"></p>
        </div>
        <div class="card" style="padding:1rem">
            <p style="font-size:0.75rem;color:var(--c-muted);margin-bottom:0.25rem">Mon rôle</p>
            <p style="font-size:1rem;font-weight:600;margin-top:0.35rem" x-text="firmRole || '—'"></p>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;gap:0.25rem;margin-bottom:1.5rem;background:var(--c-surface);border:1px solid var(--c-border);border-radius:0.625rem;padding:0.25rem;width:fit-content">
        <button class="tab-btn" :class="tab==='portfolio' && 'active'" @click="tab='portfolio'">Portefeuille</button>
        <button class="tab-btn" :class="tab==='tasks' && 'active'" @click="tab='tasks';loadTasks()">Tâches & Délais</button>
        <button class="tab-btn" :class="tab==='reports' && 'active'" @click="tab='reports';loadReport()">Rapports</button>
        <button class="tab-btn" :class="tab==='team' && 'active'" @click="tab='team';loadStaff()">Équipe</button>
        <button class="tab-btn" :class="tab==='settings' && 'active'" @click="tab='settings'">Paramètres</button>
    </div>

    {{-- ── Tab: Portefeuille ──────────────────────────────────────────── --}}
    <div x-show="tab === 'portfolio'">
        {{-- Search --}}
        <div style="margin-bottom:1rem;display:flex;gap:0.75rem;flex-wrap:wrap">
            <input x-model="clientSearch" placeholder="Rechercher un client…" style="max-width:320px" />
            <select x-model="clientFilter" style="max-width:200px">
                <option value="">Tous les statuts</option>
                <option value="OVERDUE">En retard</option>
                <option value="WARNING">Avertissement</option>
                <option value="OK">À jour</option>
            </select>
        </div>

        {{-- Loading skeletons --}}
        <div x-show="loadingPortfolio" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem">
            <template x-for="i in 6" :key="i">
                <div class="card" style="padding:1.25rem">
                    <div class="skeleton" style="height:1rem;width:60%;margin-bottom:0.5rem"></div>
                    <div class="skeleton" style="height:0.75rem;width:40%;margin-bottom:1rem"></div>
                    <div style="display:flex;gap:0.5rem">
                        <div class="skeleton" style="height:1.5rem;width:4rem;border-radius:9999px"></div>
                        <div class="skeleton" style="height:1.5rem;width:4rem;border-radius:9999px"></div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Client grid --}}
        <div x-show="!loadingPortfolio" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem">
            <template x-for="client in filteredClients" :key="client.id">
                <div class="card" style="padding:1.25rem;display:flex;flex-direction:column;gap:0.75rem">
                    {{-- Header row --}}
                    <div style="display:flex;align-items:flex-start;gap:0.75rem">
                        <div style="flex:1;min-width:0">
                            <p style="font-weight:600;font-size:0.95rem" x-text="client.name"></p>
                            <p style="font-size:0.75rem;color:var(--c-muted)" x-text="'NIU: ' + (client.niu || '—')"></p>
                        </div>
                        <span class="badge" :class="overallBadgeClass(client.compliance?.overall)" x-text="client.compliance?.overall || '—'"></span>
                    </div>

                    {{-- Compliance badges --}}
                    <div style="display:flex;gap:0.4rem;flex-wrap:wrap">
                        <span class="badge" :class="statusBadgeClass(client.compliance?.tva)" x-text="'TVA: ' + (client.compliance?.tva || '—')"></span>
                        <span class="badge" :class="statusBadgeClass(client.compliance?.dgi)" x-text="'DGI: ' + (client.compliance?.dgi || '—')"></span>
                        <span class="badge" :class="statusBadgeClass(client.compliance?.dsf)" x-text="'DSF: ' + (client.compliance?.dsf || '—')"></span>
                    </div>

                    {{-- Meta --}}
                    <div style="font-size:0.75rem;color:var(--c-faint)">
                        <span x-text="client.engagement_type"></span>
                        <span x-show="client.assigned_accountant"> · <span x-text="client.assigned_accountant"></span></span>
                        <span x-show="client.last_activity_human"> · Dernière activité <span x-text="client.last_activity_human"></span></span>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;gap:0.5rem;margin-top:0.25rem">
                        <button class="btn-primary" style="flex:1;font-size:0.8rem;padding:0.4rem 0.75rem" @click="openClient(client)">
                            Ouvrir le Dossier
                        </button>
                        <button class="btn-ghost" style="font-size:0.8rem;padding:0.4rem 0.75rem" @click="editClient(client)">⋯</button>
                    </div>
                </div>
            </template>

            {{-- Empty state --}}
            <div x-show="!loadingPortfolio && filteredClients.length === 0" style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--c-muted)">
                <p style="font-size:1.5rem;margin-bottom:0.5rem">📂</p>
                <p x-show="clientSearch || clientFilter">Aucun client ne correspond aux filtres.</p>
                <p x-show="!clientSearch && !clientFilter">Aucun client dans le portefeuille. Ajoutez votre premier client.</p>
            </div>
        </div>
    </div>

    {{-- ── Tab: Tâches & Délais ─────────────────────────────────────── --}}
    <div x-show="tab === 'tasks'">
        <div x-show="loadingTasks" style="text-align:center;padding:3rem;color:var(--c-muted)">Chargement du calendrier…</div>
        <div x-show="!loadingTasks">
            <template x-for="group in taskGroups" :key="group.date">
                <div style="margin-bottom:1.5rem">
                    <h3 style="font-size:0.8rem;font-weight:600;color:var(--c-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem" x-text="group.label"></h3>
                    <div style="display:flex;flex-direction:column;gap:0.5rem">
                        <template x-for="(task, idx) in group.entries" :key="idx">
                            <div class="card" style="padding:0.875rem 1.25rem;display:flex;align-items:center;gap:1rem">
                                <div style="width:3rem;text-align:center">
                                    <span style="font-size:0.65rem;font-weight:700;padding:0.15rem 0.4rem;border-radius:0.25rem;border:1px solid"
                                        :style="taskTypeBorder(task.type)"
                                        x-text="task.type"></span>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <p style="font-size:0.875rem;font-weight:500" x-text="task.label"></p>
                                    <p style="font-size:0.75rem;color:var(--c-muted)" x-text="task.company"></p>
                                </div>
                                <span class="badge" :class="taskStatusBadge(task.status)" x-text="task.status"></span>
                                <span :class="'urgency-' + task.urgency.toLowerCase()" style="font-size:0.7rem;font-weight:600;min-width:3rem;text-align:right" x-text="task.urgency"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <div x-show="taskGroups.length === 0" style="text-align:center;padding:3rem;color:var(--c-muted)">
                <p>Aucune tâche dans les 90 prochains jours.</p>
            </div>
        </div>
    </div>

    {{-- ── Tab: Rapports consolidés ────────────────────────────────── --}}
    <div x-show="tab === 'reports'">
        {{-- Period picker --}}
        <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;margin-bottom:1.25rem">
            <div style="display:flex;gap:0.5rem;align-items:center">
                <label style="margin:0;white-space:nowrap">Du</label>
                <input x-model="reportFrom" type="date" style="max-width:160px" />
            </div>
            <div style="display:flex;gap:0.5rem;align-items:center">
                <label style="margin:0;white-space:nowrap">Au</label>
                <input x-model="reportTo" type="date" style="max-width:160px" />
            </div>
            <button class="btn-primary" @click="loadReport(true)">Actualiser</button>
        </div>

        <div x-show="loadingReport" style="text-align:center;padding:3rem;color:var(--c-muted)">Chargement…</div>

        <div x-show="!loadingReport && report">
            {{-- Consolidated KPIs --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;margin-bottom:1.5rem">
                <div class="card" style="padding:1rem">
                    <p style="font-size:0.72rem;color:var(--c-muted);margin-bottom:0.25rem;text-transform:uppercase;letter-spacing:0.05em">Chiffre d'affaires</p>
                    <p style="font-size:1.4rem;font-weight:700;color:#4ade80" x-text="fmtXaf(report?.totals?.revenue)"></p>
                </div>
                <div class="card" style="padding:1rem">
                    <p style="font-size:0.72rem;color:var(--c-muted);margin-bottom:0.25rem;text-transform:uppercase;letter-spacing:0.05em">TVA collectée</p>
                    <p style="font-size:1.4rem;font-weight:700;color:#60a5fa" x-text="fmtXaf(report?.totals?.tva)"></p>
                </div>
                <div class="card" style="padding:1rem">
                    <p style="font-size:0.72rem;color:var(--c-muted);margin-bottom:0.25rem;text-transform:uppercase;letter-spacing:0.05em">Charges</p>
                    <p style="font-size:1.4rem;font-weight:700;color:#f87171" x-text="fmtXaf(report?.totals?.charges)"></p>
                </div>
                <div class="card" style="padding:1rem">
                    <p style="font-size:0.72rem;color:var(--c-muted);margin-bottom:0.25rem;text-transform:uppercase;letter-spacing:0.05em">Résultat net</p>
                    <p style="font-size:1.4rem;font-weight:700" :style="(report?.totals?.result||0) >= 0 ? 'color:#4ade80' : 'color:#f87171'" x-text="fmtXaf(report?.totals?.result)"></p>
                </div>
                <div class="card" style="padding:1rem">
                    <p style="font-size:0.72rem;color:var(--c-muted);margin-bottom:0.25rem;text-transform:uppercase;letter-spacing:0.05em">DGI non-sync</p>
                    <p style="font-size:1.4rem;font-weight:700" :style="(report?.totals?.dgi_pending||0) > 0 ? 'color:#fbbf24' : ''" x-text="report?.totals?.dgi_pending ?? 0"></p>
                </div>
            </div>

            {{-- Per-client breakdown table --}}
            <div class="card" style="overflow:hidden">
                <div style="padding:0.875rem 1.25rem;border-bottom:1px solid var(--c-border);display:flex;align-items:center;justify-content:space-between">
                    <h3 style="font-size:0.875rem;font-weight:600">Répartition par client</h3>
                    <span style="font-size:0.75rem;color:var(--c-muted)" x-text="'Période: ' + (report?.period?.from||'') + ' → ' + (report?.period?.to||'')"></span>
                </div>
                <div style="overflow-x:auto">
                    <table style="width:100%;border-collapse:collapse;font-size:0.825rem">
                        <thead>
                            <tr style="background:var(--c-bg)">
                                <th style="text-align:left;padding:0.6rem 1.25rem;color:var(--c-muted);font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid var(--c-border)">Client</th>
                                <th style="text-align:right;padding:0.6rem 1rem;color:var(--c-muted);font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid var(--c-border)">CA</th>
                                <th style="text-align:right;padding:0.6rem 1rem;color:var(--c-muted);font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid var(--c-border)">TVA</th>
                                <th style="text-align:right;padding:0.6rem 1rem;color:var(--c-muted);font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid var(--c-border)">Charges</th>
                                <th style="text-align:right;padding:0.6rem 1.25rem;color:var(--c-muted);font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid var(--c-border)">Résultat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="row in (report?.clients||[])" :key="row.id">
                                <tr style="border-bottom:1px solid var(--c-border)" onmouseover="this.style.background='#1C2A3A'" onmouseout="this.style.background=''">
                                    <td style="padding:0.7rem 1.25rem">
                                        <span style="font-weight:500" x-text="row.name"></span>
                                        <span style="color:var(--c-faint);font-size:0.72rem;display:block" x-text="row.niu ? 'NIU: '+row.niu : ''"></span>
                                    </td>
                                    <td style="padding:0.7rem 1rem;text-align:right;color:#4ade80;font-variant-numeric:tabular-nums" x-text="fmtXaf(row.revenue)"></td>
                                    <td style="padding:0.7rem 1rem;text-align:right;color:#60a5fa;font-variant-numeric:tabular-nums" x-text="fmtXaf(row.tva)"></td>
                                    <td style="padding:0.7rem 1rem;text-align:right;color:#f87171;font-variant-numeric:tabular-nums" x-text="fmtXaf(row.charges)"></td>
                                    <td style="padding:0.7rem 1.25rem;text-align:right;font-weight:600;font-variant-numeric:tabular-nums" :style="row.result >= 0 ? 'color:#4ade80' : 'color:#f87171'" x-text="fmtXaf(row.result)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tab: Équipe ─────────────────────────────────────────────── --}}
    <div x-show="tab === 'team'">
        <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
            <button class="btn-primary" @click="showAddStaff = true">+ Ajouter un membre</button>
        </div>

        <div x-show="loadingStaff" style="text-align:center;padding:3rem;color:var(--c-muted)">Chargement…</div>

        <div x-show="!loadingStaff" class="card" style="overflow:hidden">
            <table style="width:100%;border-collapse:collapse;font-size:0.85rem">
                <thead>
                    <tr style="background:var(--c-bg)">
                        <th style="text-align:left;padding:0.75rem 1.25rem;color:var(--c-muted);font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid var(--c-border)">Nom</th>
                        <th style="text-align:left;padding:0.75rem 1rem;color:var(--c-muted);font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid var(--c-border)">Email</th>
                        <th style="text-align:left;padding:0.75rem 1rem;color:var(--c-muted);font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid var(--c-border)">Rôle</th>
                        <th style="text-align:left;padding:0.75rem 1rem;color:var(--c-muted);font-weight:600;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid var(--c-border)">Statut</th>
                        <th style="border-bottom:1px solid var(--c-border)"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="member in staffList" :key="member.id">
                        <tr style="border-bottom:1px solid var(--c-border)" onmouseover="this.style.background='#1C2A3A'" onmouseout="this.style.background=''">
                            <td style="padding:0.75rem 1.25rem;font-weight:500" x-text="member.name"></td>
                            <td style="padding:0.75rem 1rem;color:var(--c-muted)" x-text="member.email"></td>
                            <td style="padding:0.75rem 1rem">
                                <span class="badge badge-unknown" x-text="member.firm_role"></span>
                            </td>
                            <td style="padding:0.75rem 1rem">
                                <span class="badge" :class="member.is_active ? 'badge-ok' : 'badge-unknown'" x-text="member.is_active ? 'Actif' : 'Inactif'"></span>
                            </td>
                            <td style="padding:0.75rem 1rem;text-align:right">
                                <button @click="confirmRemoveStaff(member)" style="background:transparent;border:none;color:var(--c-faint);cursor:pointer;font-size:0.75rem" onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='var(--c-faint)'">Retirer</button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="staffList.length === 0">
                        <td colspan="5" style="padding:2rem;text-align:center;color:var(--c-muted)">Aucun membre d'équipe.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Tab: Paramètres du Cabinet ──────────────────────────────── --}}
    <div x-show="tab === 'settings'">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;max-width:800px" class="settings-grid">
            {{-- Firm info card --}}
            <div class="card" style="padding:1.5rem;grid-column:1/-1">
                <h3 style="font-size:0.95rem;font-weight:600;margin-bottom:1.25rem">Informations du Cabinet</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="form-group" style="margin-bottom:0">
                        <label>Raison sociale</label>
                        <input x-model="settingsForm.name" />
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>N° OECAM</label>
                        <input x-model="settingsForm.oecam_number" placeholder="OEC-XXXX-XXXX" />
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Email</label>
                        <input x-model="settingsForm.email" type="email" />
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Téléphone</label>
                        <input x-model="settingsForm.phone" placeholder="+237 6..." />
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Adresse</label>
                        <input x-model="settingsForm.address" />
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>Ville</label>
                        <input x-model="settingsForm.city" placeholder="Douala" />
                    </div>
                </div>
                <p x-show="settingsError" x-text="settingsError" style="color:#f87171;font-size:0.8rem;margin-top:0.75rem"></p>
                <p x-show="settingsSuccess" x-text="settingsSuccess" style="color:#4ade80;font-size:0.8rem;margin-top:0.75rem"></p>
                <div style="margin-top:1.25rem;display:flex;justify-content:flex-end">
                    <button class="btn-primary" @click="saveSettings()" :disabled="savingSettings">
                        <span x-show="!savingSettings">Enregistrer</span>
                        <span x-show="savingSettings">Sauvegarde…</span>
                    </button>
                </div>
            </div>

            {{-- Logo upload card --}}
            <div class="card" style="padding:1.5rem">
                <h3 style="font-size:0.95rem;font-weight:600;margin-bottom:1rem">Logo du Cabinet</h3>
                <div x-show="firm?.logo_url" style="margin-bottom:1rem">
                    <img :src="firm?.logo_url" style="max-height:80px;border-radius:0.5rem;border:1px solid var(--c-border)" />
                </div>
                <div x-show="!firm?.logo_url" style="width:80px;height:80px;border:2px dashed var(--c-border);border-radius:0.5rem;display:flex;align-items:center;justify-content:center;color:var(--c-faint);font-size:1.5rem;margin-bottom:1rem">🏢</div>
                <input type="file" accept="image/jpeg,image/png,image/webp" x-ref="logoInput" style="display:none" @change="uploadLogo($event)" />
                <button class="btn-ghost" style="font-size:0.8rem" @click="$refs.logoInput.click()" :disabled="uploadingLogo">
                    <span x-show="!uploadingLogo">Changer le logo</span>
                    <span x-show="uploadingLogo">Envoi…</span>
                </button>
                <p style="font-size:0.72rem;color:var(--c-faint);margin-top:0.5rem">JPG, PNG ou WebP · max 2 Mo</p>
            </div>

            {{-- Capacity card --}}
            <div class="card" style="padding:1.5rem">
                <h3 style="font-size:0.95rem;font-weight:600;margin-bottom:1rem">Capacité du Portefeuille</h3>
                <div style="display:flex;align-items:baseline;gap:0.5rem;margin-bottom:0.75rem">
                    <span style="font-size:2rem;font-weight:700" x-text="firm?.client_count ?? 0"></span>
                    <span style="color:var(--c-muted);font-size:0.875rem" x-text="'/ ' + (firm?.max_clients ?? 50) + ' clients'"></span>
                </div>
                <div style="background:var(--c-bg);border-radius:9999px;height:6px;overflow:hidden">
                    <div style="background:var(--c-accent);height:100%;border-radius:9999px;transition:width 0.3s"
                         :style="'width:' + Math.min(100, ((firm?.client_count||0) / (firm?.max_clients||50)) * 100) + '%'"></div>
                </div>
                <p style="font-size:0.75rem;color:var(--c-faint);margin-top:0.5rem">Contactez le support pour augmenter la limite.</p>
            </div>
        </div>
    </div>
</div>

{{-- ── Add Client Modal ─────────────────────────────────────────────── --}}
<div x-show="showAddClient" class="modal-backdrop" @click.self="showAddClient = false">
    <div class="card" style="width:100%;max-width:480px;padding:1.5rem" @click.stop>
        <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:1.25rem">Ajouter un client au portefeuille</h2>
        <div class="form-group">
            <label>ID de la société *</label>
            <input x-model="newClient.company_id" type="number" placeholder="ID numérique de la société Opes Books" />
        </div>
        <div class="form-group">
            <label>Type d'engagement</label>
            <select x-model="newClient.engagement_type">
                <option value="FULL_OUTSOURCE">Externalisation totale</option>
                <option value="REVIEW_ONLY">Révision uniquement</option>
                <option value="TAX_ONLY">Fiscal uniquement</option>
                <option value="PAYROLL_ONLY">Paie uniquement</option>
            </select>
        </div>
        <div class="form-group">
            <label>Mode de facturation</label>
            <select x-model="newClient.billing_mode">
                <option value="FIRM_PAYS">Cabinet facture</option>
                <option value="CLIENT_PAYS">Client paie directement</option>
                <option value="HYBRID">Hybride</option>
            </select>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea x-model="newClient.notes" rows="2" placeholder="Remarques internes…"></textarea>
        </div>
        <p x-show="addClientError" x-text="addClientError" style="color:#f87171;font-size:0.8rem;margin-bottom:0.75rem"></p>
        <div style="display:flex;gap:0.75rem;justify-content:flex-end">
            <button class="btn-ghost" @click="showAddClient = false">Annuler</button>
            <button class="btn-primary" @click="submitAddClient()" :disabled="addingClient">
                <span x-show="!addingClient">Ajouter</span>
                <span x-show="addingClient">Ajout…</span>
            </button>
        </div>
    </div>
</div>

{{-- ── Edit Client Modal ────────────────────────────────────────────── --}}
<div x-show="showEditClient" class="modal-backdrop" @click.self="showEditClient = false">
    <div class="card" style="width:100%;max-width:480px;padding:1.5rem" @click.stop>
        <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:0.25rem" x-text="editingClient?.name"></h2>
        <p style="font-size:0.8rem;color:var(--c-muted);margin-bottom:1.25rem" x-text="'NIU: ' + (editingClient?.niu || '—')"></p>
        <div class="form-group">
            <label>Type d'engagement</label>
            <select x-model="editForm.engagement_type">
                <option value="FULL_OUTSOURCE">Externalisation totale</option>
                <option value="REVIEW_ONLY">Révision uniquement</option>
                <option value="TAX_ONLY">Fiscal uniquement</option>
                <option value="PAYROLL_ONLY">Paie uniquement</option>
            </select>
        </div>
        <div class="form-group">
            <label>Mode de facturation</label>
            <select x-model="editForm.billing_mode">
                <option value="FIRM_PAYS">Cabinet facture</option>
                <option value="CLIENT_PAYS">Client paie directement</option>
                <option value="HYBRID">Hybride</option>
            </select>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea x-model="editForm.notes" rows="2"></textarea>
        </div>
        <p x-show="editClientError" x-text="editClientError" style="color:#f87171;font-size:0.8rem;margin-bottom:0.75rem"></p>
        <div style="display:flex;gap:0.75rem;justify-content:space-between">
            <button class="btn-ghost" style="color:#f87171;border-color:rgba(239,68,68,0.3)" @click="removeClient(editingClient)">Retirer du portefeuille</button>
            <div style="display:flex;gap:0.75rem">
                <button class="btn-ghost" @click="showEditClient = false">Annuler</button>
                <button class="btn-primary" @click="submitEditClient()" :disabled="savingClient">
                    <span x-show="!savingClient">Enregistrer</span>
                    <span x-show="savingClient">Sauvegarde…</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Add Staff Modal ──────────────────────────────────────────────── --}}
<div x-show="showAddStaff" class="modal-backdrop" @click.self="showAddStaff = false">
    <div class="card" style="width:100%;max-width:420px;padding:1.5rem" @click.stop>
        <h2 style="font-size:1.05rem;font-weight:700;margin-bottom:1.25rem">Ajouter un membre d'équipe</h2>
        <div class="form-group">
            <label>Adresse email *</label>
            <input x-model="newStaff.email" type="email" placeholder="collaborateur@cabinet.cm" />
            <p style="font-size:0.72rem;color:var(--c-faint);margin-top:0.3rem">L'utilisateur doit déjà avoir un compte Opes Books.</p>
        </div>
        <div class="form-group">
            <label>Rôle dans le cabinet</label>
            <select x-model="newStaff.firm_role">
                <option value="PARTNER">Associé (PARTNER)</option>
                <option value="SENIOR">Senior</option>
                <option value="JUNIOR" selected>Junior</option>
                <option value="ASSISTANT">Assistant</option>
            </select>
        </div>
        <p x-show="addStaffError" x-text="addStaffError" style="color:#f87171;font-size:0.8rem;margin-bottom:0.75rem"></p>
        <div style="display:flex;gap:0.75rem;justify-content:flex-end">
            <button class="btn-ghost" @click="showAddStaff = false">Annuler</button>
            <button class="btn-primary" @click="submitAddStaff()" :disabled="addingStaff">
                <span x-show="!addingStaff">Ajouter</span>
                <span x-show="addingStaff">Ajout…</span>
            </button>
        </div>
    </div>
</div>

<script>
function firmApp() {
    return {
        // State
        loaded: false,
        firm: null,
        firmRole: null,
        stats: {},
        clients: [],
        taskGroups: [],
        tab: 'portfolio',
        loadingPortfolio: true,
        loadingTasks: false,
        tasksLoaded: false,
        clientSearch: '',
        clientFilter: '',

        // Setup
        setup: { name: '', oecam_number: '', email: '', phone: '', address: '', city: 'Douala' },
        creating: false,
        setupError: '',

        // Add client modal
        showAddClient: false,
        newClient: { company_id: '', engagement_type: 'FULL_OUTSOURCE', billing_mode: 'FIRM_PAYS', notes: '' },
        addingClient: false,
        addClientError: '',

        // Edit client modal
        showEditClient: false,
        editingClient: null,
        editForm: { engagement_type: '', billing_mode: '', notes: '' },
        savingClient: false,

        // Reports
        report: null,
        loadingReport: false,
        reportLoaded: false,
        reportFrom: new Date(new Date().getFullYear(), 0, 1).toISOString().slice(0,10),
        reportTo: new Date().toISOString().slice(0,10),

        // Staff
        staffList: [],
        loadingStaff: false,
        staffLoaded: false,
        showAddStaff: false,
        newStaff: { email: '', firm_role: 'JUNIOR' },
        addingStaff: false,
        addStaffError: '',

        // Settings
        settingsForm: { name: '', oecam_number: '', email: '', phone: '', address: '', city: '' },
        savingSettings: false,
        settingsError: '',
        settingsSuccess: '',
        uploadingLogo: false,
        editClientError: '',

        get filteredClients() {
            return this.clients.filter(c => {
                const matchSearch = !this.clientSearch ||
                    c.name.toLowerCase().includes(this.clientSearch.toLowerCase()) ||
                    (c.niu || '').includes(this.clientSearch);
                const matchFilter = !this.clientFilter || c.compliance?.overall === this.clientFilter;
                return matchSearch && matchFilter;
            });
        },

        token() { return localStorage.getItem('opes_token'); },

        async api(method, path, body) {
            const res = await fetch('/api/v1' + path, {
                method,
                headers: { 'Authorization': 'Bearer ' + this.token(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: body ? JSON.stringify(body) : undefined,
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Erreur');
            return data;
        },

        async init() {
            if (!this.token()) { window.location = '/login'; return; }
            try {
                const me = await this.api('GET', '/firm/me');
                this.firm = me.firm;
                this.firmRole = me.firm_role;
                if (this.firm) {
                    this.syncSettingsForm();
                    await this.loadPortfolio();
                }
            } catch (e) {
                console.error(e);
            }
            this.loaded = true;
        },

        syncSettingsForm() {
            if (!this.firm) return;
            this.settingsForm = {
                name: this.firm.name || '',
                oecam_number: this.firm.oecam_number || '',
                email: this.firm.email || '',
                phone: this.firm.phone || '',
                address: this.firm.address || '',
                city: this.firm.city || '',
            };
        },

        async loadPortfolio() {
            this.loadingPortfolio = true;
            try {
                const data = await this.api('GET', '/firm/portfolio');
                this.firm = data.firm;
                this.stats = data.stats;
                this.clients = data.clients;
                this.syncSettingsForm();
            } catch (e) { console.error(e); }
            this.loadingPortfolio = false;
        },

        async loadTasks() {
            if (this.tasksLoaded) return;
            this.loadingTasks = true;
            try {
                const data = await this.api('GET', '/firm/tasks');
                this.taskGroups = data.tasks;
                this.tasksLoaded = true;
            } catch (e) { console.error(e); }
            this.loadingTasks = false;
        },

        async createFirm() {
            this.setupError = '';
            if (!this.setup.name.trim()) { this.setupError = 'Le nom est obligatoire.'; return; }
            this.creating = true;
            try {
                const data = await this.api('POST', '/firm', this.setup);
                this.firm = data.firm;
                this.firmRole = 'PARTNER';
                await this.loadPortfolio();
            } catch (e) { this.setupError = e.message; }
            this.creating = false;
        },

        async openClient(client) {
            try {
                await this.api('POST', '/firm/clients/' + client.id + '/open');
                window.location = '/app';
            } catch (e) { alert(e.message); }
        },

        editClient(client) {
            this.editingClient = client;
            this.editForm = { engagement_type: client.engagement_type, billing_mode: client.billing_mode, notes: client.notes || '' };
            this.editClientError = '';
            this.showEditClient = true;
        },

        async submitEditClient() {
            this.editClientError = '';
            this.savingClient = true;
            try {
                const data = await this.api('PUT', '/firm/clients/' + this.editingClient.id, this.editForm);
                const idx = this.clients.findIndex(c => c.id === this.editingClient.id);
                if (idx !== -1) this.clients[idx] = data.client;
                this.showEditClient = false;
            } catch (e) { this.editClientError = e.message; }
            this.savingClient = false;
        },

        async removeClient(client) {
            if (!confirm('Retirer ' + client.name + ' du portefeuille ?')) return;
            try {
                await this.api('DELETE', '/firm/clients/' + client.id);
                this.clients = this.clients.filter(c => c.id !== client.id);
                this.stats.total_clients = Math.max(0, (this.stats.total_clients || 1) - 1);
                this.showEditClient = false;
            } catch (e) { alert(e.message); }
        },

        async submitAddClient() {
            this.addClientError = '';
            if (!this.newClient.company_id) { this.addClientError = 'Renseignez l\'ID de la société.'; return; }
            this.addingClient = true;
            try {
                const data = await this.api('POST', '/firm/clients', this.newClient);
                this.clients.unshift(data.client);
                this.stats.total_clients = (this.stats.total_clients || 0) + 1;
                this.showAddClient = false;
                this.newClient = { company_id: '', engagement_type: 'FULL_OUTSOURCE', billing_mode: 'FIRM_PAYS', notes: '' };
            } catch (e) { this.addClientError = e.message; }
            this.addingClient = false;
        },

        async loadReport(force = false) {
            if (this.reportLoaded && !force) return;
            this.loadingReport = true;
            try {
                const data = await this.api('GET', `/firm/report?from=${this.reportFrom}&to=${this.reportTo}`);
                this.report = data;
                this.reportLoaded = true;
            } catch (e) { console.error(e); }
            this.loadingReport = false;
        },

        async loadStaff() {
            if (this.staffLoaded) return;
            this.loadingStaff = true;
            try {
                const data = await this.api('GET', '/firm/staff');
                this.staffList = data.staff ?? [];
                this.staffLoaded = true;
            } catch (e) { console.error(e); }
            this.loadingStaff = false;
        },

        async submitAddStaff() {
            this.addStaffError = '';
            if (!this.newStaff.email) { this.addStaffError = 'Email obligatoire.'; return; }
            this.addingStaff = true;
            try {
                const data = await this.api('POST', '/firm/staff', this.newStaff);
                this.staffList.push(data.member);
                this.showAddStaff = false;
                this.newStaff = { email: '', firm_role: 'JUNIOR' };
            } catch (e) { this.addStaffError = e.message; }
            this.addingStaff = false;
        },

        async confirmRemoveStaff(member) {
            if (!confirm(`Retirer ${member.name} de l'équipe ?`)) return;
            try {
                await this.api('DELETE', '/firm/staff/' + member.id);
                this.staffList = this.staffList.filter(m => m.id !== member.id);
            } catch (e) { alert(e.message); }
        },

        async saveSettings() {
            this.settingsError = '';
            this.settingsSuccess = '';
            this.savingSettings = true;
            try {
                const data = await this.api('PUT', '/firm', this.settingsForm);
                this.firm = data.firm;
                this.settingsSuccess = 'Paramètres enregistrés avec succès.';
                setTimeout(() => this.settingsSuccess = '', 3000);
            } catch (e) { this.settingsError = e.message; }
            this.savingSettings = false;
        },

        async uploadLogo(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.uploadingLogo = true;
            const formData = new FormData();
            formData.append('logo', file);
            try {
                const res = await fetch('/api/v1/firm/logo', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + this.token(), 'Accept': 'application/json' },
                    body: formData,
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erreur');
                this.firm = { ...this.firm, logo_url: data.logo_url };
            } catch (e) { alert(e.message); }
            this.uploadingLogo = false;
        },

        fmtXaf(v) {
            if (v == null) return '—';
            return new Intl.NumberFormat('fr-CM', { style:'currency', currency:'XAF', maximumFractionDigits:0 }).format(v);
        },

        logout() {
            localStorage.removeItem('opes_token');
            window.location = '/login';
        },

        overallBadgeClass(overall) {
            if (overall === 'OK') return 'badge-ok';
            if (overall === 'WARNING') return 'badge-warning';
            if (overall === 'OVERDUE') return 'badge-overdue';
            return 'badge-unknown';
        },

        statusBadgeClass(status) {
            if (status === 'CURRENT' || status === 'SYNCED') return 'badge-ok';
            if (status === 'DUE') return 'badge-warning';
            if (status === 'OVERDUE') return 'badge-overdue';
            if (status === 'PENDING') return 'badge-pending';
            return 'badge-unknown';
        },

        taskStatusBadge(status) {
            if (status === 'DONE') return 'badge-ok';
            if (status === 'OVERDUE') return 'badge-overdue';
            return 'badge-pending';
        },

        taskTypeBorder(type) {
            const colors = { TVA: '#60a5fa', CNPS: '#a78bfa', DSF: '#34d399', IS: '#f97316' };
            const c = colors[type] || '#8B9EC0';
            return `color:${c};border-color:${c}33;background:${c}11`;
        },
    };
}
</script>
</body>
</html>
