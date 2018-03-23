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
 * @property OrderMessage $object
 */
class AdminOrderStateGroupControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order_state_group';
        $this->className = 'OrderStateGroup';

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->context = Context::getContext();


        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
		$this->_select = '
		a.id_order_state_group,
		a.group_name,
		a.id_shop';

		$this->_where = 'AND a.`id_shop` = '.(int)$this->context->shop->id;
        $this->_use_found_rows = true;

        $this->fields_list = array(
            'id_order_state_group' => array(
                'title' => $this->l('ID'),
                'align' => 'center'
            ),
            'group_name' => array(
                'title' => $this->l('Name')
            )
        );

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Order States Groups'),
                'icon' => 'icon-mail'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'group_name',
                    'size' => 53,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $_POST['id_shop'] = (int)$this->context->shop->id;
        }    
        return parent::postProcess();
	}

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_order_state_group'] = array(
                'href' => self::$currentIndex.'&addorder_state_group&token='.$this->token,
                'desc' => $this->l('Add new order state group'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }
}
