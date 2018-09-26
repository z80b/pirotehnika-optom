<?php

class Manufacturer extends ManufacturerCore {

    public static function getManufacturersList($id_category = NULL, $id_lang = NULL)
    {
        if (!$id_lang) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        if (!Group::isFeatureActive()) {
            $all_group = true;
        }

        $prefix  = _DB_PREFIX_;
        $filter = Product::getProductsFilter($id_category, true, false);

        $sql = "
        SELECT
            m.*,
            ml.description,
            ml.short_description

        FROM {$prefix}manufacturer AS m

        INNER JOIN {$prefix}manufacturer_lang AS ml
        ON m.id_manufacturer = ml.id_manufacturer
            AND ml.id_lang = '{$id_lang}'

        LEFT JOIN {$prefix}product AS p
        ON m.id_manufacturer = p.id_manufacturer

        LEFT JOIN {$prefix}category_product AS cp
        ON p.id_product = cp.id_product

        INNER JOIN {$prefix}product_shop AS ps
        ON  ps.id_product = cp.id_product

        LEFT JOIN {$prefix}specific_price AS sp
        ON p.id_product = sp.id_product
            AND ps.id_shop = sp.id_shop

        LEFT JOIN {$prefix}stock_available AS st
        ON st.id_product = cp.id_product

        WHERE m.active = 1
            AND ps.active = 1
            AND ps.show_price = 1
            {$filter}

        GROUP BY m.id_manufacturer

        ORDER BY m.name ASC";

        $manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($manufacturers === false) {
            return false;
        }

        $filter = Product::getProductsFilter($id_category);

        foreach ($manufacturers as $key => $manufacturer) {
            $manufacturers[$key]['products_count'] = self::getProductsCount($manufacturer['id_manufacturer'], $filter);
        }

        return $manufacturers;
    }

    public static function getProductsCount($id_manufacturer, $filter) {
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
            AND p.id_manufacturer = {$id_manufacturer} {$filter}
        ");
    }

}