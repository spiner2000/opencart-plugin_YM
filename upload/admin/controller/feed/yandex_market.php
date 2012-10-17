<?php
class ControllerFeedYandexMarket extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('feed/yandex_market');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            if (isset($this->request->post['yandex_market_categories'])) {
                $this->request->post['yandex_market_categories'] = implode(',', $this->request->post['yandex_market_categories']);
            }

            $this->model_setting_setting->editSetting('yandex_market', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
        }
            
        $this->data['entry_data_feed']      = $this->language->get('entry_data_feed');
        $this->data['entry_stock_status']   = $this->language->get('entry_stock_status');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_select_all'] = $this->language->get('text_select_all');
        $this->data['text_unselect_all'] = $this->language->get('text_unselect_all');

        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_data_feed'] = $this->language->get('entry_data_feed');
        $this->data['entry_shopname'] = $this->language->get('entry_shopname');
        $this->data['entry_company'] = $this->language->get('entry_company');
        $this->data['entry_category'] = $this->language->get('entry_category');
        $this->data['entry_currency'] = $this->language->get('entry_currency');
        $this->data['entry_in_stock'] = $this->language->get('entry_in_stock');
        $this->data['entry_out_of_stock'] = $this->language->get('entry_out_of_stock');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        $this->data['tab_general'] = $this->language->get('tab_general');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_feed'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('feed/yml', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('feed/yandex_market', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['yandex_market_status'])) {
            $this->data['yandex_market_status'] = $this->request->post['yandex_market_status'];
        } else {
            $this->data['yandex_market_status'] = $this->config->get('yandex_market_status');
        }

        $this->data['data_feed'] = HTTP_CATALOG . 'index.php?route=feed/yandex_market';

        if (isset($this->request->post['yandex_market_shopname'])) {
            $this->data['yandex_market_shopname'] = $this->request->post['yandex_market_shopname'];
        } else {
            $this->data['yandex_market_shopname'] = $this->config->get('yandex_market_shopname');
        }

        if (isset($this->request->post['yandex_market_company'])) {
            $this->data['yandex_market_company'] = $this->request->post['yandex_market_company'];
        } else {
            $this->data['yandex_market_company'] = $this->config->get('yandex_market_company');
        }

        if (isset($this->request->post['yandex_market_currency'])) {
            $this->data['yandex_market_currency'] = $this->request->post['yandex_market_currency'];
        } else {
            $this->data['yandex_market_currency'] = $this->config->get('yandex_market_currency');
        }

        if (isset($this->request->post['yandex_market_in_stock'])) {
            $this->data['yandex_market_in_stock'] = $this->request->post['yandex_market_in_stock'];
        } elseif ($this->config->get('yandex_market_in_stock')) {
            $this->data['yandex_market_in_stock'] = $this->config->get('yandex_market_in_stock');
        } else {
            $this->data['yandex_market_in_stock'] = 7;
        }

        if (isset($this->request->post['yandex_market_out_of_stock'])) {
            $this->data['yandex_market_out_of_stock'] = $this->request->post['yandex_market_out_of_stock'];
        } elseif ($this->config->get('yandex_market_in_stock')) {
            $this->data['yandex_market_out_of_stock'] = $this->config->get('yandex_market_out_of_stock');
        } else {
            $this->data['yandex_market_out_of_stock'] = 5;
        }

        $this->load->model('localisation/stock_status');

        $this->data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

        $this->load->model('catalog/category');

        $this->data['categories'] = $this->model_catalog_category->getCategories(0);

        if (isset($this->request->post['yandex_market_categories'])) {
            $this->data['yandex_market_categories'] = $this->request->post['yandex_market_categories'];
        } elseif ($this->config->get('yandex_market_categories') != '') {
            $this->data['yandex_market_categories'] = explode(',', $this->config->get('yandex_market_categories'));
        } else {
            $this->data['yandex_market_categories'] = array();
        }

        $this->load->model('localisation/currency');
        $currencies = $this->model_localisation_currency->getCurrencies();
        $allowed_currencies = array_flip(array('RUR', 'RUB', 'BYR', 'KZT', 'UAH'));
        $this->data['currencies'] = array_intersect_key($currencies, $allowed_currencies);

        $this->template = 'feed/yandex_market.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(), $this->config->get('config_compression'));
    }

    /**
     * Отдает список производителей в формате JSON
     * Вызывается по AJAX
     *
     * @return object Response
     */
    public function brands()
    {
        $json = array(
            'error' => '',
            'results' => '',
            'translations' => array()
        );

        // Подключение файла локализации
        $this->load->language('feed/yandex_market');

        // Проверка прав доступа
        if (!$this->user->hasPermission('access', 'feed/yandex_market')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            // Получение локализированных значений для ключевых элементов страницы
            $json['translations']['entry_option_in_stock'] = $this->language->get('entry_option_in_stock');
            $json['translations']['entry_all_brands'] = $this->language->get('entry_all_brands');

            // Загрузка модели работы с производителями
            $this->load->model('catalog/manufacturer');

            $json['results'] = array();

            $config_status_brand = unserialize($this->config->get('yandex_market_stock_brands'));
            $status_brands = array();
            $products_available = false;
            if ($stock_status_id = (int)$this->request->post['stock_status']) {
                if (isset($config_status_brand[$stock_status_id])) {
                    $status_brands = isset($config_status_brand[$stock_status_id]['manufacturers'])
                        ? $config_status_brand[$stock_status_id]['manufacturers']
                        : array();
                    // Атрибут доступности товара для Яндекс Маркет
                    if (isset($config_status_brand[$stock_status_id]['available'])) {
                        $products_available = (bool)$config_status_brand[$stock_status_id]['available'];
                    }
                }
            }

            $json['products_available'] = $products_available;

            $json['set_all'] = is_bool($status_brands);

            foreach ($this->model_catalog_manufacturer->getManufacturers() as $m) {
                $json['results'][] = array(
                    'checked' => (is_array($status_brands) && in_array($m['manufacturer_id'], $status_brands)),
                    'id' => $m['manufacturer_id'],
                    'name' => $m['name'],
                );
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    /**
     * Сохраняет данные по производителям переданные в POST
     *
     * @return object Response true|false
     */
    public function save()
    {
        $json = false;
        if (!$this->user->hasPermission('access', 'feed/yandex_market')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $this->load->model('setting/setting');

            if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
                $ym_stock_brands = unserialize($this->config->get('yandex_market_stock_brands'));
                if (isset($this->request->post['yandex_market_all_brands']) && ($this->request->post['yandex_market_all_brands'] == 1)) {
                    $data = true;
                } else if (isset($this->request->post['yandex_market_stock_brands']) && is_array($this->request->post['yandex_market_stock_brands'])) {
                    $data = $this->request->post['yandex_market_stock_brands'];
                } else {
                    $data = array();
                }

                $ym_stock_brands[$this->request->post['stock_status_id']] = array(
                    'manufacturers' => $data,
                    'available' => (isset($this->request->post['products_available']) && $this->request->post['products_available']));

                $this->request->post['yandex_market_stock_brands'] = serialize($ym_stock_brands);
                unset($key, $this->request->post['stock_status_id'], $this->request->post['products_available']);
                $this->model_setting_setting->editSetting('yandex_market', $this->request->post);
                $json = true;
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    /**
     * Проверка прав текущего пользователя для доступа к модулю
     *
     * @return bool
     */
    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'feed/yandex_market')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !count($this->error);
    }

}

?>
