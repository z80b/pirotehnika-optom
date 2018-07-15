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

class MyCatalogControllerCore extends FrontController
{
    public $php_self = 'mycatalog';

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'global.css');
        $this->addCSS(_THEME_CSS_DIR_.'product_list.css');
        $this->addJS(_THEME_JS_DIR_.'category-filter.js');
    }

    public function initContent() {
        parent::initContent();
        $this->productSort();

        $this->categories = Category::getCategoriesList($this->context->language->id);

        $this->productsCount = Category::getProductsList($this->context->language->id, null, null, true);
        $this->pagination($this->productsCount);
        $this->products = Category::getProductsList($this->context->language->id, (int)$this->p - 1, (int)$this->n);
        
        $categoryRoot = new Category(Configuration::get('PS_HOME_CATEGORY'),$this->context->language->id,$this->context->shop->id);
        $categoriesHome = $categoryRoot->getSubCategories($this->context->language->id);
        $message = '';
        $this->context->smarty->assign(array(
                'path'          => 'каталог',
                'categories'    => $this->categories,
                'products'      => $this->products,
                'checked'       => Category::getCheckedCategories(),
                'subcategories' => $categoriesHome,
                'messageSmarty' => $message,
                'homeSize'      => Image::getSize('medium_default'),
                'nbProducts'          => $this->productsCount,
                'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')
        ));

        $this->setTemplate(_PS_THEME_DIR_.'blockhomecategorys.tpl');
    }

}
