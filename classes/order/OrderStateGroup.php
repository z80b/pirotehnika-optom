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

class OrderStateGroupCore extends ObjectModel
{
    /** @var string name name */
    public $group_name;


    /** @var string Object creation date */
    public $id_shop;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_state_group',
        'primary' => 'id_order_state_group',
        'fields' => array(
            'group_name' =>        array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 50),
            'id_shop' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );

    protected $webserviceParameters = array(
            'fields' => array(
            'name' => array('sqlId' => 'group_name')
        )
    );

    public static function getOrderStateGroups()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM '._DB_PREFIX_.'order_state_group 
		WHERE `id_shop` = '.(int)Context::getContext()->shop->id);
    }
 
	public static function getOrderPerGroupsCount()
    {
		$ordercounts = array();
		$ordercount = Db::getInstance()->getValue('
		SELECT COUNT(*)
		FROM '._DB_PREFIX_.'orders o
		WHERE o.`id_shop` = '.(int)Context::getContext()->shop->id);
		$ordercounts[] = $ordercount;
        $groups = OrderStateGroup::getOrderStateGroups();
		foreach ($groups as $group) {
			if (count($ordercounts) < 6) {
/* 				$ordercount = Db::getInstance()->getValue('
				SELECT COUNT(*)
				FROM '._DB_PREFIX_.'orders o
				RIGHT JOIN `'._DB_PREFIX_.'order_state_to_group` ostg ON (ostg.`id_order_state` = o.`current_state` AND ostg.`id_order_state_group` = '.$group['id_order_state_group'].' AND ostg.`id_shop` = '.(int)Context::getContext()->shop->id.')
				WHERE o.`id_shop` = '.(int)Context::getContext()->shop->id);
 */
				$ordercount = Db::getInstance()->getValue('
				SELECT COUNT(*)
				FROM '._DB_PREFIX_.'orders o
				RIGHT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = o.`current_state` AND os.`order_state_groups` LIKE \'%'.$group['id_order_state_group'].'%\')
				WHERE o.`id_shop` = '.(int)Context::getContext()->shop->id);
				$ordercounts[] = $ordercount;
			} else {
				$ordercounts[] = 0;
				break;
			}	
		}
		return $ordercounts;
    }
}
