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
 * @property Gender $object
 */
class AdminTrcsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'trc';
        $this->className = 'Trc';
        $this->lang = false;
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->context = Context::getContext();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );


        $this->fields_list = array(
            'id_trc' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => 'Наименование',
                'filter_key' => 'name'
            ),
            'trc_group' => array(
                'title' => 'Группа',
            )
        );

        parent::__construct();
 		
   }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_trc'] = array(
                'href' => self::$currentIndex.'&addtrc&token='.$this->token,
                'desc' => 'Новая ТК',
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => 'Транспортные компании',
                'icon' => 'icon-male'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => 'Наименование',
                    'name' => 'name',
                    'col' => 4,
                    'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"�{}_$%:',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => 'Группа',
                    'name' => 'trc_group',
                    'col' => 1,
                    'required' => false
                ),
                array(
                    'type' => 'textarea',
                    'label' => 'Примечания',
                    'name' => 'prim',
                    'col' => 9,
                    'row' => 9,
                    'hint' => 'Адреса, телефоны, часы работы филиалов. Прочая необходимая информация.'
                ),
                array(
                    'type' => 'hidden',
                    'label' => 'Магазин',
                    'name' => 'id_shop',
                    'col' => 1,
                    'hint' => ''
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        /** @var Gender $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }


        return parent::renderForm();
    }


}
