<?php
// Heading
$_['heading_title']    = 'Google Értékelések Megjelenítő';

// Text
$_['text_extension']   = 'Bővítmények';
$_['text_success']     = 'Sikeresen módosította a Google Értékelések modult!';
$_['text_edit']        = 'Google Értékelések Modul Szerkesztése';
$_['text_fetch_success'] = 'Új értékelések sikeresen letöltve és elmentve: %s db.';
$_['text_confirm_toggle'] = 'Biztosan megváltoztatja a láthatóságot?';
$_['text_official_google_data'] = 'Hivatalos Google Adatok (utolsó letöltéskor frissítve)';
$_['text_sort_order_newest'] = 'Legfrissebb elöl';
$_['text_sort_order_random'] = 'Véletlenszerű';


// Entry
$_['entry_name']                   = 'Modul Neve';
$_['entry_title']                  = 'Fejléc Címe';
$_['entry_summary']                = 'Értékelés Összefoglaló';
$_['entry_write_review_url']       = '"Vélemény írása" Gomb URL';
$_['entry_status']                 = 'Állapot';
$_['entry_scraper_api_url']        = 'Scraping API URL';
$_['entry_min_rating']             = 'Minimális értékelés mentése';
$_['entry_max_reviews_to_fetch']   = 'Letöltendő új értékelések maximális száma';
$_['entry_reviews_to_display']     = 'Megjelenítendő értékelések száma';
$_['entry_overall_rating']         = 'Hivatalos átlagos értékelés';
$_['entry_total_reviews']          = 'Hivatalos értékelések száma';
$_['entry_sort_order']             = 'Megjelenítési sorrend';


// Help
$_['help_title'] = 'A modul fő címe, pl. "Vásárlóink Mondták".';
$_['help_summary'] = 'A manuálisan beírt összefoglaló szöveg, ami az első kártyán jelenik meg.';
$_['help_write_review_url'] = 'A Google oldalra mutató link, ahol új véleményt lehet írni.';
$_['help_scraper_api_url'] = 'A scraping szolgáltatótól kapott teljes URL, ami a vélemények adatait tartalmazza.';
$_['help_min_rating'] = 'Csak az ennél a csillagszámnál nagyobb vagy egyenlő értékelések kerülnek mentésre.';
$_['help_max_reviews_to_fetch'] = 'Egy letöltés alkalmával legfeljebb ennyi új értékelést fog a rendszer keresni és menteni.';
$_['help_reviews_to_display'] = 'Ennyi (engedélyezett) értékelés fog megjelenni a webshopban.';
$_['help_sort_order'] = 'Válaszd ki, hogy a webshopban a legfrissebb értékelések jelenjenek-e meg, vagy véletlenszerűen válogasson a rendszer az engedélyezettek közül.';


// Tabs
$_['tab_general'] = 'Általános és Megjelenítés';
$_['tab_api_settings'] = 'Scraping API Beállítások';
$_['tab_review_management'] = 'Értékelések Kezelése';

// Button
$_['button_fetch'] = 'Új értékelések letöltése';

// Column
$_['column_author'] = 'Szerző';
$_['column_rating'] = 'Értékelés';
$_['column_status'] = 'Állapot';
$_['column_date_added'] = 'Letöltés Dátuma';
$_['column_action'] = 'Művelet';

// Error
$_['error_permission'] = 'Figyelmeztetés: Nincs jogosultsága a Google Értékelések modul módosításához!';
$_['error_name']       = 'A Modul neve mezőnek 3 és 64 karakter között kell lennie!';
$_['error_scraper_url_missing'] = 'A Scraping API URL megadása kötelező!';
$_['error_scraper_url_missing_apikey'] = 'A Scraping API URL-ből hiányzik az API kulcs (api_key=...)!';
$_['error_api_connect'] = 'Hiba a kapcsolatban: Nem sikerült csatlakozni a szolgáltatóhoz. (HTTP kód: %s)';
$_['error_scraper_invalid_response'] = 'A szolgáltatótól kapott válasz érvénytelen vagy nem tartalmaz értékeléseket. Ellenőrizd az API URL helyességét.';

