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

/**
 * @since 1.5.0
 */
class RevizCore extends ObjectModel
{
    public $id_reviz;
    public $status_sost;
    public $status_correctno;
    public $id_shop;
    public $id_warehouse;
    public $reviz_name;
    public $date_do;
    public $date_actual;
    public $sum_plus;
    public $sum_minus;
    public $sum_itog;
    public $prim;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'reviz',
        'primary' => 'id_reviz',
        'fields' => array(
            'id_reviz' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'status_sost' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'status_correctno' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_shop' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_warehouse' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'reviz_name' =>            array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'date_do' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_actual' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'sum_plus' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'sum_minus' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'sum_itog' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'prim' =>            array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false),
        ),
    );

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = array(
        'fields' => array(
            'id_supplier' => array('xlink_resource' => 'suppliers'),
            'id_lang' => array('xlink_resource' => 'languages'),
            'id_warehouse' => array('xlink_resource' => 'warehouses'),
            'id_supply_order_state' => array('xlink_resource' => 'supply_order_states'),
            'id_currency' => array('xlink_resource' => 'currencies'),
        ),
        'hidden_fields' => array(
            'id_ref_currency',
        ),
        'associations' => array(
            'supply_order_details' => array(
                'resource' => 'supply_order_detail',
                'fields' => array(
                    'id' => array(),
                    'id_product' => array(),
                    'id_product_attribute' => array(),
                    'supplier_reference' => array(),
                    'product_name' => array(),
                ),
            ),
        ),
    );

    /**
     * @see ObjectModel::update()
     */
    public function update($null_values = false)
    {
        $this->calculatePrices();

        $res = parent::update($null_values);

        if ($res && !$this->is_template) {
            $this->addHistory();
        }

        return $res;
    }

    /**
     * @see ObjectModel::add()
     */
    public function add($autodate = true, $null_values = false)
    {
        $res = parent::add($autodate, $null_values);

        return $res;
    }

    /**
     * Checks all products in this order and calculate prices
     * Applies the global discount if necessary
     */
    protected function calculatePrices()
    {
        $this->total_te = 0;
        $this->total_with_discount_te = 0;
        $this->total_tax = 0;
        $this->total_ti = 0;
        $is_discount = false;

        if (is_numeric($this->discount_rate) && (float)$this->discount_rate >= 0) {
            $is_discount = true;
        }

        // gets all product entries in this order
        $entries = $this->getEntriesCollection();

        foreach ($entries as $entry) {
            /** @var SupplyOrderDetail $entry */
            // applys global discount rate on each product if possible
            if ($is_discount) {
                $entry->applyGlobalDiscount((float)$this->discount_rate);
            }

            // adds new prices to the total
            $this->total_te += $entry->price_with_discount_te;
            $this->total_with_discount_te += $entry->price_with_order_discount_te;
            $this->total_tax += $entry->tax_value_with_order_discount;
            $this->total_ti = $this->total_tax + $this->total_with_discount_te;
        }

        // applies global discount rate if possible
        if ($is_discount) {
            $this->discount_value_te = $this->total_te - $this->total_with_discount_te;
        }
    }

    /**
     * Retrieves the product entries for the current order
     *
     * @param int $id_lang Optional Id Lang - Uses Context::language::id by default
     * @return array
     */
    public function getEntries($id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = Context::getContext()->language->id;
        }

        // build query
        $query = new DbQuery();

        $query->select('
			s.*,
			IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.name, \' - \', al.name SEPARATOR \', \')), pl.name) as name_displayed');

        $query->from('supply_order_detail', 's');

        $query->innerjoin('product_lang', 'pl', 'pl.id_product = s.id_product AND pl.id_lang = '.$id_lang);

        $query->leftjoin('product', 'p', 'p.id_product = s.id_product');
        $query->leftjoin('product_attribute_combination', 'pac', 'pac.id_product_attribute = s.id_product_attribute');
        $query->leftjoin('attribute', 'atr', 'atr.id_attribute = pac.id_attribute');
        $query->leftjoin('attribute_lang', 'al', 'al.id_attribute = atr.id_attribute AND al.id_lang = '.$id_lang);
        $query->leftjoin('attribute_group_lang', 'agl', 'agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$id_lang);

        $query->where('s.id_supply_order = '.(int)$this->id);

        $query->groupBy('s.id_supply_order_detail');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * Retrieves the details entries (i.e. products) collection for the current order
     *
     * @return PrestaShopCollection Collection of SupplyOrderDetail
     */
    public function getEntriesCollection()
    {
        $details = new PrestaShopCollection('SupplyOrderDetail');
        $details->where('id_supply_order', '=', $this->id);
        return $details;
    }


    /**
     * Check if the order has entries
     *
     * @return bool Has/Has not
     */
    public function hasEntries()
    {
        $query = new DbQuery();
        $query->select('COUNT(*)');
        $query->from('supply_order_detail', 's');
        $query->where('s.id_supply_order = '.(int)$this->id);

        return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) > 0);
    }

    /**
     * Check if the current state allows to edit the current order
     *
     * @return bool
     */
    public function isEditable()
    {
        $query = new DbQuery();
        $query->select('s.editable');
        $query->from('supply_order_state', 's');
        $query->where('s.id_supply_order_state = '.(int)$this->id_supply_order_state);

        return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) == 1);
    }

    /**
     * Checks if the current state allows to generate a delivery note for this order
     *
     * @return bool
     */
    public function isDeliveryNoteAvailable()
    {
        $query = new DbQuery();
        $query->select('s.delivery_note');
        $query->from('supply_order_state', 's');
        $query->where('s.id_supply_order_state = '.(int)$this->id_supply_order_state);

        return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) == 1);
    }

    /**
     * Checks if the current state allows to add products in stock
     *
     * @return bool
     */
    public function isInReceiptState()
    {
        $query = new DbQuery();
        $query->select('s.receipt_state');
        $query->from('supply_order_state', 's');
        $query->where('s.id_supply_order_state = '.(int)$this->id_supply_order_state);

        return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) == 1);
    }


    /**
     * Removes all products from the order
     */
    public function resetProducts()
    {
        $products = $this->getEntriesCollection();

        foreach ($products as $p) {
            $p->delete();
        }
    }

    /**
     * For a given $id_warehouse, tells if it has pending supply orders
     *
     * @param int $id_warehouse
     * @return bool
     */
    public static function warehouseHasPendingOrders($id_warehouse)
    {
        if (!$id_warehouse) {
            return false;
        }

        $query = new DbQuery();
        $query->select('COUNT(so.id_supply_order) as supply_orders');
        $query->from('supply_order', 'so');
        $query->leftJoin('supply_order_state', 'sos', 'so.id_supply_order_state = sos.id_supply_order_state');
        $query->where('sos.enclosed != 1');
        $query->where('so.id_warehouse = '.(int)$id_warehouse);

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        return ($res > 0);
    }

    /**
     * For a given $id_supplier, tells if it has pending supply orders
     *
     * @param int $id_supplier Id Supplier
     * @return bool
     */
    public static function supplierHasPendingOrders($id_supplier)
    {
        if (!$id_supplier) {
            return false;
        }

        $query = new DbQuery();
        $query->select('COUNT(so.id_supply_order) as supply_orders');
        $query->from('supply_order', 'so');
        $query->leftJoin('supply_order_state', 'sos', 'so.id_supply_order_state = sos.id_supply_order_state');
        $query->where('sos.enclosed != 1');
        $query->where('so.id_supplier = '.(int)$id_supplier);

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        return ($res > 0);
    }

    /**
     * For a given id or reference, tells if the supply order exists
     *
     * @param int|string $match Either the reference of the order, or the Id of the order
     * @return int SupplyOrder Id
     */
    public static function exists($match)
    {
        if (!$match) {
            return false;
        }

        $query = new DbQuery();
        $query->select('id_supply_order');
        $query->from('supply_order', 'so');
        $query->where('so.id_supply_order = '.(int)$match.' OR so.reference = "'.pSQL($match).'"');

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        return ((int)$res);
    }

    /**
     * For a given reference, returns the corresponding supply order
     *
     * @param string $reference Reference of the order
     * @return bool|SupplyOrder
     */
    public static function getSupplyOrderByReference($reference)
    {
        if (!$reference) {
            return false;
        }

        $query = new DbQuery();
        $query->select('id_supply_order');
        $query->from('supply_order', 'so');
        $query->where('so.reference = "'.pSQL($reference).'"');
        $id_supply_order = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        if (!$id_supply_order) {
            return false;
        }

        $supply_order = new SupplyOrder($id_supply_order);
        return $supply_order;
    }

    /**
     * @see ObjectModel::hydrate()
     */
    public function hydrate(array $data, $id_lang = null)
    {
        $this->id_lang = $id_lang;
        if (isset($data[$this->def['primary']])) {
            $this->id = $data[$this->def['primary']];
        }
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this)) {
                // formats prices and floats
                if ($this->def['fields'][$key]['validate'] == 'isFloat' ||
                    $this->def['fields'][$key]['validate'] == 'isPrice') {
                    $value = Tools::ps_round($value, 6);
                }
                $this->$key = $value;
            }
        }
    }


    /**
     * Gets the reference of a given order
     *
     * @param int $id_supply_order
     * @return bool|string
     */
    public static function getReferenceById($id_supply_order)
    {
        if (!$id_supply_order) {
            return false;
        }

        $query = new DbQuery();
        $query->select('so.reference');
        $query->from('supply_order', 'so');
        $query->where('so.id_supply_order = '.(int)$id_supply_order);
        $ref = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        return (pSQL($ref));
    }

    public function getAllExpectedQuantity()
    {
        return Db::getInstance()->getValue('
			SELECT SUM(`quantity_expected`)
			FROM `'._DB_PREFIX_.'supply_order_detail`
			WHERE `id_supply_order` = '.(int)$this->id
        );
    }

    public function getAllReceivedQuantity()
    {
        return Db::getInstance()->getValue('
			SELECT SUM(`quantity_received`)
			FROM `'._DB_PREFIX_.'supply_order_detail`
			WHERE `id_supply_order` = '.(int)$this->id
        );
    }

    public function getAllPendingQuantity()
    {
        return Db::getInstance()->getValue('
			SELECT (SUM(`quantity_expected`) - SUM(`quantity_received`))
			FROM `'._DB_PREFIX_.'supply_order_detail`
			WHERE `id_supply_order` = '.(int)$this->id
        );
    }

    /*********************************\
     *
     * Webservices Specific Methods
     *
     *********************************/

    /**
     * Webservice : gets the ids supply_order_detail associated to this order
     *
     * @return array
     */
    public function getWsSupplyOrderDetails()
    {
        $query = new DbQuery();
        $query->select('sod.id_supply_order_detail as id, sod.id_product,
						sod.id_product_attribute,
					    sod.name as product_name, supplier_reference');
        $query->from('supply_order_detail', 'sod');
        $query->where('id_supply_order = '.(int)$this->id);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}
