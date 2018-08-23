<?php


class Product extends ProductCore
{
    function getProductCartInfo(){
        $id = $this->id;
        return self::getProductCartInfoStatic($id);
    }

    static function getProductCartInfoStatic($id=null){
        if(!$id) return array();
        $context = Context::getContext();
        $cart = $context->cart;

        $info = array();
        foreach($cart->getProducts() as $cartProd){
            if($cartProd['id_product'] == $id) $info = $cartProd;
        }
        return $info;
    }

    static function SGetProductUnity($unity){
        $res = '';
        switch ($unity) {
            case 1:
                $res = 'уп.';
                break;
            case 2:
                $res = 'бл.';
                break;
            default:
                $res = 'шт.';
        }
        return $res;
    }

    static function instance($id, $full=true){
        return new Product($id, $full);
    }

    public static function getProductSiblings($id_product, $order_by = 'name', $order_way = 'asc') {

        if (!isset($context)) {
            $context = Context::getContext();
        }

        list($order_by, $order_way) = array_values(self::getOrder($order_by, $order_way));

        $prefix  = _DB_PREFIX_;
        $id_lang = $context->cookie->id_lang;
        $id_shop = $context->shop->id;
        $current_date = date('Y-m-d').' 00:00:00';

        $filter = self::getProductsFilter();

        $sql = "
        SELECT p.id_product

        FROM {$prefix}product AS p

        INNER JOIN {$prefix}product_shop AS ps
        ON  ps.id_product = p.id_product
            AND ps.id_shop = {$id_shop}
            AND ps.active = 1
            AND ps.show_price = 1
        
        LEFT JOIN {$prefix}category_product AS cp
        ON p.id_product = cp.id_product

        LEFT JOIN {$prefix}specific_price AS sp
        ON p.id_product = sp.id_product
            AND ps.id_shop = sp.id_shop

        LEFT JOIN {$prefix}product_attribute_shop AS product_attribute_shop
        ON  p.id_product = product_attribute_shop.id_product
            AND product_attribute_shop.default_on = 1
            AND product_attribute_shop.id_shop={$id_shop}

        LEFT JOIN {$prefix}product_lang AS pl
        ON  p.id_product = pl.id_product
            AND pl.id_lang = {$id_lang}

        LEFT JOIN {$prefix}image_shop AS image_shop
        ON image_shop.id_product = p.id_product
            AND image_shop.cover = 1
            AND image_shop.id_shop = {$id_shop}

        LEFT JOIN {$prefix}stock_available AS stock
        ON stock.id_product = p.id_product
            AND stock.id_product_attribute = 0
            AND stock.id_shop = {$id_shop} 
            AND stock.id_shop_group = 0

        LEFT JOIN {$prefix}image_lang AS il
        ON image_shop.id_image = il.id_image
            AND il.id_lang = {$id_lang}

        LEFT JOIN {$prefix}manufacturer AS m
        ON m.id_manufacturer = p.id_manufacturer

        WHERE ps.active = 1
            AND ps.show_price = 1
            AND stock.quantity > 0
            {$filter}

        GROUP BY p.id_product

        ORDER BY {$order_by}, p.id_product {$order_way}";

        $result = array_map(function($item) { return $item['id_product']; }, Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true));

       //die('<pre>'.print_r($result, true).'</pre>');

        $position = array_search($id_product, $result, false);

        return array (
            'prev_product' => isset($result[$position - 1]) ? $context->link->getProductLink($result[$position - 1]) : NULL,
            'next_product' => isset($result[$position + 1]) ? $context->link->getProductLink($result[$position + 1]) : NULL,
        );

        //die('<pre>'.print_r(array($position, $sss, $filter, $order_by, $order_way, $result), true).'</pre>');
    }

    public static function getOrder($order_by = 'name', $order_way = 'asc') {
        if (isset($_COOKIE['order_by'])) $order_by = $_COOKIE['order_by'];
        if (isset($_COOKIE['order_dir'])) $order_way = $_COOKIE['order_dir'];

        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'price';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'ps';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if ($order_by == 'quantity') $order_by_prefix = 'stock';

        if ($order_by == 'reference') $order_by_prefix = 'p';

        return array ('order_by' => "{$order_by_prefix}.{$order_by}", 'order_way' => $order_way);
    }

    /**
    * Get all available products
    *
    * @param int $id_lang Language id
    * @param int $start Start number
    * @param int $limit Number of products to return
    * @param string $order_by Field for ordering
    * @param string $order_way Way for ordering (ASC or DESC)
    * @return array Products details
    */
    public static function getProductsList($id_lang, $start = 0, $limit = 20, $order_by = 'name', $order_way = 'ASC', $id_category = false,
        $only_active = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $sql = 'SELECT p.*, p.price*2 as price_discount, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name
                FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
                ($id_category ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)' : '').'
                WHERE pl.`id_lang` = '.(int)$id_lang.' AND p.`quantity` = 0'.
                    ($id_category ? ' AND c.`id_category` = '.(int)$id_category : '').
                    ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                    ($only_active ? ' AND product_shop.`active` = 1' : '').'
                ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).
                ($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
                //die('<pre>'.print_r($sql, true).'</pre>');
        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($order_by == 'price') {
            Tools::orderbyPrice($rq, $order_way);
        }

        foreach ($rq as &$row) {
            //die('<pre>'.print_r($row, true).'</pre>');
            //$row.name .= '????';
            $row = Product::getTaxesInformations($row);
            $row['specific_prices'] = array('reduction' => 0);
            $row['reduction'] = 0;
            $row['features'] = 0;
        }

        return ($rq);
    }


    public static function getProductsFilter($id_category = NULL) {
        if (isset($id_category)) {
            $filter = " AND cp.id_category = {$id_category}";
        } else $filter = '';

        if (isset($_COOKIE['categories']) && $_COOKIE['categories']) {
            $categories_filter = array();
            foreach (explode('|', $_COOKIE['categories']) as $key => $item) {
                $categories_filter[] = "(select cp.id_product from "._DB_PREFIX_."category_product cp where cp.id_category IN({$item}))";
            }
            $filter2 = " cp.id_product IN ". implode(' AND cp.id_product IN ', $categories_filter);
            $sql = "
                select distinct p.id_product from "._DB_PREFIX_."product p 
                    left join "._DB_PREFIX_."category_product cp on cp.id_product = p.id_product
                    where ".$filter2;
            $result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $ff1 = '';
            foreach ($result2 as $key => $product) {
                $ff1 .= $product['id_product'].','; 
            }
            $ff1 .= '0';
            $filter = " AND cp.id_product IN (".$ff1.")";
        }

        if (isset($_COOKIE['discount']) && $_COOKIE['discount'] == '1') {
            $filter .= ' AND sp.reduction > 0';
        }

        if (isset($_COOKIE['manufact']) && $_COOKIE['manufact']) {
            $filter .= " AND p.id_manufacturer IN(" . $_COOKIE['manufact'] .")";
        }
        return $filter;
    }

}