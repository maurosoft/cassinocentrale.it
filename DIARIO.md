# 📔 Diario del progetto — B&B Cassino Centrale

> **A cosa serve questo file:** è il "punto di ripartenza".
> Se apro il progetto da un altro PC (o dopo tanto tempo), Claude legge PRIMA questo file
> e sa subito a che punto siamo, senza che io debba rispiegare tutto.
> **Ultimo aggiornamento: 17/09/2026.**

---

## 🏠 Cos'è il progetto

Sito + gestionale per il **B&B Cassino Centrale** (Viale Dante 6, Cassino FR).
Fatto con **Laravel 12 + Blade + Tailwind** (PWA installabile).

- **Sito live:** https://cassinocentrale.it
- **Codice su GitHub:** https://github.com/maurosoft/cassinocentrale.it
- **Server:** Hetzner + HestiaCP + MariaDB
- 4 camere matrimoniali (102, 103, 104, 205), tutte con bagno privato, AC, TV, Wi-Fi fibra, colazione.
- Titolare: Jessica Vicalvi — P.IVA 03351130608.

---

## 💻 Come lavorare da un ALTRO PC

1. Installa **Claude Code** e fai login con lo stesso account.
2. Scarica il progetto:
   ```
   git clone https://github.com/maurosoft/cassinocentrale.it.git
   ```
3. Apri quella cartella con Claude Code e digli: **"Leggi il DIARIO.md e ripartiamo"**.

> Il **sito** è sul server (sempre online). Il **codice** è su GitHub (scaricabile ovunque).
> La **chat** con Claude riparte da zero su ogni PC: per questo esiste questo diario.

---

## 🚀 Come aggiornare il server (routine)

Da collegato al server come **root**:

```
cd /home/promoweb/web/cassinocentrale.it/private/app
git pull origin main
php artisan migrate --force
php artisan optimize:clear && php artisan optimize
chown -R promoweb:promoweb /home/promoweb/web/cassinocentrale.it/private/app
```

**Trucchi / trappole già scoperte:**
- Login "Too Many Attempts" → `php artisan optimize:clear` (azzera il blocco).
- git "dubious ownership" (dopo il chown) → una volta sola:
  `git config --global --add safe.directory /home/promoweb/web/cassinocentrale.it/private/app`
- Gli asset (CSS/JS) si costruiscono **in locale** con `npm run build` e si committano
  (`public/build`): il server NON ha bisogno di Node.
- `public_html` sul server è un **symlink** a `.../private/app/public`.
- PHP: CLI 8.3 (artisan/composer), web 8.4. Nomi DB HestiaCP usano `_` non `-`.

---

## ✅ Cosa è FATTO

- **Fase 1** — Sito pubblico (home, camere, Scopri Cassino → Turismo + Attività, contatti, pagine legali).
- **Fase 2** — Area admin con ruoli (Superadmin / Reception / Editor), dashboard, CRUD completo.
- **Fase 3** — Prenotazioni + calendario + prezzi + chiusure + registro clienti (CRM base con data/luogo nascita).
  - Prezzi: 65€/notte (1 pers.), −10% in due, +10% soggiorni lunghi (da 3 notti, non cumulabili).
  - Gruppi 3-4 ospiti → 2 camere; sconto 2ª camera modificabile da admin (default 15%).
- **Fase 4** — Email (SMTP da admin) ✅ + WhatsApp multi-provider (UoZap + Hooki/SpotWab) con fallback ✅. Testati OK.
- **Fase 5** — Stripe (pagamenti online) via Checkout ospitato. Chiavi inserite dall'admin.
- **Extra** — Modalità manutenzione, logo da admin, favicon da admin, galleria camere 16:9 con lightbox,
  **immagini home (hero + parallax) modificabili da admin** (17/09/2026).
- **Fase 6a — Chatbot "Zap" SUL SITO** ✅ (17/09/2026): widget bollicina in basso a destra.
  Admin → "Chatbot Zap" (solo superadmin): scegli provider (Anthropic/OpenAI/Gemini/DeepSeek/OpenRouter),
  incolli la chiave API, premi **"Rileva modelli"**, scegli il modello, spunti "Attiva", salvi.
  Zap conosce in automatico camere, prezzi e luoghi/negozi (con indirizzo). Chiavi cifrate nel DB.
  Log conversazioni + prova rapida nel pannello. Provider scelto per i primi test: **DeepSeek**.
- **Fase 6a+ — Zap prenota in chat** ✅ (18/09/2026): con l'interruttore "Consenti a Zap di prenotare",
  Zap legge il **calendario vero** (disponibilità + prezzi reali, suggerisce date alternative) e crea
  prenotazioni **"da approvare"** raccogliendo nome/email/telefono + **consenso privacy** nel dialogo.

## 🔜 Cosa MANCA (prossimi passi)

- **Fase 6b — Zap anche su WhatsApp** (da fare): ricezione messaggi in arrivo dal provider WhatsApp.
- **Fase 7 — Notifiche push (PWA / VAPID).**
- **Backlog** — Recensioni Google Business (import + risposte AI in bozza + pubblica).
- Verificare pagamento Stripe di prova (carta 4242 4242 4242 4242).
- Attivare gli interruttori Notifiche (email/WhatsApp) + numero staff per far partire gli avvisi reali.

---

## 🗣️ Come Claude deve parlarmi (importante)

Non sono un programmatore, sono uno "smanettone". Quindi:
- **Italiano semplice**, spiega i termini tecnici.
- Comandi **pronti da copiare** (terminale, file, valori esatti).
- **Chiedi conferma** prima di scelte importanti — dialogo, non solo codice.
- Proponi **migliorie** di marketing/funzionali quando ha senso.
