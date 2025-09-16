<?php
class ModelExtensionModuleGoogleReviews extends Model {
    /**
     * Visszaadja a legfrissebb, láthatóvá tett értékeléseket a "Legfrissebb elöl" sorrendhez.
     * A rendezést a megbízható 'review_id'-ra cseréltük.
     * @param int $limit A megjelenítendő értékelések maximális száma.
     */
    public function getLatestVisibleReviews($limit = 50) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "google_reviews` WHERE `is_visible` = 1 ORDER BY `review_id` DESC LIMIT " . (int)$limit);
        return $query->rows;
    }
    
    /**
     * Visszaadja az ÖSSZES láthatóvá tett értékelést a "Véletlenszerű" sorrendhez.
     */
    public function getAllVisibleReviews() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "google_reviews` WHERE `is_visible` = 1");
        return $query->rows;
    }
}

