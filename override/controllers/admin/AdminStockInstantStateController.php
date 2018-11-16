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
class AdminStockInstantStateController extends AdminStockInstantStateControllerCore
{
 
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
		
        AdminController::__construct();
// Иванов 
		$this->id_shop = (int)Context::getContext()->shop->id;	
		$this->stock_instant_state_warehouses = Warehouse::getWarehouses(false,$this->id_shop);
		$this->stock_instant_state_manufacturers = Manufacturer::getMyManufacturers();
		$this->stock_instant_state_categories = Category::getHomeCategories(1);
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
        array_unshift($this->stock_instant_state_categories, array('id_category' => -1, 'name' => 'Все категории'));

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
            AdminController::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

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
            AdminController::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
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

            AdminController::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

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
		
//		$tek_prod_id = (int)Tools::getValue('id_tek_product');
		$tek_prod_id = 0;
		if ($tek_prod_id > 0) {
			$prod_filter = 'p.id_product = '.$tek_prod_id;
		} else {
			$prod_filter = '';
		}
			
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
		
		$ctg = $this->getCurrentCoverageCategories();
        if ($ctg != -1) {
 			if ($ctg != 1000) {
				$this->_where .= ' AND p.id_category_default = '.$ctg;
				self::$currentIndex .= '&id_category_default='.(int)$ctg;
			} else {
				$this->_where .= ' AND p.id_category_default = 0';
				self::$currentIndex .= '&id_category_default=0';
			}	
 		}
		
		$zak_price = (int)Tools::getValue('zak_price');
        // toolbar btn
        $this->toolbar_btn = array();
        // disables link
        $this->list_no_link = true;
				// Считаем сумму свободного товара
                $query = new DbQuery();
				if ($zak_price == 1) {
					$query->select('SUM(p.price * (100 - m.whole_disc) / 100 * IF(sa.quantity < 0 , 0 , sa.quantity)) as summa_rest');
					$query->leftjoin('manufacturer', 'm',  'm.id_manufacturer = p.id_manufacturer');
				} else {
					$query->select('SUM(p.price * IF(sa.quantity < 0 , 0 , sa.quantity)) as summa_rest');
				}
                $query->from('product','p');
		        $query->leftjoin('stock_available', 'sa',  'sa.id_product = p.id_product and sa.quantity > 0 and sa.id_shop = '.$this->id_shop);
				$query->where('p.active = 1');
				$query->where($prod_filter);
 		        if ($mnf != -1) {
 					if ($mnf == 1000) {
						$query->where('p.id_manufacturer = 0');
					} else {
						$query->where('p.id_manufacturer = '.(int)$mnf);
					}	
 				}
				if ($ctg != -1) {
					$query->where ('p.id_category_default = '.(int)$ctg);
				}
				
                $res12 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
				
				// Считаем сумму товаров всего на складе
                $query = new DbQuery();
				$query->from('stock','s');
		        $query->rightjoin('product', 'p',  'p.id_product = s.id_product and p.active = 1');
				if ($zak_price == 1) {
					$query->select('SUM(p.price * (100 - m.whole_disc) / 100 * s.usable_quantity) as summa_usable');
					$query->select('SUM(p.price * (100 - m.whole_disc) / 100 * s.physical_quantity) as summa_physical');
					$query->leftjoin('manufacturer', 'm',  'm.id_manufacturer = p.id_manufacturer');
				} else {
					$query->select('SUM(p.price * s.usable_quantity) as summa_usable');
					$query->select('SUM(p.price * s.physical_quantity) as summa_physical');
				}
 				$query->where($prod_filter);
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
				if ($ctg != -1) {
					$query->where ('p.id_category_default = '.(int)$ctg);
				}
				
                $res13 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

				// Считаем сумму товаров в брони
                $query = new DbQuery();
				$query->from('order_detail','od');
		        $query->rightjoin('product', 'p',  'p.id_product = od.product_id and p.active = 1');
		        $query->rightjoin('orders', 'o',  'o.id_order = od.id_order and o.current_state > 0');
		        $query->rightjoin('order_state', 'os',  'os.id_order_state = o.current_state and os.shipped = 0');
                $query->select('SUM(p.price * od.product_quantity) as summa_bron');
 				if ($zak_price == 1) {
					$query->select('SUM(p.price * (100 - m.whole_disc) / 100 * od.product_quantity) as summa_bron');
					$query->leftjoin('manufacturer', 'm',  'm.id_manufacturer = p.id_manufacturer');
				} else {
					$query->select('SUM(p.price * od.product_quantity) as summa_bron');
				}
				$query->where($prod_filter);
				if ($this->getCurrentCoverageWarehouse() != -1) {
                    $query->where('id_warehouse = '.(int)$this->getCurrentCoverageWarehouse());
				} else {
					$query->where('id_warehouse IN ('.$this->wh_list.')');
				}
				$query->where('od.product_quantity >0 ');


		        if ($mnf != -1) {
					if ($mnf == 1000) {
						$query->where('p.id_manufacturer = 0');
					} else {
						$query->where('p.id_manufacturer = '.(int)$mnf);
					}	
				}
				if ($ctg != -1) {
					$query->where ('p.id_category_default = '.(int)$ctg);
				}
				
                $res14 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);


        // smarty
        $this->tpl_list_vars['stock_instant_state_warehouses'] = $this->stock_instant_state_warehouses;
        $this->tpl_list_vars['stock_instant_state_manufacturers'] = $this->stock_instant_state_manufacturers;
        $this->tpl_list_vars['stock_instant_state_categories'] = $this->stock_instant_state_categories;
        $this->tpl_list_vars['stock_instant_state_cur_warehouse'] = $this->getCurrentCoverageWarehouse();
        $this->tpl_list_vars['stock_instant_state_cur_manufacturer'] = $mnf;
        $this->tpl_list_vars['stock_instant_state_cur_category'] = $ctg;
        $this->tpl_list_vars['summa_physical'] = $res13['summa_physical'];
        $this->tpl_list_vars['summa_usable'] = $res13['summa_usable'];
        $this->tpl_list_vars['summa_rest'] = $res12['summa_rest'];
        $this->tpl_list_vars['summa_bron'] = $res14['summa_bron'];
		$this->tpl_list_vars['summa_razn'] = $res13['summa_usable']-$res12['summa_rest']-$res14['summa_bron'];
		$this->tpl_list_vars['zak_price'] = $zak_price;
/* 		if ($zak_price == 1) {
			$this->tpl_list_vars['summa_razn'] = 1;
			$this->tpl_list_vars['zak_price'] = 1;
		} else {
			$this->tpl_list_vars['summa_razn'] = 2;
			$this->tpl_list_vars['zak_price'] = 0;
		}
 */		
        // adds ajax params
        $this->ajax_params = array('id_warehouse' => $this->getCurrentCoverageWarehouse());

        // displays help information
 //       $this->displayInformation($this->l('This interface allows you to display detailed information about your stock per warehouse.'));

        // sets toolbar
        $this->initToolbar();

        $list = AdminController::renderList();

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

 
}
