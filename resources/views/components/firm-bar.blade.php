{{--
    Firm context bar — shown at the top of /app when a FIRM_ACCOUNTANT
    is viewing a client's books. Rendered by app.blade.php via x-firm-bar.
--}}
<div x-data="firmBar()" x-init="init()" x-show="visible" style="
    background: linear-gradient(90deg, #1C2A3A 0%, #162133 100%);
    border-bottom: 1px solid #334155;
    padding: 0.5rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.8rem;
    flex-wrap: wrap;
">
    <span style="color: #F59E0B; font-weight: 700; white-space: nowrap">🏢 Cabinet</span>
    <span style="color: #8B9EC0; white-space: nowrap">Vous consultez le dossier de</span>
    <span style="color: #F0F4FA; font-weight: 600; white-space: nowrap" x-text="companyName"></span>
    <span style="color: #4E647E">·</span>
    <span style="color: #8B9EC0" x-text="firmName"></span>
    <span style="flex: 1"></span>
    <a href="/firm" style="
        color: #F59E0B;
        text-decoration: none;
        border: 1px solid rgba(245,158,11,0.3);
        padding: 0.2rem 0.75rem;
        border-radius: 0.375rem;
        white-space: nowrap;
        transition: background 0.15s;
    " onmouseover="this.style.background='rgba(245,158,11,0.1)'" onmouseout="this.style.background='transparent'">
        ← Retour au Portefeuille
    </a>
</div>

<script>
function firmBar() {
    return {
        visible: false,
        firmName: '',
        companyName: '',
        token() { return localStorage.getItem('opes_token'); },
        async init() {
            try {
                const res = await fetch('/api/v1/firm/me', {
                    headers: { 'Authorization': 'Bearer ' + this.token(), 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();
                if (!data.is_firm_accountant || !data.firm) return;
                this.firmName = data.firm.name;
                // Get current company name from auth/me
                const meRes = await fetch('/api/v1/auth/me', {
                    headers: { 'Authorization': 'Bearer ' + this.token(), 'Accept': 'application/json' }
                });
                if (meRes.ok) {
                    const me = await meRes.json();
                    this.companyName = me.company?.name || '';
                }
                this.visible = true;
            } catch (e) { /* silently skip */ }
        }
    };
}
</script>
