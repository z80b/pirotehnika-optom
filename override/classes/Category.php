<?php

class Category extends CategoryCore {

    public static function getCheckedCategories($id_category = NULL) {

        $result = array();
        $filters = json_decode($_COOKIE['filter'], true);
        $filter = isset($filters[$id_category]) ? $filters[$id_category] : '';

        if ($filter) {
            if (isset($filter['categories']) && $filter['categories']) {
                $categories = array();
                foreach (explode(',', $filter['categories']) as $key => $item) {
                    $categories = array_merge($categories, preg_split("/[\|\,]/", $item));
                }
                $result['categories'] = array_flip($categories);
            }
            if (isset($filter['manufact']) && $filter['manufact']) {
                $result['manufact'] = array_flip($filter['manufact']);   
            }
            if (isset($filter['discount']) && $filter['discount']) {
                $result['discount'] = 1;
            }
        }   

        return $result;
    }

    public static function getSubcategoriesList($id_category, $id_lang) {

        $categories = self::getCategoryChildren($id_category, $id_lang);
        $filter = Product::getProductsFilter($id_category, false);

        foreach ($categories as $index => $category) {
            if ($subcategories = self::getCategoryChildren($category['id_category'], $id_lang)) {
                $categories[$index]['products_count'] = Category::getProductsCount($category['id_category'], $filter);
                $categories[$index]['categories'] = $subcategories;
            }
        }

        return $categories;
    }

    public static function getCategoryChildren($id_category = NULL, $id_lang) {

        $db_prefix = _DB_PREFIX_;
        $filter = Product::getProductsFilter($id_category, false);
        $sql = "

        SELECT * FROM {$db_prefix}category AS c

        LEFT JOIN {$db_prefix}category_lang AS cl
        ON c.id_category = cl.id_category
            AND cl.id_lang = {$id_lang}

        LEFT JOIN {$db_prefix}category_product AS cp
        ON cp.id_category = c.id_category

        LEFT JOIN {$db_prefix}product AS p
        ON p.id_product = cp.id_product

        INNER JOIN {$db_prefix}product_shop AS ps
        ON  ps.id_product = cp.id_product

        LEFT JOIN {$db_prefix}specific_price AS sp
        ON p.id_product = sp.id_product
            AND ps.id_shop = sp.id_shop

        LEFT JOIN {$db_prefix}stock_available AS st
        ON st.id_product = cp.id_product

        WHERE c.id_parent = {$id_category}
            {$filter}
            AND c.active
            AND st.id_shop = 1
            AND ps.active = 1
            AND ps.show_price = 1


        GROUP BY c.id_category

        ORDER BY c.position";

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        foreach ($result as $key => $category) {
            $result[$key]['products_count'] = self::getProductsCount($category['id_category'], $filter);
        }

        return $result;
    }

    public static function getGeneralCategories() {
        $id_lang = Context::getContext()->cookie->id_lang;
        $categories = Category::getCategories($id_lang);
        $result = array();
        foreach ($categories as $_category) {
            foreach ($_category as $key => $item) {
                $category = $item['infos'];
                if ($category['level_depth'] == 2)
                    $result[$category['id_category']] = $category;
            }
        }
        return $result;
    }
    
    public static function getCategoriesList($id_lang) {
        $arr = Category::getCategories($id_lang, true, true, '', 'ORDER BY c.position ASC');
        $categories = array();
        $subcategories = array();

        foreach ($arr as $value) {
            foreach ($value as $key => $item) {
                $category = $item['infos'];
                if ($category['level_depth'] == 4) {
                    if (!isset($subcategories[$category['id_parent']])) {
                        $subcategories[$category['id_parent']] = array();
                    }
                    $subcategories[$category['id_parent']][$category['id_category']] = $category;
                }
            }
        }

        foreach ($arr as $value) {
            foreach ($value as $key => $item) {
                $category = $item['infos'];
                if ($category['level_depth'] == 2)
                    if (!isset($categories[$category['id_category']]))
                        $categories[$category['id_category']] = $category;
                    else
                        array_merge($categories[$category['id_category']], $category);
                    
                if ($category['level_depth'] == 3) {
                    if (!isset($categories[$category['id_parent']])) {
                        $categories[$category['id_parent']] = array('categories' => array());

                    }

                    if (isset($subcategories[$category['id_category']])) {
                        $category['categories'] = $subcategories[$category['id_category']];
                    }

                    $categories[$category['id_parent']]['categories'][$category['id_category']] = $category;

                }
            }
        }

        return $categories;
    }

    public static function getProductsList($id_lang, $id_category, $page_number = 0, $nb_products = 10, $count = false, $order_by = null, $order_way = null, $beginning = false, $ending = false, Context $context = null) {
        if (!Validate::isBool($count)) {
            die(Tools::displayError());
        }

        if (!$context) {
            $context = Context::getContext();
        }
        if ($page_number < 0) {
            $page_number = 0;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }

        list($order_by, $order_way) = array_values(Product::getOrder($order_by, $order_way));

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }

        $prefix  = _DB_PREFIX_;
        $id_shop = Context::getContext()->shop->id;
        $current_date = date('Y-m-d').' 00:00:00';
        $offset = $page_number * $nb_products;
        $limit = $nb_products;
        $filter = Product::getProductsFilter($id_category);

        if ($count) return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue("
            SELECT COUNT(DISTINCT p.id_product)
            FROM {$prefix}product AS p

            INNER JOIN {$prefix}product_shop AS ps
            ON (ps.id_product = p.id_product AND ps.id_shop = {$id_shop})

            LEFT JOIN {$prefix}category_product AS cp
            ON p.id_product = cp.id_product

            LEFT JOIN {$prefix}stock_available AS stock
            ON stock.id_product = p.id_product
                AND stock.id_product_attribute = 0
                AND stock.id_shop = {$id_shop} 
                AND stock.id_shop_group = 0

            LEFT JOIN {$prefix}specific_price AS sp
            ON p.id_product = sp.id_product
                AND ps.id_shop = sp.id_shop

            WHERE
                ps.active = 1
            AND stock.quantity > 0
            AND ps.show_price = 1
            {$filter}");

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]).'.'.pSQL($order_by[1]).'';
        }

        $sql = "
        SELECT
            p.*,
            (p.price - p.price * sp.reduction) AS price_discount,
            sp.reduction,
            ps.*,
            stock.out_of_stock,
            IFNULL (stock.quantity, 0) AS quantity,
            pl.description,
            pl.description_short,
            pl.available_now,
            pl.available_later,
            IFNULL (product_attribute_shop.id_product_attribute, 0) AS id_product_attribute,
            pl.link_rewrite,
            pl.meta_description,
            pl.meta_keywords,
            pl.meta_title,
            pl.name,
            image_shop.id_image AS id_image,
            il.legend,
            m.name AS manufacturer_name
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

        ORDER BY {$order_by}, p.id_product {$order_way}

        LIMIT {$offset}, {$limit}";

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        foreach ($result as $key => $product) {
            $result[$key]['specific_prices'] = array(
                'reduction' => $product['reduction'],
                'reduction_type' => 'percentage'
            );
            $result[$key]['price'] = isset($product['price_discount']) ? $product['price_discount'] : $product['price'];
            $result[$key]['price_without_reduction'] = $product['price'];
            $result[$key]['features'] = Product::getFrontFeaturesStatic($id_lang, $product['id_product']);
            $result[$key]['link'] = $context->link->getProductLink(
                $product['id_product'],
                $product['link_rewrite'],
                $product['id_category_default'],
                $product['ean13']);
        }
        return $result;
    }

    public static function getProductsCount($id_category, $filter) {
        $prefix  = _DB_PREFIX_;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue("
            SELECT COUNT(DISTINCT p.id_product)
            FROM {$prefix}product AS p

            INNER JOIN {$prefix}product_shop AS ps
            ON ps.id_product = p.id_product AND ps.id_shop = 1

            LEFT JOIN {$prefix}category_product AS cp
            ON p.id_product = cp.id_product

            LEFT JOIN {$prefix}stock_available AS stock
            ON stock.id_product = p.id_product
                AND stock.id_product_attribute = 0
                AND stock.id_shop = 1
                AND stock.id_shop_group = 0

            LEFT JOIN {$prefix}specific_price AS sp
            ON p.id_product = sp.id_product
                AND ps.id_shop = sp.id_shop

            WHERE
                ps.active = 1
            AND stock.quantity > 0
            AND ps.show_price = 1
            AND cp.id_category = {$id_category} {$filter}
        ");
    }

}