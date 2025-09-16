<?php
class ControllerExtensionModuleGoogleReviews extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/google_reviews');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        $this->load->model('extension/module/google_reviews');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_google_reviews', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_extension'), 'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        $data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/module/google_reviews', 'user_token=' . $this->session->data['user_token'], true));

        $data['action'] = $this->url->link('extension/module/google_reviews', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['fetch_action_non_ajax'] = $this->url->link('extension/module/google_reviews/fetch', 'user_token=' . $this->session->data['user_token'], true);
        $data['user_token'] = $this->session->data['user_token'];

        $fields = ['name', 'title', 'summary', 'write_review_url', 'scraper_api_url', 'min_rating', 'max_reviews_to_fetch', 'reviews_to_display', 'sort_order', 'overall_rating', 'total_reviews', 'status'];
        foreach ($fields as $field) {
            $config_key = 'module_google_reviews_' . $field;
            if (isset($this->request->post[$config_key])) {
                $data[$field] = $this->request->post[$config_key];
            } else {
                $data[$field] = $this->config->get($config_key);
            }
        }

        // --- ÉRTÉKELÉSEK LISTÁJÁNAK KEZELÉSE ---
		if (isset($this->request->get['sort'])) { $sort = $this->request->get['sort']; } else { $sort = 'review_id'; }
		if (isset($this->request->get['order'])) { $order = $this->request->get['order']; } else { $order = 'DESC'; }
		if (isset($this->request->get['page'])) { $page = (int)$this->request->get['page']; } else { $page = 1; }

        $url = '';
		if (isset($this->request->get['sort'])) { $url .= '&sort=' . $this->request->get['sort']; }
		if (isset($this->request->get['order'])) { $url .= '&order=' . $this->request->get['order']; }
		if (isset($this->request->get['page'])) { $url .= '&page=' . $this->request->get['page']; }

        $data['reviews'] = array();
		$filter_data = array(
			'sort'  => $sort, 'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

        $total_reviews_in_db = $this->model_extension_module_google_reviews->getTotalReviews();
		$results = $this->model_extension_module_google_reviews->getReviews($filter_data);

        foreach ($results as $result) {
			$data['reviews'][] = array(
				'review_id'        => $result['review_id'], 
                'author_name'      => $result['author_name'],
                'author_photo_url' => $result['author_photo_url'],
                'comment_text'     => $result['comment_text'],
				'rating'           => $result['rating'], 
                'is_visible'       => $result['is_visible'],
				'review_date'      => $result['review_date_text']
			);
		}

        $sort_url = '';
		if ($order == 'ASC') { $sort_url .= '&order=DESC'; } else { $sort_url .= '&order=ASC'; }
        if (isset($this->request->get['page'])) { $sort_url .= '&page=' . $this->request->get['page']; }

        $data['sort_author'] = $this->url->link('extension/module/google_reviews', 'user_token=' . $this->session->data['user_token'] . '&sort=author_name' . $sort_url, true);
		$data['sort_rating'] = $this->url->link('extension/module/google_reviews', 'user_token=' . $this->session->data['user_token'] . '&sort=rating' . $sort_url, true);
		$data['sort_status'] = $this->url->link('extension/module/google_reviews', 'user_token=' . $this->session->data['user_token'] . '&sort=is_visible' . $sort_url, true);
		$data['sort_review_date'] = $this->url->link('extension/module/google_reviews', 'user_token=' . $this->session->data['user_token'] . '&sort=review_id' . $sort_url, true);

        $pagination_url = '';
        if (isset($this->request->get['sort'])) { $pagination_url .= '&sort=' . $this->request->get['sort']; }
		if (isset($this->request->get['order'])) { $pagination_url .= '&order=' . $this->request->get['order']; }

        $pagination = new Pagination();
		$pagination->total = $total_reviews_in_db;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/google_reviews', 'user_token=' . $this->session->data['user_token'] . $pagination_url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($total_reviews_in_db) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_reviews_in_db - $this->config->get('config_limit_admin'))) ? $total_reviews_in_db : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_reviews_in_db, ceil($total_reviews_in_db / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;
        // --- ÉRTÉKELÉSEK LISTÁJÁNAK KEZELÉSE VÉGE ---

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/google_reviews', $data));
    }
    
    public function install() {
        $this->load->model('extension/module/google_reviews');
        $this->model_extension_module_google_reviews->install();
    }

    public function uninstall() {
        $this->load->model('extension/module/google_reviews');
        $this->model_extension_module_google_reviews->uninstall();
    }

    public function fetch() {
        set_time_limit(300);
        
        echo "<html><head><title>Feldolgozás...</title><meta charset='UTF-8'></head><body>";
        echo "A letöltés elindult. Kérlek, várj...<br><hr>";
        
        $this->load->language('extension/module/google_reviews');

        if (!$this->user->hasPermission('modify', 'extension/module/google_reviews')) {
            die('HIBA TÖRTÉNT!<br>Nincs jogosultsága a művelet végrehajtásához.');
        }

        try {
            $scraper_url = $this->config->get('module_google_reviews_scraper_api_url');
            $min_rating = (int)$this->config->get('module_google_reviews_min_rating');
            $max_reviews_to_fetch = (int)$this->config->get('module_google_reviews_max_reviews_to_fetch');
            
            if (!$scraper_url) {
                throw new Exception($this->language->get('error_scraper_url_missing'));
            }
            
            $decoded_url = html_entity_decode($scraper_url, ENT_QUOTES, 'UTF-8');

            parse_str(parse_url($decoded_url, PHP_URL_QUERY), $query_params);
            $api_key = isset($query_params['api_key']) ? $query_params['api_key'] : null;

            if (!$api_key) {
                 throw new Exception($this->language->get('error_scraper_url_missing_apikey'));
            }

            $this->load->model('setting/setting');
            $this->load->model('extension/module/google_reviews');

            $existing_ids = array_flip($this->model_extension_module_google_reviews->getExistingReviewIds());
            $current_url = $decoded_url;
            $reviews_saved = 0;

            while ($current_url && $reviews_saved < $max_reviews_to_fetch) {
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $current_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 180);
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_code != 200) {
                    throw new Exception(sprintf($this->language->get('error_api_connect'), $http_code));
                }

                $result = json_decode($response, true);
                
                if (!$result) {
                    throw new Exception($this->language->get('error_scraper_invalid_response'));
                }

                if (isset($result['place_info']['rating'])) {
                    $setting_data = $this->model_setting_setting->getSetting('module_google_reviews');
                    $setting_data['module_google_reviews_overall_rating'] = $result['place_info']['rating'];
                    $setting_data['module_google_reviews_total_reviews'] = $result['place_info']['reviews'];
                    $this->model_setting_setting->editSetting('module_google_reviews', $setting_data);
                }

                if (isset($result['reviews']) && is_array($result['reviews'])) {
                    foreach ($result['reviews'] as $review) {
                        if ($reviews_saved >= $max_reviews_to_fetch) break; 
                        
                        if (isset($existing_ids[$review['review_id']])) continue;
                        
                        if (!empty($review['snippet']) && (int)$review['rating'] >= $min_rating && isset($review['date'])) {
                            $review_data = [
                                'google_review_id'   => $review['review_id'],
                                'author_name'        => $review['user']['name'],
                                'author_photo_url'   => $review['user']['thumbnail'],
                                'rating'             => $review['rating'],
                                'comment_text'       => $review['snippet'],
                                'review_date_text'   => $review['date']
                            ];
                            $this->model_extension_module_google_reviews->addReview($review_data);
                            $reviews_saved++;
                        }
                    }
                }
                
                if ($reviews_saved < $max_reviews_to_fetch && isset($result['serpapi_pagination']['next'])) {
                    $current_url = $result['serpapi_pagination']['next'];
                    if (strpos($current_url, 'api_key=') === false) {
                        $current_url .= '&api_key=' . $api_key;
                    }
                } else {
                    $current_url = null;
                }
            }

            echo '<br><br><strong>SIKERES LETÖLTÉS!</strong><br>';
            echo sprintf($this->language->get('text_fetch_success'), $reviews_saved);

        } catch (Exception $e) {
            echo '<br><br><strong>HIBA TÖRTÉNT!</strong><br>';
            echo 'Az alábbi hibaüzenet segít azonosítani a probléma forrását.<br>';
            echo '<strong>Hiba:</strong> ' . $e->getMessage();
        }
        
        echo "<br><br><a href='" . $this->url->link('extension/module/google_reviews', 'user_token=' . $this->session->data['user_token'], true) . "'>Vissza a beállításokhoz</a>";
        echo "</body></html>";
        exit();
    }

    public function toggleVisibility() {
        $json = array();
        $this->load->language('extension/module/google_reviews');
        if (!$this->user->hasPermission('modify', 'extension/module/google_reviews')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            if (isset($this->request->post['review_id'])) {
                $this->load->model('extension/module/google_reviews');
                $this->model_extension_module_google_reviews->toggleReviewVisibility($this->request->post['review_id']);
                $json['success'] = true;
            } else {
                $json['error'] = 'Hiányzó értékelés azonosító!';
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/google_reviews')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['module_google_reviews_name']) < 3) || (utf8_strlen($this->request->post['module_google_reviews_name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        return !$this->error;
    }
}

