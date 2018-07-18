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
class TrcCore extends ObjectModel
{
    public $id_trc;
    public $id_shop;
    public $name;
    public $trc_group;
    public $prim;
	protected $context;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'trc',
        'primary' => 'id_trc',
        'multilang' => false,
        'fields' => array(
            'id_shop' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'trc_group' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'prim' =>        array('type' => self::TYPE_STRING,  'size' => 2000),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
		$context = Context::getContext();
		$this->id_shop = (int)$context->shop->id;
		parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getTrcs($id_shop = 0)
    {
        return Db::getInstance()->executeS('
			SELECT t.`id_trc`, `name`
			FROM `'._DB_PREFIX_.'trc` t
			WHERE id_shop = '.$id_shop.'
			ORDER BY `name` ASC
		');
    }

    public static function getCustomerTrcByID($id_customer = 0)
    {
        return Db::getInstance()->getValue('
			SELECT c.`id_trc`
			FROM `'._DB_PREFIX_.'customer` c
			WHERE c.`id_customer` = '.$id_customer
		);
    }

}
