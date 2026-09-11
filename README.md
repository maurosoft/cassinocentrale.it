# B&B Cassino Centrale

Sito web e piattaforma del **B&B Cassino Centrale** (Viale Dante 6, Cassino – FR).
Realizzato con **Laravel 12**, **Blade** e **Tailwind CSS**. È una **PWA** (installabile su telefono).

> Progetto in costruzione a fasi. Vedi il documento di sintesi in
> [`docs/progetto/2026-09-11-progetto-cassino-centrale.md`](docs/progetto/2026-09-11-progetto-cassino-centrale.md).

## Cosa c'è oggi (Fase 1)
- Sito pubblico: Home, Camere (elenco + dettaglio), Scopri Cassino, Contatti.
- Sezione offerte in home + pulsante WhatsApp fisso.
- Base dati e dati di esempio (4 camere, luoghi, recensioni, utenti admin di test).
- PWA di base (manifest + service worker + pagina offline).

## Prossime fasi
2. Area admin multi-ruolo · 3. Prenotazioni + calendario · 4. Email + WhatsApp
· 5. Stripe · 6. Chatbot AI · 7. Push notifications.

---

## Installazione in locale (per sviluppatori)
Servono: **PHP 8.2+**, **Composer**, **Node 18+**, un database **MySQL/MariaDB**.

```bash
git clone https://github.com/maurosoft/cassinocentrale.it.git
cd cassinocentrale.it

composer install
npm install

cp .env.example .env
php artisan key:generate

# Configura i dati del database nel file .env, poi:
php artisan migrate --seed
php artisan storage:link

# Avvia in sviluppo (due terminali):
npm run dev
php artisan serve
```

Apri poi `http://localhost:8000`.

### Utenti admin di test (creati dal seed)
| Ruolo | Email | Password |
|------|-------|----------|
| Superadmin | admin@cassinocentrale.it | password |
| Reception | reception@cassinocentrale.it | password |
| Editor | editor@cassinocentrale.it | password |

> ⚠️ Cambia queste password prima di andare online. L'area di login arriva in Fase 2.

---

## Messa online su Hetzner + HestiaCP
1. In HestiaCP crea il dominio `cassinocentrale.it` e un database MariaDB.
2. Imposta la **document root** del dominio sulla cartella `public/` del progetto.
3. Clona il repository nella cartella del sito e crea il file `.env` (parti da `.env.example`).
4. Da terminale, dentro la cartella del progetto, lancia una volta:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --seed --force
   php artisan storage:link
   npm ci && npm run build
   ```
5. Per gli aggiornamenti successivi basta:
   ```bash
   git pull origin main
   bash deploy/deploy-prod.sh
   ```

Assicurati che PHP abbia le estensioni richieste da Laravel (pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo) e che la cartella `storage/` sia scrivibile dal web server.

---

## Struttura del progetto (in breve)
- `app/Models` – camere, prenotazioni, luoghi, impostazioni, recensioni, utenti.
- `app/Http/Controllers` – logica delle pagine pubbliche.
- `app/Support/Settings.php` – lettura impostazioni sito (con cache).
- `database/migrations` – creazione tabelle.
- `database/seeders` – dati di esempio.
- `resources/views` – pagine (Blade).
- `public/manifest.json`, `public/sw.js` – PWA.
- `deploy/deploy-prod.sh` – script di messa online.
