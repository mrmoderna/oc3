<?php
class ModelExtensionModuleGoogleReviews extends Model {
    public function install() {
        // A biztonság kedvéért a telepítő most már a 'review_date_text' oszlopot hozza létre
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "google_reviews` (
              `review_id` INT(11) NOT NULL AUTO_INCREMENT,
              `google_review_id` VARCHAR(255) NOT NULL,
              `author_name` VARCHAR(255) NOT NULL,
              `author_photo_url` TEXT NOT NULL,
              `rating` INT(1) NOT NULL,
              `comment_text` TEXT NOT NULL,
              `review_date_text` VARCHAR(255) NOT NULL,
              `is_visible` TINYINT(1) NOT NULL DEFAULT '1',
              `date_added` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`review_id`),
              UNIQUE KEY `google_review_id` (`google_review_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "google_reviews`");
    }

    public function addReview($review_data) {
        // A mentés is a 'review_date_text' oszlopot használja
        $this->db->query("
            INSERT INTO `" . DB_PREFIX . "google_reviews` SET 
            `google_review_id` = '" . $this->db->escape($review_data['google_review_id']) . "',
            `author_name` = '" . $this->db->escape($review_data['author_name']) . "',
            `author_photo_url` = '" . $this->db->escape($review_data['author_photo_url']) . "',
            `rating` = " . (int)$review_data['rating'] . ",
            `comment_text` = '" . $this->db->escape($review_data['comment_text']) . "',
            `review_date_text` = '" . $this->db->escape($review_data['review_date_text']) . "'
            ON DUPLICATE KEY UPDATE 
            author_name = VALUES(author_name),
            author_photo_url = VALUES(author_photo_url),
            rating = VALUES(rating),
            comment_text = VALUES(comment_text),
            review_date_text = VALUES(review_date_text)
        ");
    }

    public function getReviews($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "google_reviews`";

        $sort_data = array(
            'author_name',
            'rating',
            'is_visible',
            'review_id' // JAVÍTÁS: A rendezés most már a stabil 'review_id'-t használja
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $this->db->escape($data['sort']);
        } else {
            $sql .= " ORDER BY review_id"; // Alapértelmezett rendezés
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getTotalReviews() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "google_reviews`");
        return $query->row['total'];
    }

    public function toggleReviewVisibility($review_id) {
        $this->db->query("UPDATE `" . DB_PREFIX . "google_reviews` SET `is_visible` = 1 - `is_visible` WHERE `review_id` = " . (int)$review_id);
    }
    
    public function getExistingReviewIds() {
        $query = $this->db->query("SELECT `google_review_id` FROM `" . DB_PREFIX . "google_reviews`");
        return array_column($query->rows, 'google_review_id');
    }
}

