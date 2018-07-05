<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/exim/PHPExcel/Classes/PHPExcel.php';

class ProductsImportController extends FrontController
{
    public function initContent()
    {
        parent::initContent();

        $products = Product::getProducts($this->context->language->id, 0, 0, 'name', 'asc');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $active_sheet->setTitle("Выгрузка товаров");

        foreach ($products as $row => $product) {
            // Цена
            $active_sheet->setCellValueByColumnAndRow(0, $row, $product['price']);
            // Название
            $active_sheet->setCellValueByColumnAndRow(1, $row, $product['name']);
            // Ссылка
            $productLink = $this->context->link->getProductLink($product);
            $active_sheet->setCellValueByColumnAndRow(2, $row, $productLink);
            // Артикул
            $active_sheet->setCellValueByColumnAndRow(3, $row, $product['reference']);
            // Производитель
            $active_sheet->setCellValueByColumnAndRow(4, $row, $product['manufacturer_name']);
            // наличие
            $active_sheet->setCellValueByColumnAndRow(5, $row, $product['quantity']);
            // Описание
            $active_sheet->setCellValueByColumnAndRow(6, $row, $product['description_short']);
            // Ссылка на изображение
            // $imageLink = $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'], 'large_default');
            // $active_sheet->setCellValueByColumnAndRow(7, $row, $imageLink);
        }

        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename='products.xls'");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}