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
        $filter = Product::getProductsFilter($id_category);
        $sql = "
        SELECT
            m.*,
            ml.description,
            ml.short_description,
            COUNT(DISTINCT cp.id_product) AS products_count

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
            AND st.quantity > 0

        {$filter}

        GROUP BY m.id_manufacturer

        ORDER BY m.name ASC";

        $manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($manufacturers === false) {
            return false;
        }

        // $total_manufacturers = count($manufacturers);
        // $rewrite_settings = (int)Configuration::get('PS_REWRITING_SETTINGS');
        // for ($i = 0; $i < $total_manufacturers; $i++) {
        //     $manufacturers[$i]['link_rewrite'] = ($rewrite_settings ? Tools::link_rewrite($manufacturers[$i]['name']) : 0);
        // }
        //die('<pre>'.print_r($manufacturers, true).'</pre>');
        return $manufacturers;
    }

}