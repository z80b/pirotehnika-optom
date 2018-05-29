<?php
/**
* Module is prohibited to sales! Violation of this condition leads to the deprivation of the license!
*
* @category  Front Office Features
* @package   Yandex Payment Solution
* @author    Yandex.Money <cms@yamoney.ru>
* @copyright © 2015 NBCO Yandex.Money LLC
* @license   https://money.yandex.ru/doc.xml?id=527052
*/
class AdminOrdersController extends AdminOrdersControllerCore
{

    public $full_paid;

    public function __construct() {

        parent::__construct();

        $new_fields_list = array();
        foreach ($this->fields_list as $name => $field) {
            $new_fields_list[$name] = $field;
            if ($name == 'total_paid_tax_incl') {
                $new_fields_list['full_paid'] = array(
                    'title'    => $this->l('Оплачено'),
                    'type'     => 'bool',
                    'align'    => 'text-center',
                    'callback' => 'printPaidIcon',
                );
            }
        }
        $this->fields_list = $new_fields_list;
    }

    private function sendJSON($array) {
        header('Content-Type: application/json');
        die(Tools::jsonEncode($array));
    }

    public function setMedia() {
        parent::setMedia();

        $this->addJs(_PS_OVERRIDE_DIR_.'/js/admin/order-full-paid.js');
        $this->addCSS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/css/full-paid-popup.css');
    }
    /*
    * module: yamodule
    * date: 2017-11-13 15:29:53
    * version: 1.4.5
    */
    public function printPDFIcons($id, $tr)
    {
        $order = new Order($id);
        $return_btn = ($order->module == 'yamodule')?'<a class="btn btn-default _blank" href="'
            .$this->context->link->getAdminLink('AdminOrders')
            .'&id_order='.$id.'&viewReturns"><i class="icon-gift"></i> Возвраты</a> ':'';
        return  $return_btn. parent::printPDFIcons($id, $tr);
    }
    /*
    * module: yamodule
    * date: 2017-11-13 15:29:53
    * version: 1.4.5
    */
    public function renderList()
    {
        if (Tools::isSubmit('viewReturns')) {
            $id_order = Tools::getValue('id_order', 0);
            if ($id_order) {
                $module = new Yamodule();
                $params = array('order' => new Order($id_order));
                $this->content .= $module->displayReturnsContentTabs($params);
                $this->content .= $module->displayReturnsContent($params);
            } else {
                $this->errors[] = $this->l('There is no order number!');
            }
        } else {
            return parent::renderList();
        }
    }

    public function printPaidIcon($value, $order) {
        return sprintf(
            '<a id="full-paid-%d" class="%s js-change-paid-val" href="/%s"><i class="%s"></i></a>',
            (int)$order['id_order'],
            'list-action-enable ' . ($value ? 'action-enabled' : 'action-disabled'),
            Tools::safeOutput($this->admin_webpath . '/?tab=AdminOrders&id_order=' . (int)$order['id_order'] . '&action=changePaidVal&token=' . Tools::getAdminTokenLite('AdminOrders')),
            $value ? 'icon-check' : 'icon-remove'
        );
    }

    public function processChangePaidVal() {
        $order = new Order(Tools::getValue('id_order'));
        //die('<pre>'.print_r($order, true).'</pre>');
        if (!Validate::isLoadedObject($order)) {
            $this->errors[] = Tools::displayError('An error occurred while updating order information.');
            die(Tools::jsonEncode(array('hasErrors' => true, 'errors' => $this->errors)));
        }

        if ($order->full_paid == 0) {

            $order->full_paid_reason = Tools::getValue('full_paid_reason');
            $order->full_paid_date   = Tools::getValue('full_paid_date');
            $op = Tools::getValue('op', 'cancel');
            //$order->total_paid_real = Tools::getValue('total_paid_tax_incl', $order->total_paid_real);
            $order->full_paid = 1;

            //die('<pre>'.print_r($order, true).'</pre>');
            if ($op == 'update') {

                $order->total_paid_real = Tools::getValue('total_paid_tax_incl', $order->total_paid_real);
                
                if (!$order->update()) {
                    $this->errors[] = Tools::displayError('An error occurred while updating order information.');
                    $this->sendJSON(array(
                        'hasErrors' => true,
                        'errors' => $this->errors,
                    ));
                }
                $this->sendJSON(array(
                    'hasErrors' => false,
                    'id_order' => $order->id,
                    'full_paid' => $order->full_paid,
                    'total_paid_real' => $order->total_paid_real,
                    'event' => 'full_paid:changed',
                ));

            } elseif ($order->total_paid_tax_incl > $order->total_paid_real) {

                if ($order->full_paid_reason && $order->full_paid_date) {
                    $order->total_paid_real = Tools::getValue('total_paid_tax_incl', $order->total_paid_real);
                    if (!$order->update()) {
                        $this->errors[] = Tools::displayError('An error occurred while updating order information.');
                        $this->sendJSON(array(
                            'hasErrors' => true,
                            'errors' => $this->errors,
                        ));
                    }
                    $this->sendJSON(array(
                        'hasErrors' => false,
                        'id_order' => $order->id,
                        'full_paid' => $order->full_paid,
                        'total_paid_real' => $order->total_paid_real,
                        'event' => 'full_paid:changed',
                    ));
                }

                $this->sendJSON(array(
                    'hasErrors' => false,
                    'full_paid' => $order->full_paid,
                    'event' => 'full_paid_reason:required',
                    'total_paid_tax_incl' => round($order->total_paid_tax_incl, 2),
                ));

            } else {

                if ($order->full_paid_date) {
                    $order->total_paid_real = Tools::getValue('total_paid_tax_incl', $order->total_paid_real);
                    if (!$order->update()) {
                        $this->errors[] = Tools::displayError('An error occurred while updating order information.');
                        $this->sendJSON(array(
                            'hasErrors' => true,
                            'errors' => $this->errors,
                        ));
                    }

                    $this->sendJSON(array(
                        'hasErrors' => false,
                        'id_order' => $order->id,
                        'full_paid' => $order->full_paid,
                        'total_paid_real' => $order->total_paid_real,
                        'event' => 'full_paid:changed',
                    ));
                }

                $this->sendJSON(array(
                    'hasErrors' => false,
                    'full_paid' => $order->full_paid,
                    'event' => 'full_paid_date:required',
                    'total_paid_tax_incl' => round($order->total_paid_tax_incl, 2),
                ));
            }
        } else {

            $order->full_paid = 0;

            if (!$order->update()) {
                $this->errors[] = Tools::displayError('An error occurred while updating order information.');
                $this->sendJSON(array(
                    'hasErrors' => true,
                    'errors' => $this->errors,
                ));
            }

            $this->sendJSON(array(
                'hasErrors' => false,
                'full_paid' => $order->full_paid,
            ));
        }
    }
}