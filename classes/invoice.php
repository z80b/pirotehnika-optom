<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
include_once(_PS_TOOL_DIR_.'tcpdf/tcpdf.php');

class MYPDF extends TCPDF
{
	/**
	 * Сумма прописью
	 * @author runcore
	 */
	public function num2str($inn, $stripkop=false) {
	    $nol = 'ноль';
    	$str[100]= array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот', 'восемьсот','девятьсот');
	    $str[11] = array('','десять','одиннадцать','двенадцать','тринадцать', 'четырнадцать','пятнадцать','шестнадцать','семнадцать', 'восемнадцать','девятнадцать','двадцать');
	    $str[10] = array('','десять','двадцать','тридцать','сорок','пятьдесят', 'шестьдесят','семьдесят','восемьдесят','девяносто');
    	$sex = array(
	        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),// m
    	    array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять') // f
	    );
    	$forms = array(
        	array('копейка', 'копейки', 'копеек', 1), // 10^-2
	        array('рубль', 'рубля', 'рублей',  0), // 10^ 0
	        array('тысяча', 'тысячи', 'тысяч', 1), // 10^ 3
	        array('миллион', 'миллиона', 'миллионов',  0), // 10^ 6
	        array('миллиард', 'миллиарда', 'миллиардов',  0), // 10^ 9
	        array('триллион', 'триллиона', 'триллионов',  0), // 10^12
    	);

	    $out = $tmp = array();
	    $tmp = explode('.', str_replace(',','.', $inn));
	    $rub = number_format($tmp[ 0], 0,'','-');
    	if ($rub== 0) $out[] = $nol;
	    // нормализация копеек
	    $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0,2) : '00';
	    $segments = explode('-', $rub);
    	$offset = sizeof($segments);
	    if ((int)$rub== 0) { // если 0 рублей
	        $o[] = $nol;
	        $o[] = $this->morph( 0, $forms[1][ 0],$forms[1][1],$forms[1][2]);
    	}
	    else {
	        foreach ($segments as $k=>$lev) {
	            $sexi= (int) $forms[$offset][3]; // определяем род
            $ri = (int) $lev; // текущий сегмент
            if ($ri== 0 && $offset>1) {// если сегмент==0 & не последний уровень(там Units)
                $offset--;
                continue;
            }
            // нормализация
            $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);
            // получаем циферки для анализа
            $r1 = (int)substr($ri, 0,1); //первая цифра
            $r2 = (int)substr($ri,1,1); //вторая
            $r3 = (int)substr($ri,2,1); //третья
            $r22= (int)$r2.$r3; //вторая и третья
            // разгребаем порядки
            if ($ri>99) $o[] = $str[100][$r1]; // Сотни
            if ($r22>20) {// >20
                $o[] = $str[10][$r2];
                $o[] = $sex[ $sexi ][$r3];
            }
            else { // <=20
                if ($r22>9) $o[] = $str[11][$r22-9]; // 10-20
                elseif($r22> 0) $o[] = $sex[ $sexi ][$r3]; // 1-9
            }
            // Рубли
            $o[] = $this->morph($ri, $forms[$offset][ 0],$forms[$offset][1],$forms[$offset][2]);
            $offset--;
	        }
	    }

	    // Копейки
	    if (!$stripkop) {
	        $o[] = $kop;
	        $o[] = $this->morph($kop,$forms[ 0][ 0],$forms[ 0][1],$forms[ 0][2]);
    	}

	    return preg_replace("/\s{2,}/",' ',implode(' ',$o));
	}

	/**
	 * Склоняем словоформу
	 */
	public function morph($n, $f1, $f2, $f5) {
	    $n = abs($n) % 100;
	    $n1= $n % 10;
	    if ($n>10 && $n<20) return $f5;
	    if ($n1>1 && $n1<5) return $f2;
	    if ($n1==1) return $f1;
		return $f5;
	}

	public function russian_date($month)
	{
		switch ($month)
		{
			case 1: $m='января'; break;
			case 2: $m='февраля'; break;
			case 3: $m='марта'; break;
			case 4: $m='апреля'; break;
			case 5: $m='мая'; break;
			case 6: $m='июня'; break;
			case 7: $m='июля'; break;
			case 8: $m='августа'; break;
			case 9: $m='сентября'; break;
			case 10: $m='октября'; break;
			case 11: $m='ноября'; break;
			case 12: $m='декабря'; break;
		}
		return $m;
	}
}

class SPDF extends MYPDF
{
	public static function invoice($id_order, $mode = 'I')
	{
		global $cookie;

		$order = new Order((int)$id_order);
		$customer = new Customer((int)$order->id_customer);
		$address_invoice = new Address($order->id_address_invoice);
		$address_delivery = new Address($order->id_address_delivery);
		$result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'exreg` WHERE `id_customer` = '. $order->id_customer);

		$pdf = new SPDF('P', 'mm', 'A4', true, 'UTF-8', false); 
		$pdf->SetTitle('Счет на оплату №'.sprintf('%06d', $order->id));
		$pdf->SetDrawColor(150);
		$pdf->SetLineWidth(0.3);

		// настройки документа
		$preferences = array(
			'HideToolbar' => false,
			'HideMenubar' => true,
			'HideWindowUI' => true,
			'FitWindow' => true,
			'CenterWindow' => true,
			'DisplayDocTitle' => true,
			'NonFullScreenPageMode' => 'UseNone', // UseNone, UseOutlines, UseThumbs, UseOC
			'ViewArea' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
			'ViewClip' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
			'PrintArea' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
			'PrintClip' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
			'PrintScaling' => 'AppDefault', // None, AppDefault
			'Duplex' => 'DuplexFlipLongEdge', // Simplex, DuplexFlipShortEdge, DuplexFlipLongEdge
			'PickTrayByPDFSize' => true,
			'PrintPageRange' => array(1),
			'NumCopies' => 1
		);
		// выводим с настройками
		$pdf->setViewerPreferences($preferences);

		// убираем шапку и футер документа
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false); 
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->SetMargins(15, 10, 15); // отступы (слева, сверху, справа)

		// set font
		$pdf->SetFont('dejavusanscondensed');
		$pdf->AddPage();
		$pdf->Ln(5);

		if (Configuration::get('INVOICEPAYRU_SHOWL'))
		{
		if (version_compare(_PS_VERSION_,'1.5','<'))
		{
			if (file_exists(_PS_IMG_DIR_.'/logo_invoice.jpg'))
				$pdf->Image(_PS_IMG_DIR_.'/logo_invoice.jpg', 15, 8, 0, 15);
			elseif (file_exists(_PS_IMG_DIR_.'/logo.jpg'))
				$pdf->Image(_PS_IMG_DIR_.'/logo.jpg', 15, 8, 0, 15);
				$pdf->Ln(10);
		}
		else
		{
			$id_shop = Shop::getContextShopID();

			if (Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop)))
				$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop);
			elseif (Configuration::get('PS_LOGO', null, null, $id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop)))
				$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop);
				
			$pdf->Image($logo, 15, 8, 0, 15);
			$pdf->Ln(10);
		}
		}

		$pdf->SetFontSize(9);
		$pdf->Write(6, Configuration::get('INVOICEPAYRU_S'), '', false, 'L', 1);
		$pdf->Write(6, 'Юр/адрес: '.Configuration::get('INVOICEPAYRU_A'), '', false, 'L', 1);
		$pdf->Write(6, 'Факт/адрес: '.Configuration::get('INVOICEPAYRU_FA'), '', false, 'L', 1);


		$pdf->Ln(6);
		$pdf->setCellPaddings(2, '', 2, '');
		$pdf->MultiCell(50, 6, 'ИНН '.Configuration::get('INVOICEPAYRU_I'), 1, 'L', 0, 0, '', '', true, 0, false, true, 6, 'M');
		$pdf->MultiCell(50, 6, 'КПП '.Configuration::get('INVOICEPAYRU_K'), 1, 'L', 0, 1, '', '', true, 0, false, true, 6, 'M');
		$pdf->MultiCell(100, 14, "Получатель\n\n".Configuration::get('INVOICEPAYRU_S'), 1, 'L', 0, 1, '', '', true, 0, false, true, 14, 'M', true);
		$pdf->MultiCell(100, 14, "Банк получателя\n\n".Configuration::get('INVOICEPAYRU_AB'), 1, 'L', 0, 1, '', '', true, 0, false, true, 14, 'M', true);

		$pdf->MultiCell(20, 20, 'Сч.№', 1, 'C', 0, 0, 115, $pdf->GetY()-34, true, 0, false, true, 18, 'B');
		$pdf->MultiCell(55, 20, Configuration::get('INVOICEPAYRU_C'), 1, 'L', 0, 1, '', '', true, 0, false, true, 18, 'B');

		$pdf->MultiCell(20, 7, 'БИК', 1, 'C', 0, 0, 115, $pdf->GetY(), true, 0, false, true, 7, 'M');
		$pdf->MultiCell(55, 7, Configuration::get('INVOICEPAYRU_B'), 'LTR', 'L', 0, 0, '', '', true, 0, false, true, 7, 'M');

		$pdf->MultiCell(20, 7, 'Сч.№', 1, 'C', 0, 0, 115, $pdf->GetY()+7, true, 0, false, true, 7, 'M');
		$pdf->MultiCell(55, 7, Configuration::get('INVOICEPAYRU_CB'), 'LRB', 'L', 0, 0, '', '', true, 0, false, true, 7, 'M');


		$pdf->Ln(18);
		$pdf->SetFontSize(14);
		$pdf->Write(0, 'СЧЕТ №'.$order->id.' '.'от '.date('d').' '.$pdf->russian_date(date('n')).' '.date('Y').' г.', '', false, 'C');

		$pdf->Ln(10);
		$pdf->SetFontSize(8);

		/*
		$pdf->MultiCell(200, 0, 'Плательщик: '. AddressFormat::generateAddress($address_invoice, array('avoid' => array()), ', '), 0, 'L', 0, 1);
		$pdf->MultiCell(200, 0, 'Грузополучатель: '. AddressFormat::generateAddress($address_delivery, array('avoid' => array()), ', '), 0, 'L', 0, 0);
		*/
		$pdf->MultiCell(35, 0, 'Покупатель: ', 0, 'L', false, 0);
		$pdf->MultiCell(140, 0, ($result['org_name'] ? $result['org_name'] : $address_invoice->company).', ИНН '.$result['inn'].'/'.$result['kpp'].', ' . ($result['org_ur_addr'] ? $result['org_ur_addr'] : ' '.$address_invoice->city.', '.$address_invoice->address1), 0, 'L', 0, 1);
		$pdf->Ln(2);

		$pdf->MultiCell(35, 0, 'Грузополучатель: ', 0, 'L', false, 0);
		$pdf->MultiCell(140, 0, ($result['org_name'] ? $result['org_name'] : $address_delivery->company).', ' . ($result['org_post_addr'] ? $result['org_post_addr'] : ' '.$address_delivery->city.', '.$address_delivery->address1), 0, 'L', 0, 0);
		

		$pdf->Ln(10);
		$pdf->SetFontSize(7);
		$pdf->MultiCell(9, 9, '№', 1, 'C', 0, 0, '', '', true, 0, false, true, 9, 'M');
		$pdf->MultiCell(80, 9, 'Наименование товара', 1, 'C', 0, 0, '', '', true, 0, false, true, 9, 'M');
		$pdf->MultiCell(18, 9, 'Единица', 1, 'C', 0, 0, '', '', true, 0, false, true, 9, 'M');
		$pdf->MultiCell(18, 9, 'Количество', 1, 'C', 0, 0, '', '', true, 0, false, true, 9, 'M');
		$pdf->MultiCell(25, 9, 'Цена', 1, 'C', 0, 0, '', '', true, 0, false, true, 9, 'M');
		$pdf->MultiCell(25, 9, 'Сумма', 1, 'C', 0, 1, '', '', true, 0, false, true, 9, 'M');


		if (isset($order->products) AND sizeof($order->products))
			$products = $order->products;
		else
			$products = $order->getProducts();

		$customizedDatas = Product::getAllCustomizedDatas((int)($order->id_cart));
		Product::addCustomizationPrice($products, $customizedDatas);

		$pdf->SetFontSize(8);
		$i = 1;
		foreach($products AS $product)
		{
			$pdf->lasth = 0;
			$pdf->MultiCell(80, $pdf->lasth, $product['product_name'], 1, 'L', 0, 0, 24, $pdf->GetY(), false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(9, $pdf->lasth, $i++, 1, 'C', 0, 0, 15, $pdf->GetY(), false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(18, $pdf->lasth, 'шт.', 1, 'C', 0, 0, 104, $pdf->GetY(), false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(18, $pdf->lasth, (int)($product['product_quantity']), 1, 'R', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(25, $pdf->lasth, number_format($product['product_price_wt'], 2, '.', ''), 1, 'R', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(25, $pdf->lasth, number_format(($product['product_price_wt'] * $product['product_quantity']), 2, '.', ''), 1, 'R', 0, 1, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);

			if (Configuration::get('INVOICEPAYRU_CALLNDS'))
			{
				$totalWithoutTax += ($product['product_price'] * $product['product_quantity']);
				$totalWithTax += (((($product['product_price'] * $product['product_quantity']) / 100) * 18) + ($product['product_price'] * $product['product_quantity']));
				$totalTax += $product['total_wt'] - $product['total_price'];
			}
			else
			{
				$totalWithTax += $product['total_wt'];
				$totalWithoutTax += $product['total_price'];
				$totalTax += $product['total_wt'] - $product['total_price'];
			}


			if ($pdf->GetY() > 260)
				$pdf->AddPage();
		}


		if (Configuration::get('INVOICEPAYRU_ADDDELIVERY'))
		{
			$totalWithoutTax = $totalWithoutTax + $order->total_shipping;
			$totalWithTax = $totalWithTax + $order->total_shipping;
		}

		$discounts = $order->getDiscounts();
		if ($discounts)
		{
			$totalWithoutTax = $totalWithoutTax - $order->total_discounts;
			$totalWithTax = $totalWithTax - $order->total_discounts;
		
			foreach($discounts as $discount)
			{
				$pdf->MultiCell(150, 4, 'Скидка ('. $discount['name'] .'):', 0, 'R', 0, 0);
				$pdf->MultiCell(25, 4, number_format($discount['value'], 2, '.', ''), 1, 'R', 0, 1);
			}
		}

		if (Configuration::get('INVOICEPAYRU_ADDDELIVERY'))
		{
			$pdf->MultiCell(150, 4, 'Доставка:', 0, 'R', 0, 0);
			$pdf->MultiCell(25, 4, number_format($order->total_shipping, 2, '.', ''), 1, 'R', 0, 1);
		}

		$pdf->MultiCell(150, 4, 'Итого:', 0, 'R', 0, 0);
		//$pdf->MultiCell(25, 4, number_format($totalWithoutTax, 2, '.', ''), 1, 'R', 0, 1);
		$pdf->MultiCell(25, 4, number_format($totalWithTax, 2, '.', ''), 1, 'R', 0, 1);

		$pdf->MultiCell(150, 4, 'Итого НДС:', 0, 'R', 0, 0);
		
		
		$pdf->MultiCell(25, 4, number_format($order->total_paid_tax_incl - $order->total_paid_tax_excl, 2, '.', ''), 1, 'R', 0, 1);
		$pdf->MultiCell(150, 4, 'Всего к оплате:', 0, 'R', 0, 0);
		$pdf->MultiCell(25, 4, number_format($totalWithTax, 2, '.', ''), 1, 'R', 0, 1);


		$pdf->Ln(10);
		$pdf->SetFontSize(8);
		$pdf->MultiCell(150, 0, 'Всего наименований '.--$i.', на сумму '.number_format($totalWithTax, 2, '.', ''), 0, 'L', 0, 1);

		$totalstr = $pdf->num2str(number_format($totalWithTax, 2, '.', ''));
		$pdf->MultiCell(150, 0, mb_strtoupper(mb_substr($totalstr, 0, 1, 'utf-8'), 'utf-8').mb_substr($totalstr, 1, mb_strlen($totalstr, 'utf-8'), 'utf-8'), 0, 'L', 0, 1);

		$pdf->Ln(6);
		$pdf->MultiCell(120, 5, 'Руководитель предприятия ________________________________________ (ф.и.о)', 0, 'L', 0, 0);

		$pdf->Ln(9);
		$pdf->MultiCell(130, 5, 'Главный бухгалтер ________________________________________ (ф.и.о)', 0, 'L', 0, 0);





		if (Configuration::get('INVOICEPAYRU_TEXT'))
		{
			$pdf->Ln(9);
			$pdf->SetFontSize(6);
			$pdf->Write(0, Configuration::get('INVOICEPAYRU_TEXT'), 0, 0, 'L');
		}





		return $pdf->Output(Configuration::get('INVOICEPAYRU_PREFIX').sprintf('%06d', $order->id).'.pdf', $mode);
	}

}

if (Tools::getValue('id_order'))
	return SPDF::invoice(Tools::getValue('id_order'));