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
 * @property Stock $object
 */
class AdminStockInstantStateControllerCore extends AdminController
{
    protected $stock_instant_state_warehouses = array();
    protected $stock_instant_state_manufacturers = array();
    protected $stock_instant_state_categories = array();
	protected $id_shop;
    protected $whs_id = array();
	protected $wh_list;

    public function __construct()
    {

		$this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'stock';
        $this->list_id = 'stock';
        $this->className = 'Stock';
        $this->tpl_list_vars['show_filter'] = true;
        $this->lang = false;
        $this->multishop_context = Shop::CONTEXT_ALL;

        $this->fields_list = array(
            'id_product' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'reference' => array(
                'title' => $this->l('Reference'),
                'align' => 'center',
                'havingFilter' => true
            ),
            'ean13' => array(
                'title' => $this->l('EAN13'),
                'align' => 'center',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'havingFilter' => true
            ),
            'price_te' => array(
                'title' => $this->l('Price (tax excl.)'),
                'orderby' => true,
                'search' => false,
                'type' => 'price',
                'currency' => true,
            ),
            'valuation' => array(
                'title' => $this->l('Valuation'),
                'orderby' => false,
                'search' => false,
                'type' => 'price',
                'currency' => true,
                'hint' => $this->l('Total value of the physical quantity. The sum (for all prices) is not available for all warehouses, please filter by warehouse.')
            ),
            'physical_quantity' => array(
                'title' => $this->l('Physical quantity'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'orderby' => true,
                'search' => false
            ),
            'usable_quantity' => array(
                'title' => $this->l('Usable quantity'),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'orderby' => true,
                'search' => false,
            ),
        );

        $this->addRowAction('details');
		
        parent::__construct();
// Иванов 
		$this->id_shop = (int)Context::getContext()->shop->id;	
		$this->stock_instant_state_warehouses = Warehouse::getWarehouses(false,$this->id_shop);
		$this->stock_instant_state_manufacturers = Manufacturer::getMyManufacturers();
		$this->stock_instant_state_categories = Manufacturer::getMyManufacturers();
		foreach ($this->stock_instant_state_warehouses as $items) {
			 foreach ($items as $key => $value)
				{
					if ($key == 'id_warehouse') {
					$this->whs_id[] = $value;
					}
				}
		}
		$this->wh_list = implode(",",$this->whs_id);
// Иванов ^		
        array_unshift($this->stock_instant_state_warehouses, array('id_warehouse' => -1, 'name' => $this->l('All Warehouses')));
        array_unshift($this->stock_instant_state_manufacturers, array('id_manufacturer' => 1000, 'name' => 'Не указан'));
        array_unshift($this->stock_instant_state_manufacturers, array('id_manufacturer' => -1, 'name' => 'Все производители'));
        array_unshift($this->stock_instant_state_categories, array('id_def_category' => -1, 'name' => 'Все категории'));

		}

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->l('Instant stock status');
		$this->page_header_toolbar_btn['stock_av_sync'] = array(
			'short' => 'Пересчитать свободные остатки',
			'href' => $this->context->link->getAdminLink('AdminStockInstantState').'&stock_av_sync',
			'desc' => $this->l('Пересчитать свободные остатки'),
			'class' => 'process-icon-plus'
		);

        if ($this->display == 'details') {
            $this->page_header_toolbar_btn['back_to_list'] = array(
                'href' => Context::getContext()->link->getAdminLink('AdminStockInstantState').(Tools::getValue('id_warehouse') ? '&id_warehouse='.Tools::getValue('id_warehouse') : ''),
                'desc' => $this->l('Back to list', null, null, false),
                'icon' => 'process-icon-back'
            );
        } elseif (Tools::isSubmit('id_warehouse') && (int)Tools::getValue('id_warehouse') != -1) {
            $this->page_header_toolbar_btn['export-stock-state-quantities-csv'] = array(
                'short' => $this->l('Export this list as CSV', null, null, false),
                'href' => $this->context->link->getAdminLink('AdminStockInstantState').'&csv_quantities&id_warehouse='.(int)$this->getCurrentCoverageWarehouse(),
                'desc' => $this->l('Export Quantities (CSV)', null, null, false),
                'class' => 'process-icon-export'
            );

            $this->page_header_toolbar_btn['export-stock-state-prices-csv'] = array(
                'short' => $this->l('Export this list as CSV', null, null, false),
                'href' => $this->context->link->getAdminLink('AdminStockInstantState').'&csv_prices&id_warehouse='.(int)$this->getCurrentCoverageWarehouse(),
                'desc' => $this->l('Export Prices (CSV)', null, null, false),
                'class' => 'process-icon-export'
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * AdminController::renderList() override
     * @see AdminController::renderList()
     */
    public function renderList()
    {

        $this->fields_list['real_quantity'] = array(
            'title' => $this->l('Real quantity'),
            'class' => 'fixed-width-xs',
            'align' => 'center',
            'orderby' => false,
            'search' => false,
            'hint' => $this->l('Physical quantity (usable) - Client orders + Supply Orders'),
        );

        // query
        $this->_select = 'IFNULL(pa.ean13, p.ean13) as ean13,
            IFNULL(pa.upc, p.upc) as upc,
            IFNULL(pa.reference, p.reference) as reference,
			IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(DISTINCT agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as name,
			w.id_currency';

        $this->_join = 'INNER JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = a.id_product AND p.advanced_stock_management = 1)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'warehouse` w ON (w.id_warehouse = a.id_warehouse)';
//        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'warehouse_shop` ws ON (w.id_warehouse = ws.id_warehouse)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
			a.id_product = pl.id_product
			AND pl.id_lang = '.(int)$this->context->language->id.'
		)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.id_product_attribute = a.id_product_attribute)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product_attribute = a.id_product_attribute)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'attribute` atr ON (atr.id_attribute = pac.id_attribute)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (
			al.id_attribute = pac.id_attribute
			AND al.id_lang = '.(int)$this->context->language->id.'
		)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (
			agl.id_attribute_group = atr.id_attribute_group
			AND agl.id_lang = '.(int)$this->context->language->id.'
		)';

        $this->_group = 'GROUP BY a.id_product, a.id_product_attribute';

        $this->_orderBy = 'name';
        $this->_orderWay = 'ASC';

        if ($this->getCurrentCoverageWarehouse() != -1) {
            $this->_where .= ' AND a.id_warehouse = '.$this->getCurrentCoverageWarehouse();
            self::$currentIndex .= '&id_warehouse='.(int)$this->getCurrentCoverageWarehouse();
        } else {
			$this->_where .= ' AND a.id_warehouse IN ('.$this->wh_list.')';
		}
		
		$mnf = $this->getCurrentCoverageManufacturer();
        if ($mnf != -1) {
 			if ($mnf != 1000) {
				$this->_where .= ' AND p.id_manufacturer = '.$mnf;
				self::$currentIndex .= '&id_manufacturer='.(int)$mnf;
			} else {
				$this->_where .= ' AND p.id_manufacturer = 0';
				self::$currentIndex .= '&id_manufacturer=0';
			}	
 		}
		
		
        // toolbar btn
        $this->toolbar_btn = array();
        // disables link
        $this->list_no_link = true;
				// Считаем сумму свободного товара
                $query = new DbQuery();
                $query->select('SUM(p.price * sa.quantity) as summa_rest');
                $query->from('product','p');
		        $query->leftjoin('stock_available', 'sa',  'sa.id_product = p.id_product and sa.quantity > 0 and sa.id_shop = '.$this->id_shop);
				$query->where('p.active = 1');
 		        if ($mnf != -1) {
 					if ($mnf == 1000) {
						$query->where('p.id_manufacturer = 0');
					} else {
						$query->where('p.id_manufacturer = '.(int)$mnf);
					}	
 				}
				
                $res12 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
				
				// Считаем сумму товаров всего на складе
                $query = new DbQuery();
                $query->select('SUM(p.price * physical_quantity) as summa_physical');
                $query->select('SUM(p.price * usable_quantity) as summa_usable');
                $query->from('stock','s');
		        $query->rightjoin('product', 'p',  'p.id_product = s.id_product and p.active = 1');
 				if ($this->getCurrentCoverageWarehouse() != -1) {
                    $query->where('id_warehouse = '.(int)$this->getCurrentCoverageWarehouse());
				} else {
					$query->where('id_warehouse IN ('.$this->wh_list.')');
				}

		        if ($mnf != -1) {
					if ($mnf == 1000) {
						$query->where('p.id_manufacturer = 0');
					} else {
						$query->where('p.id_manufacturer = '.(int)$mnf);
					}	
				}
				
                $res13 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

				// Считаем сумму товаров в брони
                $query = new DbQuery();
                $query->select('SUM(p.price * od.product_quantity) as summa_bron');
                $query->from('order_detail','od');
		        $query->rightjoin('product', 'p',  'p.id_product = od.product_id and p.active = 1');
		        $query->rightjoin('orders', 'o',  'o.id_order = od.id_order');
		        $query->rightjoin('order_state', 'os',  'os.id_order_state = o.current_state and os.shipped = 0');

		        if ($mnf != -1) {
					if ($mnf == 1000) {
						$query->where('p.id_manufacturer = 0');
					} else {
						$query->where('p.id_manufacturer = '.(int)$mnf);
					}	
				}
				
                $res14 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);


        // smarty
        $this->tpl_list_vars['stock_instant_state_warehouses'] = $this->stock_instant_state_warehouses;
        $this->tpl_list_vars['stock_instant_state_manufacturers'] = $this->stock_instant_state_manufacturers;
        $this->tpl_list_vars['stock_instant_state_categories'] = $this->stock_instant_state_categories;
        $this->tpl_list_vars['stock_instant_state_cur_warehouse'] = $this->getCurrentCoverageWarehouse();
        $this->tpl_list_vars['stock_instant_state_cur_manufacturer'] = $mnf;
        $this->tpl_list_vars['stock_instant_state_cur_category'] = $mnf;
        $this->tpl_list_vars['summa_physical'] = $res13['summa_physical'];
        $this->tpl_list_vars['summa_usable'] = $res13['summa_usable'];
        $this->tpl_list_vars['summa_rest'] = $res12['summa_rest'];
        $this->tpl_list_vars['summa_bron'] = $res14['summa_bron'];
        $this->tpl_list_vars['summa_razn'] = $res13['summa_usable']-$res12['summa_rest']-$res14['summa_bron'];
        // adds ajax params
        $this->ajax_params = array('id_warehouse' => $this->getCurrentCoverageWarehouse());

        // displays help information
 //       $this->displayInformation($this->l('This interface allows you to display detailed information about your stock per warehouse.'));

        // sets toolbar
        $this->initToolbar();

        $list = parent::renderList();

        // if export requested
        if ((Tools::isSubmit('csv_quantities') || Tools::isSubmit('csv_prices')) &&
            (int)Tools::getValue('id_warehouse') != -1) {
            if (count($this->_list) > 0) {
                $this->renderCSV();
                die;
            } else {
                $this->displayWarning($this->l('There is nothing to export as CSV.'));
            }
        }

        return $list;
    }

    public function renderDetails()
    {
        if (Tools::isSubmit('id_stock')) {
            // if a product id is submit

            $this->list_no_link = true;
            $this->lang = false;
            $this->table = 'stock';
            $this->list_id = 'details';
            $this->tpl_list_vars['show_filter'] = false;
            $lang_id = (int)$this->context->language->id;
            $this->actions = array();
            $this->list_simple_header = true;
            $ids = explode('_', Tools::getValue('id_stock'));

            if (count($ids) != 2) {
                die;
            }

            $id_product = $ids[0];
            $id_product_attribute = $ids[1];
            $id_warehouse = Tools::getValue('id_warehouse', -1);
            $this->_select = 'IFNULL(pa.ean13, p.ean13) as ean13,
                IFNULL(pa.upc, p.upc) as upc,
                IFNULL(pa.reference, p.reference) as reference,
                IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(DISTINCT agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as name,
				w.id_currency, a.price_te';
            $this->_join = 'INNER JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = a.id_product AND p.advanced_stock_management = 1)';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'warehouse` AS w ON w.id_warehouse = a.id_warehouse';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
				a.id_product = pl.id_product
				AND pl.id_lang = '.(int)$this->context->language->id.'
			)';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.id_product_attribute = a.id_product_attribute)';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product_attribute = a.id_product_attribute)';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'attribute` atr ON (atr.id_attribute = pac.id_attribute)';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (
				al.id_attribute = pac.id_attribute
				AND al.id_lang = '.(int)$this->context->language->id.'
			)';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (
				agl.id_attribute_group = atr.id_attribute_group
				AND agl.id_lang = '.(int)$this->context->language->id.'
			)';
            $this->_where = 'AND a.id_product = '.(int)$id_product.' AND a.id_product_attribute = '.(int)$id_product_attribute;

            if ($id_warehouse != -1) {
                $this->_where .= ' AND a.id_warehouse = '.(int)$id_warehouse;
            } else {
				$this->_where .= ' AND a.id_warehouse IN ('.$this->wh_list.')';
			}

            $this->_orderBy = 'name';
            $this->_orderWay = 'ASC';

            $this->_group = 'GROUP BY a.price_te';

            self::$currentIndex = self::$currentIndex.'&id_stock='.Tools::getValue('id_stock').'&detailsstock';
            return parent::renderList();
        }
    }

    /**
     * AdminController::getList() override
     * @see AdminController::getList()
     *
     * @param int         $id_lang
     * @param string|null $order_by
     * @param string|null $order_way
     * @param int         $start
     * @param int|null    $limit
     * @param int|bool    $id_lang_shop
     *
     * @throws PrestaShopException
     */
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
/*    					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
					echo "<b></b><br>";
 */      
		if (Tools::isSubmit('id_stock')) {
            parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

            $nb_items = count($this->_list);

            for ($i = 0; $i < $nb_items; $i++) {
                $item = &$this->_list[$i];
                $manager = StockManagerFactory::getManager();

                // gets quantities and valuation
                $query = new DbQuery();
//                $query->select('physical_quantity');
//                $query->select('usable_quantity');
                $query->select('SUM(physical_quantity) as physical_quantity');
                $query->select('SUM(usable_quantity) as usable_quantity');
                $query->select('SUM(p.price * physical_quantity)  as valuation');
//                $query->select('p.price as price_te');
                $query->from('stock','s');
                $query->from('product','p');
		        $query->innerjoin('product', 'p',  'p.id_product = s.id_product');
                $query->where('id_stock = '.(int)$item['id_stock'].' AND s.id_product = '.(int)$item['id_product'].' AND s.id_product_attribute = '.(int)$item['id_product_attribute']);
/* 				$wh_list1 = array();
				$wh_list1[0]='3';
				$wh_list1[1]='4';
				$wh_list2 = implode(",",$wh_list1);
 */                if ($this->getCurrentCoverageWarehouse() != -1) {
                    $query->where('id_warehouse = '.(int)$this->getCurrentCoverageWarehouse());
				} else {
					$query->where('id_warehouse IN ('.$this->wh_list.')');
				}

                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

                $item['physical_quantity'] = $res['physical_quantity'];
                $item['usable_quantity'] = $res['usable_quantity'];
                $item['valuation'] = $res['valuation'];
                $item['real_quantity'] = $manager->getProductRealQuantities(
                    $item['id_product'],
                    $item['id_product_attribute'],
                    ($this->getCurrentCoverageWarehouse() == -1 ? $this->whs_id : array($this->getCurrentCoverageWarehouse())),
                    true
                );
            }
        } elseif (Tools::isSubmit('stock_av_sync')) {
            parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
            $query = new DbQuery();
            $query->select('id_product');
            $query->from('product', 'p');
            $all_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            foreach ($all_products as $one_product) {
				if (StockAvailable::dependsOnStock($one_product['id_product'])) {
					StockAvailable::synchronize($one_product['id_product']);
				}
			}
//			StockAvailable::synchronize(1857);

		} else {	
            if ((Tools::isSubmit('csv_quantities') || Tools::isSubmit('csv_prices')) &&
                (int)Tools::getValue('id_warehouse') != -1) {
                $limit = false;
            }

            $order_by_valuation = false;
            $order_by_real_quantity = false;

            if ($this->context->cookie->{$this->table.'Orderby'} == 'valuation') {
                unset($this->context->cookie->{$this->table.'Orderby'});
                $order_by_valuation = true;
            } elseif ($this->context->cookie->{$this->table.'Orderby'} == 'real_quantity') {
                unset($this->context->cookie->{$this->table.'Orderby'});
                $order_by_real_quantity = true;
            }

            parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

            $nb_items = count($this->_list);

            for ($i = 0; $i < $nb_items; ++$i) {
                $item = &$this->_list[$i];

//                $item['price_te'] = '--';
                $item[$this->identifier] = $item['id_product'].'_'.$item['id_product_attribute'];

                // gets stock manager
                $manager = StockManagerFactory::getManager();

                // gets quantities and valuation
                $query = new DbQuery();
                $query->select('SUM(s.physical_quantity) as physical_quantity');
                $query->select('SUM(s.usable_quantity) as usable_quantity');
                $query->select('SUM(p.price * s.physical_quantity) as valuation');
				$query->from('stock', 's');
		        $query->innerjoin('product', 'p',  'p.id_product = s.id_product');
// Иванов - Двоит количество
//              $query->leftJoin('warehouse_shop', 'ws', 'ws.id_warehouse = s.id_warehouse');
// Иванов ^
                $query->where('s.id_product = '.(int)$item['id_product'].' AND s.id_product_attribute = '.(int)$item['id_product_attribute']);

                if ($this->getCurrentCoverageWarehouse() != -1) {
                    $query->where('s.id_warehouse = '.(int)$this->getCurrentCoverageWarehouse());
                }else {
					$query->where('s.id_warehouse IN ('.$this->wh_list.')');
				}
/* 					echo($this->id_shop);
					echo($this->wh_list);
 */	

                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

                $item['physical_quantity'] = $res['physical_quantity'];
                $item['usable_quantity'] = $res['usable_quantity'];

                // gets real_quantity depending on the warehouse
                $item['real_quantity'] = $manager->getProductRealQuantities($item['id_product'],
                                                                            $item['id_product_attribute'],
                                                                            ($this->getCurrentCoverageWarehouse() == -1 ? $this->whs_id : array($this->getCurrentCoverageWarehouse())),
                                                                            true);

                // removes the valuation if the filter corresponds to 'all warehouses'
                if ($this->getCurrentCoverageWarehouse() == -1) {
// Иванов
//                    $item['valuation'] = $res['N/A'];
                    $item['valuation'] = $res['valuation'];
// Иванов ^
                } else {
                    $item['valuation'] = $res['valuation'];
                }
            }

            if ($this->getCurrentCoverageWarehouse() != -1 && $order_by_valuation) {
                usort($this->_list, array($this, 'valuationCmp'));
            } elseif ($order_by_real_quantity) {
                usort($this->_list, array($this, 'realQuantityCmp'));
            }
        }
    }

    /**
     * CMP
     *
     * @param array $n
     * @param array $m
     *
     * @return bool
     */
    public function valuationCmp($n, $m)
    {
        if ($this->context->cookie->{$this->table.'Orderway'} == 'desc') {
            return $n['valuation'] > $m['valuation'];
        } else {
            return $n['valuation'] < $m['valuation'];
        }
    }

    /**
     * CMP
     *
     * @param array $n
     * @param array $m
     *
     * @return bool
     */
    public function realQuantityCmp($n, $m)
    {
        if ($this->context->cookie->{$this->table.'Orderway'} == 'desc') {
            return $n['real_quantity'] > $m['real_quantity'];
        } else {
            return $n['real_quantity'] < $m['real_quantity'];
        }
    }

    /**
     * Gets the current warehouse used
     *
     * @return int id_warehouse
     */
    protected function getCurrentCoverageWarehouse()
    {
        static $warehouse = 0;

        if ($warehouse == 0) {
            $warehouse = -1; // all warehouses
            if ((int)Tools::getValue('id_warehouse')) {
                $warehouse = (int)Tools::getValue('id_warehouse');
            }
        }
        return $warehouse;
    }

    protected function getCurrentCoverageManufacturer()
    {
        static $manufacturer = 0;

        if ($manufacturer == 0) {
            $manufacturer = -1; // all warehouses
            if ((int)Tools::getValue('id_manufacturer')) {
                $manufacturer = (int)Tools::getValue('id_manufacturer');
            }
        }
        return $manufacturer;
    }

    /**
     * @see AdminController::initToolbar();
     */
    public function initToolbar()
    {
        if (Tools::isSubmit('id_warehouse') && (int)Tools::getValue('id_warehouse') != -1) {
            $this->toolbar_btn['export-stock-state-quantities-csv'] = array(
                'short' => 'Export this list as CSV',
                'href' => $this->context->link->getAdminLink('AdminStockInstantState').'&csv_quantities&id_warehouse='.(int)$this->getCurrentCoverageWarehouse(),
                'desc' => $this->l('Export Quantities (CSV)'),
                'class' => 'process-icon-export'
            );

            $this->toolbar_btn['export-stock-state-prices-csv'] = array(
                'short' => 'Export this list as CSV',
                'href' => $this->context->link->getAdminLink('AdminStockInstantState').'&csv_prices&id_warehouse='.(int)$this->getCurrentCoverageWarehouse(),
                'desc' => $this->l('Export Prices (CSV)'),
                'class' => 'process-icon-export'
            );
        }
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    /**
     * Exports CSV
     */
    public function renderCSV()
    {
        if (count($this->_list) <= 0) {
            return;
        }

        // sets warehouse id and warehouse name
        $id_warehouse = (int)Tools::getValue('id_warehouse');
        $warehouse_name = Warehouse::getWarehouseNameById($id_warehouse);

        // if quantities requested
        if (Tools::isSubmit('csv_quantities')) {
            // filename
            $filename = $this->l('stock_instant_state_quantities').'_'.$warehouse_name.'.csv';

            // header
            header('Content-type: text/csv');
            header('Cache-Control: no-store, no-cache must-revalidate');
            header('Content-disposition: attachment; filename="'.$filename);

            // puts keys
            $keys = array('id_product', 'id_product_attribute', 'reference', 'ean13', 'upc', 'name', 'physical_quantity', 'usable_quantity', 'real_quantity');
            echo sprintf("%s\n", implode(';', $keys));

            // puts rows
            foreach ($this->_list as $row) {
                $row_csv = array($row['id_product'], $row['id_product_attribute'], $row['reference'],
                                 $row['ean13'], $row['upc'], $row['name'],
                                 $row['physical_quantity'], $row['usable_quantity'], $row['real_quantity']
                );

                // puts one row
                echo sprintf("%s\n", implode(';', array_map(array('CSVCore', 'wrap'), $row_csv)));
            }
        }
        // if prices requested
        elseif (Tools::isSubmit('csv_prices')) {
            // sets filename
            $filename = $this->l('stock_instant_state_prices').'_'.$warehouse_name.'.csv';

            // header
            header('Content-type: text/csv');
            header('Cache-Control: no-store, no-cache must-revalidate');
            header('Content-disposition: attachment; filename="'.$filename);

            // puts keys
            $keys = array('id_product', 'id_product_attribute', 'reference', 'ean13', 'upc', 'name', 'price_te', 'physical_quantity', 'usable_quantity');
            echo sprintf("%s\n", implode(';', $keys));

            foreach ($this->_list as $row) {
                $id_product = (int)$row['id_product'];
                $id_product_attribute = (int)$row['id_product_attribute'];

                // gets prices
                $query = new DbQuery();
                $query->select('s.price_te, SUM(s.physical_quantity) as physical_quantity, SUM(s.usable_quantity) as usable_quantity');
                $query->from('stock', 's');
                $query->leftJoin('warehouse', 'w', 'w.id_warehouse = s.id_warehouse');
                $query->where('s.id_product = '.$id_product.' AND s.id_product_attribute = '.$id_product_attribute);
//                $query->where('s.id_warehouse = '.$id_warehouse);
                if ($this->getCurrentCoverageWarehouse() != -1) {
                    $query->where('s.id_warehouse = '.$id_warehouse);
                }else {
					$query->where('s.id_warehouse IN ('.$this->wh_list.')');
/* 					echo($this->id_shop);
					echo($this->wh_list);
 */	
				}
				
                $query->groupBy('s.price_te');
                $datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

                // puts data
                foreach ($datas as $data) {
                    $row_csv = array($row['id_product'], $row['id_product_attribute'], $row['reference'],
                                     $row['ean13'], $row['upc'], $row['name'],
                                     $data['price_te'], $data['physical_quantity'], $data['usable_quantity']);

                    // puts one row
                    echo sprintf("%s\n", implode(';', array_map(array('CSVCore', 'wrap'), $row_csv)));
                }
            }
        }
        elseif (Tools::isSubmit('csv_prices')) {
			
		}
    }

    public function initContent()
    {
        if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $this->warnings[md5('PS_ADVANCED_STOCK_MANAGEMENT')] = $this->l('You need to activate advanced stock management before using this feature.');
            return false;
        }
        parent::initContent();
    }

    public function initProcess()
    {
        if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $this->warnings[md5('PS_ADVANCED_STOCK_MANAGEMENT')] = $this->l('You need to activate advanced stock management before using this feature.');
            return false;
        }

        if (Tools::isSubmit('detailsproduct')) {
            $this->list_id = 'details';
        } else {
            $this->list_id = 'stock';
        }

        parent::initProcess();
    }
}
