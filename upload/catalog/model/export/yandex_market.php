<?php
class ModelExportYandexMarket extends Model {
	public function getCategory() {
		$query = $this->db->query("SELECT cd.name, c.category_id, c.parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' AND c.sort_order <> '-1'");

		return $query->rows;
	}
    //TODO remove out_of_stock and > 0
	public function getProduct($allowed_categories, $out_of_stock_id, $vendor_required = true) {
		$query = $this->db->query("SELECT p.*, pd.name, pd.description, m.name AS manufacturer, p2c.category_id, IFNULL(ps.price, p.price) AS price FROM " . DB_PREFIX . "product p JOIN " . DB_PREFIX . "product_to_category AS p2c ON (p.product_id = p2c.product_id) " . ($vendor_required ? '' : 'LEFT ') . "JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "product_special ps ON (p.product_id = ps.product_id) AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ps.date_start < NOW() AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()) WHERE p2c.category_id IN (" . $this->db->escape($allowed_categories) . ") AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1' GROUP BY p.product_id");

		return $query->rows;
	}

    public function brandsFilter($products) {
        // Получаем настройки модуля экспорта
        $ym_stock_brands = (array) unserialize($this->config->get('yandex_market_stock_brands'));
        var_dump($ym_stock_brands);

        // перебираем все продукты, нужные добавляем в ленту
        foreach ($products as &$product) {
            $product['available'] = 'false';

            // Пропускаем все товары, у которых нет в наличии статуса
            if (isset($ym_stock_brands[$product['stock_status_id']])) {
                $ym_brands = isset($ym_stock_brands[$product['stock_status_id']]['manufacturers'])
                    ? $ym_stock_brands[$product['stock_status_id']]['manufacturers']
                    : null;

                // Атрибут доступности товара для Яндекс Маркет
                if(isset($ym_stock_brands[$product['stock_status_id']]['available'])
                    && $ym_stock_brands[$product['stock_status_id']]['available']){
                    $product['available'] = 'true';
                }

                if (false === (is_bool($ym_brands) && ($ym_brands == true))
                    && (false === (is_array($ym_brands) && in_array($product['manufacturer_id'], $ym_brands)))){
                    // Если производитель товара не найден в отмеченых брэндах Yandex Market - пропускаем
                    unset($product);
                    continue;
                }
            }else{
                // Если статус товара не найден в настройках Yandex Market - пропускаем
                exit;
                unset($product);
                continue;
            }


        }
        return $products;
    }

    /*
     * Возвращает id по названию.
     *
     * @var $name stock_status_name
     *
     * @return int stock_status_id
     */
    public function getStockId($name){
        $id = $this->db->query('SELECT `stock_status_id` FROM ' .DB_PREFIX. ' `stock_status` WHERE `name`="' .$name .'";');

        return $id->row['stock_status_id'];
    }
}
?>
