<?php
class ControllerExtensionModuleGoogleReviews extends Controller {
    public function index($setting) {
        if ($this->config->get('module_google_reviews_status')) {
            $this->document->addStyle('catalog/view/theme/journal3/stylesheet/google_reviews.css');
            $this->load->language('extension/module/google_reviews');
            
            // Beállítások betöltése
            $data['title'] = $this->config->get('module_google_reviews_title');
            $data['summary'] = $this->config->get('module_google_reviews_summary');
            $data['write_review_url'] = $this->config->get('module_google_reviews_write_review_url');
            $data['average_rating'] = $this->config->get('module_google_reviews_overall_rating');
            $data['total_reviews'] = $this->config->get('module_google_reviews_total_reviews');

            $limit = (int)$this->config->get('module_google_reviews_reviews_to_display');
            if (!$limit) {
                $limit = 3; 
            }
            
            $sort_order = $this->config->get('module_google_reviews_sort_order');

            $this->load->model('extension/module/google_reviews');
            
            $reviews = array();
            $all_visible_reviews = $this->model_extension_module_google_reviews->getAllVisibleReviews();
            
            // Véletlenszerű avatarok kiválasztása
            $data['summary_avatars'] = array();
            if ($all_visible_reviews) {
                $shuffled_for_avatars = $all_visible_reviews;
                shuffle($shuffled_for_avatars);
                $avatars_to_show = array_slice($shuffled_for_avatars, 0, 3);
                foreach ($avatars_to_show as $avatar_review) {
                    $data['summary_avatars'][] = $avatar_review['author_photo_url'];
                }
            }


            if ($sort_order == 'random') {
                // Véletlenszerű sorrend
                if($all_visible_reviews) {
                    // A shuffle már az avataroknál megtörtént, itt csak szeleteljük a listát
                    $reviews = array_slice($shuffled_for_avatars, 0, $limit);
                }
            } else {
                // Alapértelmezett: legfrissebb elöl
                $reviews = $this->model_extension_module_google_reviews->getLatestVisibleReviews($limit);
            }
            
            $data['google_review_items'] = array();
            if ($reviews) {
                foreach ($reviews as $review) {
                    $data['google_review_items'][] = array(
                        'author_name'      => $review['author_name'],
                        'author_photo_url' => $review['author_photo_url'],
                        'rating'           => $review['rating'],
                        'comment_text'     => $review['comment_text'],
                        'review_date'      => $review['review_date_text'] 
                    );
                }
            }
            
            return $this->load->view('extension/module/google_reviews', $data);
        }
    }
}

