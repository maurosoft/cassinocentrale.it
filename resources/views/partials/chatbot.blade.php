@php($zapOn = (bool) ($settings['chatbot.enabled'] ?? false))
@if ($zapOn)
    @php($zap = app(\App\Services\ChatbotService::class))
    @php($zapName = $zap->name())
    @php($zapGreeting = $zap->greeting())

    <style>
        .zap-launch{position:fixed;right:20px;bottom:20px;z-index:60;display:flex;align-items:center;gap:.5rem;
            background:#b85c38;color:#faf4ea;border:0;border-radius:9999px;padding:.75rem 1.1rem;cursor:pointer;
            box-shadow:0 10px 30px rgba(58,46,38,.35);font:600 15px/1 'Figtree',system-ui,sans-serif;transition:transform .15s,background .2s}
        .zap-launch:hover{background:#a04e2f;transform:translateY(-2px)}
        .zap-launch svg{width:22px;height:22px}
        .zap-panel{position:fixed;right:20px;bottom:20px;z-index:61;width:min(380px,calc(100vw - 32px));height:min(560px,calc(100vh - 40px));
            display:none;flex-direction:column;background:#faf4ea;border-radius:18px;overflow:hidden;
            box-shadow:0 24px 60px rgba(58,46,38,.4);border:1px solid #e7dcc8}
        .zap-panel.open{display:flex}
        .zap-head{display:flex;align-items:center;gap:.6rem;background:#b85c38;color:#faf4ea;padding:.85rem 1rem}
        .zap-head .zap-dot{width:9px;height:9px;border-radius:50%;background:#8fbf6b;box-shadow:0 0 0 3px rgba(143,191,107,.3)}
        .zap-head b{font:600 16px/1.2 'Cormorant Garamond',serif;font-size:18px}
        .zap-head small{display:block;font-size:11px;opacity:.85;font-weight:400}
        .zap-close{margin-left:auto;background:transparent;border:0;color:#faf4ea;font-size:22px;cursor:pointer;line-height:1;padding:.2rem}
        .zap-body{flex:1;overflow-y:auto;padding:1rem;display:flex;flex-direction:column;gap:.6rem;background:#faf4ea}
        .zap-msg{max-width:82%;padding:.6rem .8rem;border-radius:14px;font:400 14px/1.45 'Figtree',system-ui,sans-serif;white-space:pre-wrap;word-wrap:break-word}
        .zap-msg.bot{align-self:flex-start;background:#fff;color:#3a2e26;border:1px solid #eaddc7;border-bottom-left-radius:4px}
        .zap-msg.user{align-self:flex-end;background:#7e8d6b;color:#fff;border-bottom-right-radius:4px}
        .zap-typing{align-self:flex-start;color:#9a8c7d;font-size:13px;font-style:italic;padding:.2rem .4rem}
        .zap-foot{display:flex;gap:.5rem;padding:.7rem;border-top:1px solid #eaddc7;background:#fff}
        .zap-foot input{flex:1;border:1px solid #e0d3bd;border-radius:10px;padding:.6rem .7rem;font:400 14px 'Figtree',system-ui,sans-serif;color:#3a2e26;outline:none}
        .zap-foot input:focus{border-color:#b85c38}
        .zap-foot button{background:#b85c38;color:#fff;border:0;border-radius:10px;padding:0 .9rem;cursor:pointer;font-weight:600}
        .zap-foot button:disabled{opacity:.5;cursor:default}
        @media (prefers-reduced-motion:reduce){.zap-launch{transition:none}}
    </style>

    <button type="button" class="zap-launch" data-zap-open aria-label="Apri la chat con {{ $zapName }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        {{ $zapName }}
    </button>

    <section class="zap-panel" data-zap-panel aria-live="polite">
        <header class="zap-head">
            <span class="zap-dot"></span>
            <span><b>{{ $zapName }}</b><small>Assistente B&amp;B Cassino Centrale</small></span>
            <button type="button" class="zap-close" data-zap-close aria-label="Chiudi">&times;</button>
        </header>
        <div class="zap-body" data-zap-body></div>
        <form class="zap-foot" data-zap-form>
            <input type="text" data-zap-input autocomplete="off" placeholder="Scrivi un messaggio…" maxlength="1000">
            <button type="submit" aria-label="Invia">➤</button>
        </form>
    </section>

    <script>
        (function () {
            const url = @json(route('chatbot.message'));
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const greeting = @json($zapGreeting);
            const openBtn = document.querySelector('[data-zap-open]');
            const panel = document.querySelector('[data-zap-panel]');
            const closeBtn = document.querySelector('[data-zap-close]');
            const body = document.querySelector('[data-zap-body]');
            const form = document.querySelector('[data-zap-form]');
            const input = document.querySelector('[data-zap-input]');
            const sendBtn = form.querySelector('button');
            let history = [];
            let started = false;

            function bubble(text, who) {
                const el = document.createElement('div');
                el.className = 'zap-msg ' + who;
                el.textContent = text;
                body.appendChild(el);
                body.scrollTop = body.scrollHeight;
                return el;
            }

            function openPanel() {
                panel.classList.add('open');
                openBtn.style.display = 'none';
                if (!started) { bubble(greeting, 'bot'); started = true; }
                setTimeout(() => input.focus(), 50);
            }
            function closePanel() {
                panel.classList.remove('open');
                openBtn.style.display = '';
            }
            openBtn.addEventListener('click', openPanel);
            closeBtn.addEventListener('click', closePanel);

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const msg = input.value.trim();
                if (!msg) return;
                bubble(msg, 'user');
                history.push({ role: 'user', content: msg });
                input.value = '';
                sendBtn.disabled = true;

                const typing = document.createElement('div');
                typing.className = 'zap-typing';
                typing.textContent = '{{ $zapName }} sta scrivendo…';
                body.appendChild(typing);
                body.scrollTop = body.scrollHeight;

                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ message: msg, history: history.slice(0, -1) })
                })
                .then(r => r.json())
                .then(data => {
                    typing.remove();
                    const reply = data.reply || 'Mi dispiace, non sono riuscito a rispondere.';
                    bubble(reply, 'bot');
                    history.push({ role: 'assistant', content: reply });
                    if (history.length > 20) history = history.slice(-20);
                })
                .catch(() => {
                    typing.remove();
                    bubble('Ops, problema di connessione. Riprova tra poco.', 'bot');
                })
                .finally(() => { sendBtn.disabled = false; input.focus(); });
            });
        })();
    </script>
@endif
