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
	public function num2str($inn, $stripkop=false, $striprub=false) {
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
			if (!$striprub) {
            $o[] = $this->morph($ri, $forms[$offset][ 0],$forms[$offset][1],$forms[$offset][2]);
            $offset--;
	        }}
	    }


	    // Копейки
	    if (!$stripkop) {
	        $o[] = $kop;
	        $o[] = $this->morph($kop,$forms[ 0][ 0],$forms[ 0][1],$forms[ 0][2]);
    	}

	    return preg_replace("/\s{2,}/",' ',implode(' ',$o));
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
	public function morph($n, $f1, $f2, $f5) {
	    $n = abs($n) % 100;
	    $n1= $n % 10;
	    if ($n>10 && $n<20) return $f5;
	    if ($n1>1 && $n1<5) return $f2;
	    if ($n1==1) return $f1;
		return $f5;
	}
}

class SPDF extends MYPDF
{
	public static function invoice($id_order, $mode = 'D')
	{
		global $cookie;

		$order = new Order((int)$id_order);
		$customer = new Customer((int)$order->id_customer);
		$address_invoice = new Address($order->id_address_invoice);
		$address_delivery = new Address($order->id_address_delivery);
		$result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'exreg` WHERE `id_customer` = '.$order->id_customer);

		$pdf = new SPDF('L', 'mm', 'A4', true, 'UTF-8', false); 
		$pdf->SetTitle('Товарная накладная №'.sprintf('%06d', $order->id));
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
		//$pdf->SetMargins(9, 10, 11); // отступы (слева, сверху, справа)
		$pdf->SetMargins(10, 10, 10); // отступы (слева, сверху, справа)

		// set font
		$pdf->SetFont('dejavusanscondensed');
		$pdf->AddPage();


		$pdf->SetFontSize(6);
		$pdf->Write(0, "Унифицированная форма № Торг-12\nУтверждена Постановлением Госкомстата России\nот 25.12.1998 г. за № 132", 0, 0, 'R');

		$pdf->Ln(5);
		$pdf->SetFontSize(8);

		$pdf->MultiCell(214, 5, '', 0, '', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(40, 5, 'Форма по ОКУД', 0, 'R', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(22, 5, '0330212', 1, 'C', 0, 1, '', '', true, 0, false, true, 5, 'M');

		$pdf->MultiCell(214, 4, '', 0, '', 0, 0);
		$pdf->MultiCell(40, 4, 'по ОКПО', 0, 'R', 0, 0);
		$pdf->MultiCell(22, 4, '', 1, 'C', 0, 1);
		
		$pdf->MultiCell(214, 5, '', 0, '', 0, 0);
		$pdf->MultiCell(40, 5, '', 0, 'R', 0, 0);
		$pdf->MultiCell(22, 5, '', 1, 'C', 0, 1);
		
		$pdf->MultiCell(214, 4, '', 0, '', 0, 0, '', '', true, 0, false, true, 4, 'M');
		$pdf->MultiCell(40, 4, 'Вид деятельности по ОКДП', 0, 'R', 0, 0, '', '', true, 0, false, true, 4, 'M');
		$pdf->MultiCell(22, 4, '', 1, 'C', 0, 1, '', '', true, 0, false, true, 4, 'M');

		$pdf->MultiCell(214, 4, '', 0, '', 0, 0);
		$pdf->MultiCell(40, 4, 'по ОКПО', 0, 'R', 0, 0);
		$pdf->MultiCell(22, 4, '', 1, 'C', 0, 1);

		$pdf->MultiCell(214, 8, '', 0, '', 0, 0);
		$pdf->MultiCell(40, 8, 'по ОКПО', 0, 'R', 0, 0);
		$pdf->MultiCell(22, 8, '', 1, 'C', 0, 1);

		$pdf->MultiCell(214, 8, '', 0, '', 0, 0);
		$pdf->MultiCell(40, 8, 'по ОКПО', 0, 'R', 0, 0);
		$pdf->MultiCell(22, 8, '', 1, 'C', 0, 1);

		$pdf->MultiCell(230, 5, '', 0, '', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(24, 5, 'номер', 1, 'R', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(22, 5, '', 1, 'C', 0, 1, '', '', true, 0, false, true, 5, 'M');

		$pdf->MultiCell(230, 5, '', 0, '', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(24, 5, 'дата', 1, 'R', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(22, 5, '', 1, 'C', 0, 1, '', '', true, 0, false, true, 5, 'M');

		$pdf->MultiCell(230, 5, '', 0, '', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(24, 5, 'номер', 1, 'R', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(22, 5, '', 1, 'C', 0, 1, '', '', true, 0, false, true, 5, 'M');

		$pdf->MultiCell(230, 5, '', 0, '', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(24, 5, 'дата', 1, 'R', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(22, 5, '', 1, 'C', 0, 1, '', '', true, 0, false, true, 5, 'M');

		$pdf->MultiCell(214, 5, '', 0, '', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(40, 5, 'Вид операции', 0, 'R', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(22, 5, '', 1, 'C', 0, 1, '', '', true, 0, false, true, 5, 'M');


		$pdf->SetY(22);
		$pdf->SetFontSize(8);
		
		$pdf->MultiCell(205, 0, Configuration::get('INVOICETORGRU_SH'), 'B', 'L', 0, 1, '', '', true, 0, false, true);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(205, 0, 'грузоотправитель, адрес, номер телефона, банковские реквизиты', 0, 'C', 0, 1, '', '', true, 0, false, true);

		$pdf->SetY(31.1);
		$pdf->MultiCell(205, 3, '', 'B', 'L', 0, 1, '', '', true, 0, false, true);
		$pdf->MultiCell(205, 0, 'структурное подразделение', 0, 'C', 0, 1, '', '', true, 0, false, true);

		$pdf->Ln(2);
		$pdf->SetFontSize(8);
		$pdf->MultiCell(30, 0, 'Грузополучатель ', 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'T');
		$pdf->MultiCell(175, 0, ($result['org_name'] ? $result['org_name'] : $address_delivery->company).', '.($result['org_post_addr'] ? $result['org_post_addr'] : ($address_delivery->city .', '. $address_delivery->address1)).', ИНН '.$result['inn'].'/'.$result['kpp'].', р/с '.$result['rs'].' в '.$result['bank'].', БИК '.$result['bik'].', корр/с '.$result['ks'], 'B', 'L', 0, 1, '', '', true, 0, false, true, 0, 'M');
		
		
		//$pdf->MultiCell(175, 0, AddressFormat::generateAddress($address_invoice, array('avoid' => array()), ', '), 'B', 'L', 0, 1, '', '', true, 0, false, true, 0, 'M');

		$pdf->Ln(2);
		$pdf->MultiCell(30, 0, 'Поставщик ', 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'T');
		$pdf->MultiCell(175, 0, Configuration::get('INVOICETORGRU_S'), 'B', 'L', 0, 1, '', '', true, 0, false, true, 0, 'M');

		$pdf->Ln(2);
		$pdf->MultiCell(30, 0, 'Плательщик ', 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'T');
		$pdf->MultiCell(175, 0, ($result['org_name'] ? $result['org_name'] : $address_invoice->company).', '. ($result['org_ur_addr'] ? $result['org_ur_addr'] : $address_invoice->city .', '.$address_invoice->address1) .', ИНН '.$result['inn'].'/'.$result['kpp'].', р/с '.$result['rs'].' в '.$result['bank'].', БИК '.$result['bik'].', корр/с '.$result['ks'], 'B', 'L', 0, 1, '', '', true, 0, false, true, 0, 'M');
		//$pdf->MultiCell(175, 0, AddressFormat::generateAddress($address_delivery, array('avoid' => array()), ', '), 'B', 'L', 0, 1, '', '', true, 0, false, true, 0, 'M');

		$pdf->Ln(2);
		$pdf->MultiCell(30, 0, 'Основание ', 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'T');
		$pdf->MultiCell(175, 0, '', 'B', 'L', 0, 1, '', '', true, 0, false, true, 0, 'M');
		$pdf->SetFontSize(6);
		$pdf->MultiCell(175, 0, 'договор, заказ-наряд', 0, 'C', 0, 1, '', '', true, 0, false, true);


		$pdf->SetY(95);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(90, 4, '', 0, 'R', 0, 0);
		$pdf->MultiCell(2, 4, '', 0, '', 0, 0);
		$pdf->MultiCell(30, 4, 'Номер документа', 1, 'C', 0, 0, '', '', true, 0, false, true, 4, 'M');
		$pdf->MultiCell(30, 4, 'Дата составления', 1, 'C', 0, 1, '', '', true, 0, false, true, 4, 'M');

		$pdf->SetFontSize(9);
		$pdf->MultiCell(90, 5, 'ТОВАРНАЯ НАКЛАДНАЯ', 0, 'R', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->SetFontSize(8);
		$pdf->MultiCell(2, 5, '', 0, '', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(30, 5, sprintf('%06d', $order->id), 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
		$pdf->MultiCell(30, 5, date('d-m-Y'), 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');


		$pdf->Ln(10);
		$pdf->SetFontSize(7);
		$pdf->SetCellPadding(0.4);
		$pdf->MultiCell(274, 0, 'cтраница '.$pdf->getPage().' ', 0, 'R', 0, 1, 10, '');

		$pdf->MultiCell(11, 15, "№\nп/п", 1, 'C', 0, 0, 10, '');
		$pdf->MultiCell(76, 0, 'Товар', 1, 'C', 0, 0, '', '', true, 0, false, true);
		$pdf->MultiCell(30, 0, 'Единица измерения', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 15, "Вид\nупаков-\nки", 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(27, 0, 'Количество', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 15, "Масса\nбрутто", 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 15, "Количе-\nство\n(масса\nнетто)", 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(18, 15, "Цена\nруб. коп.", 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(21, 15, "Сумма без\nучета НДС\nруб. коп.", 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(35, 0, 'НДС', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 15, "Сумма с\nучетом\nНДС,\nруб. коп.", 1, 'C', 0, 0, '', '');
			$pdf->Ln(0);
				$pdf->MultiCell(62, 11, "наименование, характеристика, сорт,\nартикул товара", 1, 'C', 0, 0, $pdf->GetX()+11, $pdf->GetY()+4, true, 0, false, true);
				$pdf->MultiCell(14, 11, 'код', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(11, 11, "Наиме-\nнование", 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(19, 11, "код по\nОКЕИ", 1, 'C', 0, 0, '', '');
					$pdf->MultiCell(12, 11, "в\nодном\nместе", 1, 'C', 0, 0, $pdf->GetX()+14, '');
					$pdf->MultiCell(15, 11, "мест,\nштук", 1, 'C', 0, 0, '', '');
						$pdf->MultiCell(18, 11, 'ставка, %', 1, 'C', 0, 0, $pdf->GetX()+67, '');
						$pdf->MultiCell(17, 11, "сумма\nруб. коп.", 1, 'C', 0, 0, '', '');

		$pdf->Ln(11);
		$pdf->MultiCell(11, 0, '1', 1, 'C', 0, 0, 10, '');
		$pdf->MultiCell(62, 0, '2', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 0, '3', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(11, 0, '4', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(19, 0, '5', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 0, '6', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(12, 0, '7', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(15, 0, '8', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 0, '9', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 0, '10', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(18, 0, '11', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(21, 0, '12', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(18, 0, '13', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(17, 0, '14', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 0, '15', 1, 'C', 0, 1, '', '');


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

			if ($pdf->GetY() >= 167)
			{

				$pdf->MultiCell(143, 0, 'Итого  ', 0, 'R', 0, 0, 10, '');
				$pdf->MultiCell(15, 0, '', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 0, '', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 0, $tmp_totalQuantity, 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(18, 0, 'X', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(21, 0, number_format($tmp_totalWithoutTax, 2, '.', ''), 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(18, 0, 'X', 1, 'C', 0, 0, '', '');
				if (Configuration::get('INVOICETORGRU_NONDS'))
				{
					$pdf->MultiCell(17, 0, 'X', 1, 'C', 0, 0, '', '');
				}
				else
				{
					$pdf->MultiCell(17, 0, number_format($tmp_totalTax, 2, '.', ''), 1, 'R', 0, 0, '', '');
				}

				if (Configuration::get('INVOICETORGRU_NONDS'))
				{
					$pdf->MultiCell(14, 0, number_format($tmp_totalWithoutTax, 2, '.', ''), 1, 'R', 0, 1, '', '');
				}
				else
				{
					$pdf->MultiCell(14, 0, number_format($tmp_totalWithTax, 2, '.', ''), 1, 'R', 0, 1, '', '');
				}

				$tmp_totalQuantity = 0;
				$tmp_totalWithoutTax = 0;
				$tmp_totalWithTax = 0;
				$tmp_totalTax = 0;

						$pdf->AddPage();				

						
						
				$pdf->SetFontSize(7);
				$pdf->SetCellPadding(0.4);
				$pdf->MultiCell(274, 0, 'cтраница '.$pdf->getPage().' ', 0, 'R', 0, 1, 10, '');
				
				$pdf->MultiCell(11, 15, "№\nп/п", 1, 'C', 0, 0, 10, '');
				$pdf->MultiCell(76, 0, 'Товар', 1, 'C', 0, 0, '', '', true, 0, false, true);
				$pdf->MultiCell(30, 0, 'Единица измерения', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 15, "Вид\nупаков-\nки", 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(27, 0, 'Количество', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 15, "Масса\nбрутто", 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 15, "Количе-\nство\n(масса\nнетто)", 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(18, 15, "Цена\nруб. коп.", 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(21, 15, "Сумма без\nучета НДС\nруб. коп.", 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(35, 0, 'НДС', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 15, "Сумма с\nучетом\nНДС,\nруб. коп.", 1, 'C', 0, 0, '', '');
					$pdf->Ln(0);
						$pdf->MultiCell(62, 11, "наименование, характеристика, сорт,\nартикул товара", 1, 'C', 0, 0, $pdf->GetX()+11, $pdf->GetY()+4, true, 0, false, true);
						$pdf->MultiCell(14, 11, 'код', 1, 'C', 0, 0, '', '');
						$pdf->MultiCell(11, 11, "Наиме-\nнование", 1, 'C', 0, 0, '', '');
						$pdf->MultiCell(19, 11, "код по\nОКЕИ", 1, 'C', 0, 0, '', '');
							$pdf->MultiCell(12, 11, "в\nодном\nместе", 1, 'C', 0, 0, $pdf->GetX()+14, '');
							$pdf->MultiCell(15, 11, "мест,\nштук", 1, 'C', 0, 0, '', '');
								$pdf->MultiCell(18, 11, 'ставка, %', 1, 'C', 0, 0, $pdf->GetX()+67, '');
								$pdf->MultiCell(17, 11, "сумма\nруб. коп.", 1, 'C', 0, 0, '', '');

				$pdf->Ln(11);
				$pdf->MultiCell(11, 0, '1', 1, 'C', 0, 0, 10, '');
				$pdf->MultiCell(62, 0, '2', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 0, '3', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(11, 0, '4', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(19, 0, '5', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 0, '6', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(12, 0, '7', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(15, 0, '8', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 0, '9', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 0, '10', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(18, 0, '11', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(21, 0, '12', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(18, 0, '13', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(17, 0, '14', 1, 'C', 0, 0, '', '');
				$pdf->MultiCell(14, 0, '15', 1, 'C', 0, 1, '', '');

				$pdf->lasth = 0;
				$pdf->SetFontSize(8);
			}



			$pdf->MultiCell(62, $pdf->lasth, $product['product_name'], 1, 'L', 0, 0, 21, $pdf->GetY(), false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(11, $pdf->lasth, $i++, 1, 'C', 0, 0, 10, $pdf->GetY(), false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(14, $pdf->lasth, '', 1, 'C', 0, 0, 83, $pdf->GetY(), false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(11, $pdf->lasth, 'шт.', 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(19, $pdf->lasth, '', 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(14, $pdf->lasth, '', 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(12, $pdf->lasth, '', 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(15, $pdf->lasth, '', 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(14, $pdf->lasth, '', 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(14, $pdf->lasth, (int)($product['product_quantity']), 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(18, $pdf->lasth, number_format($product['product_price'], 2, '.', ''), 1, 'R', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			$pdf->MultiCell(21, $pdf->lasth, number_format(($product['product_price'] * $product['product_quantity']), 2, '.', ''), 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);

			if (Configuration::get('INVOICETORGRU_NONDS'))
			{
				$pdf->MultiCell(18, $pdf->lasth, 'Без НДС', 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			}
			else
			{
				$pdf->MultiCell(18, $pdf->lasth, number_format($product['tax_rate'] > 0 ? $product['tax_rate'] : '18', 3, '.', '000'), 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			}

			if (Configuration::get('INVOICETORGRU_NONDS'))
			{
				$pdf->MultiCell(17, $pdf->lasth, 'Без НДС', 1, 'C', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			}
			else
			{
				$pdf->MultiCell(17, $pdf->lasth, number_format(($product['tax_rate'] > 0 ? $product['total_wt'] - $product['total_price'] : ((((($product['product_price'] * $product['product_quantity']) / 100) * 18) + ($product['product_price'] * $product['product_quantity'])) - ($product['product_price'] * $product['product_quantity']))), 2, '.', ''), 1, 'R', 0, 0, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			}


			if (Configuration::get('INVOICETORGRU_NONDS'))
			{
				$pdf->MultiCell(14, $pdf->lasth, number_format(($product['product_price'] * $product['product_quantity']), 2, '.', ''), 1, 'R', 0, 1, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			}
			else
			{
				$pdf->MultiCell(14, $pdf->lasth, number_format(($product['tax_rate'] > 0 ? $product['total_wt'] : (((($product['product_price'] * $product['product_quantity']) / 100) * 18) + ($product['product_price'] * $product['product_quantity']))), 2, '.', ''), 1, 'R', 0, 1, '', '', false, 0, false, true, $pdf->lasth, 'M', 1);
			}

			if (Configuration::get('INVOICETORGRU_CALLNDS'))
			{
				$totalQuantity += (int)$product['product_quantity'];
				$totalWithoutTax += ($product['product_price'] * $product['product_quantity']);
				$totalWithTax += (((($product['product_price'] * $product['product_quantity']) / 100) * 18) + ($product['product_price'] * $product['product_quantity']));
				$totalTax += $product['total_wt'] - $product['total_price'];
				
				
				// fix
				$tmp_totalQuantity += $product['product_quantity'];
				$tmp_totalWithTax += ($product['product_price'] * $product['product_quantity']);
				$tmp_totalWithoutTax += (((($product['product_price'] * $product['product_quantity']) / 100) * 18) + ($product['product_price'] * $product['product_quantity']));
				$tmp_totalTax += $product['total_wt'] - $product['total_price'];
				// fix
			}
			else
			{
				$totalQuantity += (int)$product['product_quantity'];
				$totalWithTax += $product['total_wt'];
				$totalWithoutTax += $product['total_price'];
				$totalTax += $product['total_wt'] - $product['total_price'];

				// fix
				$tmp_totalQuantity += $product['product_quantity'];
				$tmp_totalWithTax += $product['total_wt'];
				$tmp_totalWithoutTax += $product['total_price'];
				$tmp_totalTax += $product['total_wt'] - $product['total_price'];
				// fix
			}
		}

		if ($order->total_discounts > 0)
		{
			$totalWithoutTax = $totalWithoutTax - $order->total_discounts;
			$totalWithTax = $totalWithTax - $order->total_discounts;

			$pdf->MultiCell(143, 0, 'Скидка  ', 0, 'R', 0, 0, 10, '');
			$pdf->MultiCell(15, 0, '', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(14, 0, '', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(14, 0, 'X', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(18, 0, 'X', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(21, 0, number_format($order->total_discounts, 2, '.', ''), 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(18, 0, 'X', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(17, 0, 'X', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(14, 0, number_format($order->total_discounts, 2, '.', ''), 1, 'R', 0, 1, '', '');
		}

		if (Configuration::get('INVOICETORGRU_ADDDELIVERY'))
		{
		
			$totalWithoutTax = $totalWithoutTax + $order->total_shipping;
			$totalWithTax = $totalWithTax + $order->total_shipping;
		
			$pdf->MultiCell(143, 0, 'Доставка  ', 0, 'R', 0, 0, 10, '');
			$pdf->MultiCell(15, 0, '', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(14, 0, '', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(14, 0, 'X', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(18, 0, 'X', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(21, 0, 'X', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(18, 0, 'X', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(17, 0, 'X', 1, 'C', 0, 0, '', '');
			$pdf->MultiCell(14, 0, number_format($order->total_shipping, 2, '.', ''), 1, 'R', 0, 1, '', '');
		}


		$pdf->MultiCell(143, 0, 'Итого  ', 0, 'R', 0, 0, 10, '');
		$pdf->MultiCell(15, 0, '', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 0, '', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 0, $totalQuantity, 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(18, 0, 'X', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(21, 0, number_format($totalWithoutTax, 2, '.', ''), 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(18, 0, 'X', 1, 'C', 0, 0, '', '');
		if (Configuration::get('INVOICETORGRU_NONDS'))
		{
			$pdf->MultiCell(17, 0, 'X', 1, 'C', 0, 0, '', '');
		}
		else
		{
			$pdf->MultiCell(17, 0, number_format($totalTax, 2, '.', ''), 1, 'R', 0, 0, '', '');
		}

		if (Configuration::get('INVOICETORGRU_NONDS'))
		{
			$pdf->MultiCell(14, 0, number_format($totalWithoutTax, 2, '.', ''), 1, 'R', 0, 1, '', '');
		}
		else
		{
			$pdf->MultiCell(14, 0, number_format($totalWithTax, 2, '.', ''), 1, 'R', 0, 1, '', '');
		}

		$pdf->MultiCell(143, 0, 'Всего по накладной  ', 0, 'R', 0, 0, 10, '');
		$pdf->MultiCell(15, 0, '', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 0, '', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(14, 0, $totalQuantity, 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(18, 0, 'X', 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(21, 0, number_format($totalWithoutTax, 2, '.', ''), 1, 'C', 0, 0, '', '');
		$pdf->MultiCell(18, 0, 'X', 1, 'C', 0, 0, '', '');
		if (Configuration::get('INVOICETORGRU_NONDS'))
		{
			$pdf->MultiCell(17, 0, 'X', 1, 'C', 0, 0, '', '');
		}
		else
		{
			$pdf->MultiCell(17, 0, number_format($totalTax, 2, '.', ''), 1, 'R', 0, 0, '', '');
		}
		
		if (Configuration::get('INVOICETORGRU_NONDS'))
		{
			$pdf->MultiCell(14, 0, number_format($totalWithoutTax, 2, '.', ''), 1, 'R', 0, 1, '', '');
		}
		else
		{
			$pdf->MultiCell(14, 0, number_format($totalWithTax, 2, '.', ''), 1, 'R', 0, 1, '', '');
		}

		$pdf->Ln(4);
		$pdf->SetFontSize(8);

		
		if ($pdf->GetY() >= 145)
		{
			$pdf->AddPage();	
		}
		
		
		$pdf->MultiCell(7, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(250, 1, 'Товарная накладная имеет приложение на ________________________________________________________________________________________________________________ листах', 0, 'L', 0, 1);
		$pdf->Ln(2);
		$pdf->MultiCell(7, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(250, 1, 'и содержит __________________________________________________________________________________________________________________________________________________ порядковых номеров записей', 0, 'L', 0, 1);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(257, 1, '(прописью)', 0, 'C', 0, 0);
		
	

		
		$pdf->Ln(4);
		$pdf->SetFontSize(8);
		$pdf->MultiCell(7, 2, '', 0, 'L', 0, 0);
		$pdf->MultiCell(95, 2, '', 0, 'L', 0, 0);
		$pdf->MultiCell(170, 2, 'Масса груза (нетто)       __________________________________________________________________', 0, 'L', 0, 1);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(95, 2, '', 0, 'L', 0, 0);
		$pdf->MultiCell(170, 1, '(прописью)', 0, 'C', 0, 0);
		$pdf->Ln(2);
		$pdf->SetFontSize(8);

		$pdf->MultiCell(7, 2, '', 0, 'L', 0, 0);
		$pdf->MultiCell(95, 2, 'Всего мест    _______________________________________________________', 0, 'L', 0, 0);
		$pdf->MultiCell(170, 2, 'Масса груза (брутто)     __________________________________________________________________', 0, 'L', 0, 1);
		$pdf->MultiCell(95, 2, '', 0, 'L', 0, 0);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(170, 1, '(прописью)', 0, 'C', 0, 0);
		
		
		

		$pdf->Ln(6);
		$pdf->SetFontSize(8);
		$pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => '#000'));
		$pdf->Line(148.5, $pdf->GetY()+1, 148.5, $pdf->GetY()+45);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, 'Приложение (паспорта, сертификаты и т.п.) на          ___________________________________     листах', 0, 'L', 0, 0);
		$pdf->MultiCell(5, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, 'По доверенности №       ______________________ от   "______" ______________________    ___________ г.', 0, 'L', 0, 1);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '                                                                                       (прописью)', 0, 'C', 0, 0);
		
		$pdf->Ln(2);
		$pdf->SetFontSize(8);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, 'Всего отпущено на сумму         _____________________________________________________________________', 0, 'L', 0, 0);
		$pdf->MultiCell(5, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, 'выданной    ________________________________________________________________________________________', 0, 'L', 0, 1);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '                                                         (прописью)', 0, 'C', 0, 0);
		$pdf->MultiCell(5, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '                  (кем, кому (организация, должность, фамилия, и., о.))', 0, 'C', 0, 0);
		
		
		$pdf->Ln(2);
		$pdf->SetFontSize(8);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '______________________________________________________________________________________________________', 0, 'L', 0, 0);
		$pdf->MultiCell(5, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '_____________________________________________________________________________________________________', 0, 'L', 0, 1);

		
		$pdf->Ln(2);
		$pdf->SetFontSize(8);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, 'Отпуск груза разрешил        _____________________   _____________________   __________________________', 0, 'L', 0, 0);
		$pdf->MultiCell(5, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '_____________________________________________________________________________________________________', 0, 'L', 0, 1);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '                                                             (должность)                              (подпись)                        (расшифровка подписи)', 0, 'C', 0, 0);
		
		$pdf->Ln(4);
		$pdf->SetFontSize(8);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, 'Главный (старший) бухгалтер                                 _____________________   __________________________', 0, 'L', 0, 0);
		$pdf->MultiCell(5, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, 'Груз принял        _______________________   _______________________   _________________________________', 0, 'L', 0, 1);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '                                                                                                               (подпись)                        (расшифровка подписи)', 0, 'C', 0, 0);
		$pdf->MultiCell(5, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '                           (должность)                                 (подпись)                                  (расшифровка подписи)', 0, 'C', 0, 0);
		
		
		$pdf->Ln(4);
		$pdf->SetFontSize(8);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, 'Отпуск груза произвел         _____________________   _____________________   __________________________', 0, 'L', 0, 0);
		$pdf->MultiCell(5, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, 'Груз получил грузополучатель     ___________________   ___________________   _______________________', 0, 'L', 0, 1);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '                                                             (должность)                              (подпись)                        (расшифровка подписи)', 0, 'C', 0, 0);
		$pdf->MultiCell(5, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '                                                                       (должность)                         (подпись)                    (расшифровка подписи)', 0, 'C', 0, 0);
		

		$pdf->Ln(7);
		$pdf->SetFontSize(8);
		$pdf->MultiCell(2, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '                  М.П.                "______"  ______________________     ___________ года', 0, 'L', 0, 0);
		$pdf->MultiCell(5, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(136.5, 1, '                  М.П.                "______"  ______________________     ___________ года', 0, 'L', 0, 0);

		
		
		$pdf->MultiCell(40, 6.5, '', 1, 'C', 0, 1, 232, $pdf->GetY()-66.8);
		$pdf->MultiCell(40, 6.5, '', 1, 'C', 0, 0, 232, '');
		
		$numstr = $pdf->num2str(--$i, true, true);
		$pdf->Text(35, $pdf->GetY() - 12, mb_strtoupper(mb_substr($numstr, 0, 1, 'utf-8'), 'utf-8').mb_substr($numstr, 1, mb_strlen($numstr, 'utf-8'), 'utf-8'));
		
		$totalstr = Configuration::get('INVOICETORGRU_NONDS') ? $pdf->num2str(number_format($totalWithoutTax, 2, '.', '')) : $pdf->num2str(number_format($totalWithTax, 2, '.', ''));
		$pdf->Text(14, $pdf->GetY() + 37.7, mb_strtoupper(mb_substr($totalstr, 0, 1, 'utf-8'), 'utf-8').mb_substr($totalstr, 1, mb_strlen($totalstr, 'utf-8'), 'utf-8'));

	
/*
		$pdf->MultiCell(40, 2, '', 0, 'L', 0, 0);
		$pdf->MultiCell(100, 2, 'Главный бухгалтер ________________________________________', 0, 'L', 0, 1);

		$pdf->SetFontSize(6);
		$pdf->MultiCell(50, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(30, 1, '(подпись)', 0, 'C', 0, 0);
		$pdf->MultiCell(30, 1, '(ф.и.о)', 0, 'C', 0, 0);

		$pdf->MultiCell(69, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(30, 1, '(подпись)', 0, 'C', 0, 0);
		$pdf->MultiCell(30, 1, '(ф.и.о)', 0, 'C', 0, 0);

		$pdf->Ln(5);
		$pdf->SetFontSize(8);
		$pdf->MultiCell(100, 1, 'Руководитель организации ________________________________________', 0, 'L', 0, 1);
		$pdf->SetFontSize(6);
		$pdf->MultiCell(50, 1, '', 0, 'L', 0, 0);
		$pdf->MultiCell(30, 1, '(подпись)', 0, 'C', 0, 0);
		$pdf->MultiCell(30, 1, '(ф.и.о)', 0, 'C', 0, 0);
*/


		return $pdf->Output(Configuration::get('INVOICETORGRU_PREFIX').sprintf('%06d', $order->id).'.pdf', $mode);
	}

}

if (Tools::getValue('id_order'))
	return SPDF::invoice(Tools::getValue('id_order'));