<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class my_paymentsCore extends ObjectModel
{

    /** Поля таблицы */
    public $id_my_payments;
    public $id_shop;
    public $add_from;
    public $date_payment;
    public $rperiod;
    public $id_my_payments_tip;
    public $id_my_payments_vid;
    public $id_my_payments_spos;
    public $id_my_payments_kas;
    public $order;
    public $id_customer;
    public $summa_all;
    public $summa_yes;
    public $summa_no;
    public $status;
    public $prim;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'my_payments',
        'primary' => 'id_my_payments',
        'multilang' => false,
        'fields' => array(
            'id_shop' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_my_payments_tip' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'add_from' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_payment' =>            array('type' => self::TYPE_DATE),
            'rperiod' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_my_payments_vid' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_my_payments_spos' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_my_payments_kas' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'order' =>                   array('type' => self::TYPE_STRING,  'size' => 10),
            'id_customer' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'summa_all' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'summa_yes' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'summa_no' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'status' =>            array('type' => self::TYPE_BOOL),
            'prim' =>        array('type' => self::TYPE_STRING,  'size' => 500),
            'date_add' =>            array('type' => self::TYPE_DATE),
            'date_upd' =>            array('type' => self::TYPE_DATE),

        ),
    );

/*     protected $webserviceParameters = array(
        'fields' => array(
            'active' => array(),
            'link_rewrite' => array('getter' => 'getLink', 'setter' => false),
        ),
        'associations' => array(
            'addresses' => array('resource' => 'address', 'setter' => false, 'fields' => array(
                'id' => array('xlink_resource' => 'addresses'),
            )),
        ),
    );
 */
    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);

/*         $this->link_rewrite = $this->getLink();
        $this->image_dir = _PS_MANU_IMG_DIR_;
 */    }


	/**
    * Get all типы оплат
    *
    */
    public static function getMy_Payments_tips($tip = null)
    {
//		$this->mpt_array = array('1' => 'Приход', '2' => 'Расход');
//		$this->mpt_array[0] = 'Приход';
//		$this->mpt_array[1] = 'Расход';
		$tip_from_bd = false;
		if ($tip_from_bd == true) {
			if (isset($tip) && ($tip === 1)) { 
				$mp_where = 'WHERE id_my_payments_tip = 1'; 
			} elseif (isset($tip) && ($tip === 2)) { 
				$mp_where = 'WHERE id_my_payments_tip = 2';
			} else { 
				$mp_where = ''; 
			}
			
			$cache_id = 'my_payments::getMy_Payments_tips_'.(int)$tip;
			if (!Cache::isStored($cache_id)) {
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT * FROM `'._DB_PREFIX_.'my_payments_tip` '.$mp_where);
				Cache::store($cache_id, $result);
				return $result;
			}
			return Cache::retrieve($cache_id);
			
		} else {
			if (isset($tip) && ($tip === 1)) {
				$mpt_array = array(
					'0' => array(
						'id_my_payments_tip' => '1',
						'name' => 'Приход'
					)
				);
			} elseif (isset($tip) && ($tip === 2)) {
				$mpt_array = array(
					'0' => array(
						'id_my_payments_tip' => '2',
						'name' => 'Расход'
					)
				);
			} else {
				$mpt_array = array(
					'0' => array(
						'id_my_payments_tip' => '1',
						'name' => 'Приход'
					),
					'1' => array(
						'id_my_payments_tip' => '2',
						'name' => 'Расход'
					)
				);
			}
			return $mpt_array;
		}
    }

	/**
    * Get all виды прихода и расхода
    *
    */
    public static function getMy_payments_Vids()
    {
		$mp_where = 'WHERE kod > 40 and status = 1'; 
		$tip2 = 0;
				switch ($tip2) {
					case 1: {
						$mp_where = 'WHERE kod < 20 and status = 1'; 
						break;
					}
					case 2: {
						$mp_where = 'WHERE kod > 20 and status = 1'; 
						break;
					}	
					default: {	
						$mp_where = 'WHERE status = 1'; 
						break;
					}	
				}
		
		$cache_id = 'my_payments::getMy_payments_Vids_';
		if (!Cache::isStored($cache_id)) {
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT * FROM `'._DB_PREFIX_.'my_payments_vid` '.$mp_where);
			Cache::store($cache_id, $result);
			return $result;
		}
		return Cache::retrieve($cache_id);
		
    }


	/**
    * Get all виды оплат нал - бнал
    *
    */
    public static function getMy_payments_sposs()
    {
		$mp_where = 'WHERE status = 1'; 
		
		$cache_id = 'my_payments::getMy_payments_sposs_';
		if (!Cache::isStored($cache_id)) {
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT * FROM `'._DB_PREFIX_.'my_payments_spos` '.$mp_where);
			Cache::store($cache_id, $result);
			return $result;
		}
		return Cache::retrieve($cache_id);
		
    }

	/**
    * Get all типы касс
    *
    */
    public static function getMy_payments_kass()
    {
	$mp_where = 'WHERE status = 1'; 

	$cache_id = 'my_payments::getMy_payments_kass_';
		if (!Cache::isStored($cache_id)) {
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT * FROM `'._DB_PREFIX_.'my_payments_kas` '.$mp_where);
			Cache::store($cache_id, $result);
			return $result;
		}
		return Cache::retrieve($cache_id);
		
    }

    public function getTotalRaspred($id_my_payments)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT SUM(amount)
		FROM '._DB_PREFIX_.'order_payment
		WHERE `id_my_payments` = '.(int)$id_my_payments);
        return (float)$result;
    }
	
	
    public function add($autodate = true, $null_values = true)
    {
        return parent::add($autodate, $null_values);
    }



    public function delete()
    {
		return parent::delete();
        
    }

    /**
     * Delete several objects from database
     *
     * return boolean Deletion result
     */
    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            die(Tools::displayError());
        }

        $result = true;
        foreach ($selection as $id) {
            $this->id = (int)$id;
            $this->id_address = Manufacturer::getManufacturerAddress();
            $result = $result && $this->delete();
        }

        return $result;
    }

    protected function getManufacturerAddress()
    {
        if (!(int)$this->id) {
            return false;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_address` FROM '._DB_PREFIX_.'address WHERE `id_manufacturer` = '.(int)$this->id);
    }

    /**
     * Return manufacturers
     *
     * @param bool $get_nb_products [optional] return products numbers for each
     * @param int $id_lang
     * @param bool $active
     * @param int $p
     * @param int $n
     * @param bool $all_group
     * @return array Manufacturers
     */
    public static function getManufacturers($get_nb_products = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_group = false, $group_by = false)
    {
        if (!$id_lang) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }
        if (!Group::isFeatureActive()) {
            $all_group = true;
        }

        $manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT m.*, ml.`description`, ml.`short_description`
		FROM `'._DB_PREFIX_.'manufacturer` m
		'.Shop::addSqlAssociation('manufacturer', 'm').'
		INNER JOIN `'._DB_PREFIX_.'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = '.(int)$id_lang.')
		'.($active ? 'WHERE m.`active` = 1' : '')
        .($group_by ? ' GROUP BY m.`id_manufacturer`' : '').'
		ORDER BY m.`name` ASC
		'.($p ? ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n : ''));
        if ($manufacturers === false) {
            return false;
        }

        if ($get_nb_products) {
            $sql_groups = '';
            if (!$all_group) {
                $groups = FrontController::getCurrentCustomerGroups();
                $sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
            }

            $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT  p.`id_manufacturer`, COUNT(DISTINCT p.`id_product`) as nb_products
					FROM `'._DB_PREFIX_.'product` p USE INDEX (product_manufacturer)
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'manufacturer` as m ON (m.`id_manufacturer`= p.`id_manufacturer`)
					WHERE p.`id_manufacturer` != 0 AND product_shop.`visibility` NOT IN ("none")
					'.($active ? ' AND product_shop.`active` = 1 ' : '').'
					'.(Group::isFeatureActive() && $all_group ? '' : ' AND EXISTS (
						SELECT 1
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` '.$sql_groups.'
					)').'
					GROUP BY p.`id_manufacturer`'
                );

            $counts = array();
            foreach ($results as $result) {
                $counts[(int)$result['id_manufacturer']] = (int)$result['nb_products'];
            }

            if (count($counts)) {
                foreach ($manufacturers as $key => $manufacturer) {
                    if (array_key_exists((int)$manufacturer['id_manufacturer'], $counts)) {
                        $manufacturers[$key]['nb_products'] = $counts[(int)$manufacturer['id_manufacturer']];
                    } else {
                        $manufacturers[$key]['nb_products'] = 0;
                    }
                }
            }
        }

        $total_manufacturers = count($manufacturers);
        $rewrite_settings = (int)Configuration::get('PS_REWRITING_SETTINGS');
        for ($i = 0; $i < $total_manufacturers; $i++) {
            $manufacturers[$i]['link_rewrite'] = ($rewrite_settings ? Tools::link_rewrite($manufacturers[$i]['name']) : 0);
        }
        return $manufacturers;
    }

    /**
     * Return name from id
     *
     * @param int $id_manufacturer Manufacturer ID
     * @return string name
     */
    protected static $cacheName = array();
    public static function getNameById($id_manufacturer)
    {
        if (!isset(self::$cacheName[$id_manufacturer])) {
            self::$cacheName[$id_manufacturer] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `name`
				FROM `'._DB_PREFIX_.'manufacturer`
				WHERE `id_manufacturer` = '.(int)$id_manufacturer.'
				AND `active` = 1'
            );
        }

        return self::$cacheName[$id_manufacturer];
    }

    public static function getIdByName($name)
    {
        $result = Db::getInstance()->getRow('
			SELECT `id_manufacturer`
			FROM `'._DB_PREFIX_.'manufacturer`
			WHERE `name` = \''.pSQL($name).'\''
        );

        if (isset($result['id_manufacturer'])) {
            return (int)$result['id_manufacturer'];
        }

        return false;
    }

    public function getLink()
    {
        return Tools::link_rewrite($this->name);
    }

    public static function getProducts($id_manufacturer, $id_lang, $p, $n, $order_by = null, $order_way = null,
        $get_total = false, $active = true, $active_category = true, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        if ($p < 1) {
            $p = 1;
        }

        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'name';
        }

        if (empty($order_way)) {
            $order_way = 'ASC';
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }

        $groups = FrontController::getCurrentCustomerGroups();
        $sql_groups = count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1';

        /* Return only the number of products */
        if ($get_total) {
            $sql = '
				SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				WHERE p.id_manufacturer = '.(int)$id_manufacturer
                .($active ? ' AND product_shop.`active` = 1' : '').'
				'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				AND EXISTS (
					SELECT 1
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)'.
                    ($active_category ? ' INNER JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '').'
					WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` '.$sql_groups.'
				)';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            return (int)count($result);
        }
        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
        }
        $alias = '';
        if ($order_by == 'price') {
            $alias = 'product_shop.';
        } elseif ($order_by == 'name') {
            $alias = 'pl.';
        } elseif ($order_by == 'manufacturer_name') {
            $order_by = 'name';
            $alias = 'm.';
        } elseif ($order_by == 'quantity') {
            $alias = 'stock.';
        } else {
            $alias = 'p.';
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'
            .(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '').'
			, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`,
			pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
				DATEDIFF(
					product_shop.`date_add`,
					DATE_SUB(
						"'.date('Y-m-d').' 00:00:00",
						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
					)
				) > 0 AS new'
            .' FROM `'._DB_PREFIX_.'product` p
			'.Shop::addSqlAssociation('product', 'p').
            (Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
						ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
				ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il
				ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
				ON (m.`id_manufacturer` = p.`id_manufacturer`)
			'.Product::sqlStock('p', 0);

        if (Group::isFeatureActive() || $active_category) {
            $sql .= 'JOIN `'._DB_PREFIX_.'category_product` cp ON (p.id_product = cp.id_product)';
            if (Group::isFeatureActive()) {
                $sql .= 'JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.`id_category` = cg.`id_category` AND cg.`id_group` '.$sql_groups.')';
            }
            if ($active_category) {
                $sql .= 'JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1';
            }
        }

        $sql .= '
				WHERE p.`id_manufacturer` = '.(int)$id_manufacturer.'
				'.($active ? ' AND product_shop.`active` = 1' : '').'
				'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				GROUP BY p.id_product
				ORDER BY '.$alias.'`'.bqSQL($order_by).'` '.pSQL($order_way).'
				LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }

        return Product::getProductsProperties($id_lang, $result);
    }

    public function getProductsLite($id_lang)
    {
        $context = Context::getContext();
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        return Db::getInstance()->executeS('
		SELECT p.`id_product`,  pl.`name`
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('pl').'
		)
		WHERE p.`id_manufacturer` = '.(int)$this->id.
        ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : ''));
    }

    /*
    * Specify if a manufacturer already in base
    *
    * @param $id_manufacturer Manufacturer id
    * @return bool
    */
    public static function manufacturerExists($id_manufacturer)
    {
        $row = Db::getInstance()->getRow('
			SELECT `id_manufacturer`
			FROM '._DB_PREFIX_.'manufacturer m
			WHERE m.`id_manufacturer` = '.(int)$id_manufacturer
        );

        return isset($row['id_manufacturer']);
    }

    public function getAddresses($id_lang)
    {
        return Db::getInstance()->executeS('
			SELECT a.*, cl.name AS `country`, s.name AS `state`
			FROM `'._DB_PREFIX_.'address` AS a
			LEFT JOIN `'._DB_PREFIX_.'country_lang` AS cl ON (
				cl.`id_country` = a.`id_country`
				AND cl.`id_lang` = '.(int)$id_lang.'
			)
			LEFT JOIN `'._DB_PREFIX_.'state` AS s ON (s.`id_state` = a.`id_state`)
			WHERE `id_manufacturer` = '.(int)$this->id.'
			AND a.`deleted` = 0'
        );
    }

    public function getWsAddresses()
    {
        return Db::getInstance()->executeS('
			SELECT a.id_address as id
			FROM `'._DB_PREFIX_.'address` AS a
			'.Shop::addSqlAssociation('manufacturer', 'a').'
			WHERE a.`id_manufacturer` = '.(int)$this->id.'
			AND a.`deleted` = 0'
        );
    }

    public function setWsAddresses($id_addresses)
    {
        $ids = array();

        foreach ($id_addresses as $id) {
            $ids[] = (int)$id['id'];
        }

        $result1 = (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'address`
			SET id_manufacturer = 0
			WHERE id_manufacturer = '.(int)$this->id.'
			AND deleted = 0') !== false
        );

        $result2 = true;
        if (count($ids)) {
            $result2 = (Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'address`
				SET id_customer = 0, id_supplier = 0, id_manufacturer = '.(int)$this->id.'
				WHERE id_address IN('.implode(',', $ids).')
				AND deleted = 0') !== false
            );
        }

        return ($result1 && $result2);
    }
}
