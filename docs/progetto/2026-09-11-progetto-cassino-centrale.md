# Progetto B&B Cassino Centrale — Documento di sintesi

_Ultimo aggiornamento: 2026-09-11_

Questo file riassume le scelte fatte insieme. È il "faro" del progetto: se un domani
ci si perde, si riparte da qui.

## In una frase
Sito + piccola piattaforma per il **B&B Cassino Centrale** (Viale Dante 6, Cassino):
sito pubblico, prenotazioni, area admin multi-ruolo, chatbot AI (sito + WhatsApp),
integrazioni (Stripe, SMTP, WhatsApp multi-provider, notifiche push), PWA.

## Dati del B&B
- **Nome:** B&B Cassino Centrale — Nel Cuore della Città
- **Slogan:** "Soggiorna a Cassino Centrale e hai Tutto a Portata di Mano!"
- **Indirizzo:** Viale Dante, 6 – Cassino (FR)
- **Telefono:** 0776 1400205
- **Gestione:** a conduzione femminile
- **Camere:** 4 matrimoniali (102, 103, 104, 205), tutte con bagno privato,
  aria condizionata, TV, Wi-Fi fibra, colazione inclusa. Una ha una piccola base cucina.

## Stack tecnico (deciso)
- **Framework:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade + Tailwind CSS + Vite
- **Database:** MariaDB/MySQL
- **Server:** VPS Hetzner + HestiaCP (Apache) — messa online in un secondo momento
- **Repo:** GitHub `maurosoft/cassinocentrale.it`
- **PWA:** manifest + service worker + push VAPID
- **Lingua:** italiano, con struttura già pronta per l'inglese (cartelle `lang/it`, `lang/en`)

## Stile / brand (deciso)
Atmosfera calda e accogliente, "B&B di charme".
- Terracotta `#B85C38` (primario), scuro `#8F4429`
- Crema `#FAF4EA` (sfondo)
- Salvia `#7E8D6B` (secondario)
- Testo `#3A2E26`
Font: sans pulito per il testo, un serif elegante per i titoli.

## Metodo di lavoro
- Si costruisce **una fase per volta**; alla fine di ognuna l'utente la prova.
- Il codice si scrive in locale → si carica su GitHub → si "accende" su Hetzner con comandi guidati.
- Linguaggio semplice, niente gergo inutile, conferme prima delle scelte importanti.

## Roadmap a fasi
1. **Fase 1 (in corso):** fondamenta (DB, modelli, impostazioni) + sito pubblico
   (home, camere + dettaglio, Scopri Cassino, contatti) + base PWA
   + sezione offerte in home + pulsante WhatsApp fisso.
2. Area admin + login ruoli (Superadmin / Reception / Editor).
3. Motore prenotazioni + calendario disponibilità + sconti soggiorni lunghi.
4. Email (SMTP) + WhatsApp multi-provider (SpotWab, UoZap, Hooki) con fallback + notifiche.
5. Stripe (pagamenti online).
6. Chatbot AI multi-provider (sito + WhatsApp) con fallback.
7. Push notifications (VAPID) + rifiniture PWA.

## Fase 1 — dettaglio
### Pagine pubbliche
- **Home:** hero con slogan e CTA "Prenota"; "Perché sceglierci"; anteprima camere;
  sezione **offerte / sconto soggiorni lunghi**; anteprima "Scopri Cassino"; recensioni (se attive).
- **Camere:** elenco (4 card) + pagina dettaglio (galleria, descrizione, servizi, regole, CTA prenota).
- **Scopri Cassino:** luoghi turistici + attività convenzionate, con distanze e categorie.
- **Contatti:** indirizzo, telefono, mappa, orari.
- **Pulsante WhatsApp** fisso in basso a destra su tutte le pagine.

### Base dati (tabelle) create in Fase 1
`users` (con ruolo), `rooms`, `services`, `room_service` (collegamento camere↔servizi),
`bookings`, `booking_rooms`, `places`, `site_settings`, `reviews`.
Le tabelle delle integrazioni (provider WhatsApp/AI, SMTP, Stripe, VAPID, log) si creeranno
nelle rispettive fasi.

### Dati di esempio (seed)
4 camere realistiche; luoghi (Abbazia di Montecassino, Anfiteatro/Teatro Romano, luoghi della
Seconda Guerra Mondiale…); attività convenzionate; qualche prenotazione finta; utenti admin di test.

### PWA base
`manifest.json` + service worker con caching di base e pagina "sei offline".

## Scelte con valori di default (modificabili da admin più avanti)
- Sconto soggiorni lunghi: **impostabile** (default proposto 10% per soggiorni > 1 notte) — da confermare.
- Tono del chatbot: da decidere in Fase 6.
- Testi email/WhatsApp: da definire in Fase 4.

## Domande ancora aperte (da chiedere all'utente al momento giusto)
- Prezzi base reali delle 4 camere (ora uso valori plausibili di esempio).
- Percentuale sconto soggiorni lunghi definitiva.
- Numero WhatsApp per il pulsante "Chatta con noi".
- Testi definitivi (descrizioni camere, intro Scopri Cassino).
