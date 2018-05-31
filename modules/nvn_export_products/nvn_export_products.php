<?php
/* ########################################################################### */
/* ----------------    NVN Export Products PrestaShop module   --------------- */
/*                       Copyright 2013   Karel Falgenhauer                    */
/*                          http://www.netvianet.com/                          */
/*                           http://www.praotec.com/                           */
/*             Please do not change this text, remove the link,                */
/*          or remove all or any part of the creator copyright notice          */
/*                                                                             */
/*    Please also note that although you are allowed to make modifications     */
/*     for your own personal use, you may not distribute the original or       */
/*                 the modified code without permission.                       */
/*                                                                             */
/*     SELLING AND REDISTRIBUTION IS FORBIDDEN! DO NOT SHARE WITH OTHERS!      */
/*                  Download is allowed only from netvianet.com                */
/*                                                                             */
/*       This software is provided as is, without warranty of any kind.        */
/*           The author shall not be liable for damages of any kind.           */
/*               Use of this software indicates that you agree.                */
/*                                                                             */
/* ########################################################################### */


//************************************************************************************************************************
class nvn_export_products extends Module
//************************************************************************************************************************
{
private $_html = '';
private static $categs = array();
private static $lastexportime = 0;
public $js_on = 0;

    function __construct()
    {
        $this->name = 'nvn_export_products';
        $this->tab =  'administration';
        $this->author = 'netvianet.com';
        $this->version = '2.8 L2'; //css
        parent::__construct();
    
        $this->displayName = $this->l('NVN Export Products XML');
        $this->description = $this->l('A module to export complete products XML. Use NVN Import XML Products module to import products, categories, combinations, images, manufacturers etc into another installation of PrestaShop.');
    }

//************************************************************************************************************************
    public function install()
//************************************************************************************************************************
    {if (parent::install() == false  OR  
    Configuration::updateValue('EXPRODUCT_NVN_ALASTID',-1) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_ANEXTID',-1) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_CLASTID',-1) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_CNEXTID',-1) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_PLASTID',-1) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_PNEXTID',-1) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_NEXT',0) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_MUTEX',0) == false OR        
    Configuration::updateValue('EXPRODUCT_NVN_ACTIVE',0) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_UPDNEW',0) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_MSHOP','0|All') == false OR
    Configuration::updateValue('EXPRODUCT_NVN_CATEG','') == false OR
    Configuration::updateValue('EXPRODUCT_NVN_DEFCAT',0) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_JSETG',1) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_AFAIL','') == false OR
    Configuration::updateValue('EXPRODUCT_NVN_CFAIL','') == false OR
    Configuration::updateValue('EXPRODUCT_NVN_PFAIL','') == false OR
    Configuration::updateValue('EXPRODUCT_NVN_LIMIT','unlimited') == false OR
    Configuration::updateValue('EXPRODUCT_NVN_MINTIME','24') == false OR
    Configuration::updateValue('EXPRODUCT_NVN_LANG2',(int)(Configuration::get('PS_LANG_DEFAULT'))) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_JSON',0) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_TOKENOF',0) == false OR
    Configuration::updateValue('EXPRODUCT_NVN_RND',$this->generateRandomString(10)) == false
     )
                return false;
    return true; 
    } 
//************************************************************************************************************************
	public function uninstall()
//************************************************************************************************************************
    {if (!parent::uninstall() OR
    !Configuration::deleteByName('EXPRODUCT_NVN_ALASTID') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_ANEXTID') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_CLASTID') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_CNEXTID') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_PLASTID') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_PNEXTID') OR 
    !Configuration::deleteByName('EXPRODUCT_NVN_NEXT') OR  
    !Configuration::deleteByName('EXPRODUCT_NVN_MUTEX') OR       
    !Configuration::deleteByName('EXPRODUCT_NVN_ACTIVE') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_UPDNEW') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_MSHOP') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_CATEG') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_DEFCAT') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_JSETG') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_AFAIL') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_CFAIL') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_PFAIL') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_LIMIT') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_MINTIME') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_LANG2') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_JSON') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_TOKENOF') OR
    !Configuration::deleteByName('EXPRODUCT_NVN_RND')
    )
            return false;
    return true;   
    }
//************************************************************************************************************************
 public function displayForm()
//************************************************************************************************************************
    {
     (Configuration::get('EXPRODUCT_NVN_TOKENOF')) ? $foo = '' : $foo =  "?act=cron&token=".substr(_COOKIE_KEY_, 34, 8);   
     $url0=Tools::getHttpHost(true).__PS_BASE_URI__."modules/nvn_export_products/export_cron_products.php".$foo;
     $lng = (int)(Configuration::get('PS_LANG_DEFAULT'));
     self::$categs = explode(',',Configuration::get('EXPRODUCT_NVN_CATEG'));
     if(Configuration::get('EXPRODUCT_NVN_JSETG')){$shide='none';}
     else{$shide='block';}
     $fname = "nvn_products_export_".Configuration::get('EXPRODUCT_NVN_RND').".xml";		
     $xml_path = dirname(__FILE__)."/download/".$fname ; 
     (function_exists('curl_init'))? $jecurl = '<span class="bold" style="color:green">CURL is available :-)&nbsp;&nbsp;</span>':$jecurl ='<span class="bold" style="color:red">CURL IS NOT AVAILABLE !!!</span>';
     (extension_loaded('XMLReader'))? $jexmlr = '<span class="bold" style="color:green">XMLReader is available :-)&nbsp;&nbsp;</span>':$jexmlr ='<span class="bold" style="color:red">XMLReader IS NOT AVAILABLE !!!</span>';
     $lastexport = "NOT EXIST.";
     if (file_exists($xml_path)) {$lastexport = date ("F d Y H:i:s.", filemtime($xml_path));}
     $comlet = "COMPLETED.";
     if((int)(Configuration::get('EXPRODUCT_NVN_ALASTID')<>-1 OR Configuration::get('EXPRODUCT_NVN_CLASTID')<>-1 OR Configuration::get('EXPRODUCT_NVN_PLASTID')<>-1 OR $lastexport == "NOT EXIST.")){$comlet = "NOT COMPLETED.";} 
     $slink = 'Not exist or is not complete.';
     if($comlet == "COMPLETED."){$slink = _PS_BASE_URL_.__PS_BASE_URI__."modules/nvn_export_products/download/nvn_products_export_".Configuration::get('EXPRODUCT_NVN_RND').".xml";}
     $afail = '';
     if((Configuration::get('EXPRODUCT_NVN_AFAIL')) <> ''){
     $afail = '<span style="text-align: left;display: table-cell;min-width: 10px;padding: 5px; background:#FE5A5A; color:#ffffff">
                FAILED ATTR GRPS: <p class="clear" ></p>'.Configuration::get('EXPRODUCT_NVN_AFAIL').'
               </span>';   
     }
     $cfail = '';
     if((Configuration::get('EXPRODUCT_NVN_CFAIL')) <> ''){
     $cfail = '<span style="text-align: left;display: table-cell;min-width: 10px;padding: 5px; background:#FE5A5A; color:#ffffff">
                FAILED CATEGORIES ID: <p class="clear" ></p>'.Configuration::get('EXPRODUCT_NVN_CFAIL').'
               </span>';   
     }     
     $pfail = '';
     if((Configuration::get('EXPRODUCT_NVN_PFAIL')) <> ''){
      $pfail = '<span style="text-align: left;display: table-cell;min-width: 10px;padding: 5px; background:#FE5A5A; color:#ffffff">
                FAILED PRODUCTS ID: <p class="clear" ></p>'.Configuration::get('EXPRODUCT_NVN_PFAIL').'<p class="clear" ></p>Check it in Catalog  > product. Otherwise if you continue, this products will be not imported.
               </span>';   
     }
     $this->_html = '
<link rel="stylesheet" type="text/css" href="'.$this->_path.'nvn.css">     
<script type="text/javascript" language="JavaScript">
<!--      
     function alterset() {
     var setingx = document.getElementById("altset").checked;
     document.getElementById("jsetingx").value = "jsetingxsend";   
     document.getElementById("nvn_export_products").submit();    
     }
// -->
</script>
     <form id= "nvn_export_products" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
     <input type="hidden" name="jsetingx" id="jsetingx" value=""/>
     <fieldset>
     <legend><img src="'.$this->_path.'logo.gif" /> '.$this->l('Setting').'</legend>
     <label>'.$this->l('Export active products only:').'</label>
     <div class="margin-form">
        <label class = "checkme"><input type="checkbox" name="activo" value="1"'.((Configuration::get('EXPRODUCT_NVN_ACTIVE')) ? ' checked=""': '').'/>
        '.$this->l('Export only active products.').'</label><p class="clear" ></p>
     </div>
     <label>'.$this->l('Export upd. and new products only:').'</label>
     <div class="margin-form">
        <label class = "checkme"><input type="checkbox" name="updnew" value="1"'.((Configuration::get('EXPRODUCT_NVN_UPDNEW')) ? ' checked=""': '').'/>
        '.$this->l('Export only new and updated products. (Fast update, derived from the time xml file.)').'</label><p class="clear" ></p>
     </div>     
     <label for="s">'.$this->l('Shop name:').'</label>
        <div class="margin-form">
        '.$this->getShopName(Configuration::get('EXPRODUCT_NVN_MSHOP')).'<span>'.$this->l('For MultiStore support.').'</span>
        </div>   
     <label>'.$this->l('Category settings:').'</label>
     <div class="margin-form">
     <label class = "checkme"><input type="checkbox" id="altset" name="altset" onclick="javascript:alterset();" value="1" '.((Configuration::get('EXPRODUCT_NVN_JSETG')) ? ' checked=""': '').' />&nbsp;'.$this->l('Export products from all categories').'</label>
     <p class="clear" ></p>
     </div>
     <div id="showAll" style="display:'.$shide.';">
         <div class="margin-form"> 
          <label class = "checkme"><input type="checkbox" id="defcat" name="defcat" value="1" '.((Configuration::get('EXPRODUCT_NVN_DEFCAT')) ? ' checked=""': '').' />&nbsp;'.$this->l('Only products as default in selected categories').'</label>
          <p class="clear" ></p>
          <label class = "checkme">'.$this->l('Select Categories. Multiselect (CTRL / Shift + mouse).').'</label><p class="clear" ></p>
         <select multiple="multiple" name="categs[ ]" id="afrom" style="width: 650px; height: 160px; font-family:courier, monospace; font-size:1em;">
         '.$this->getAllCategories($lng).'
         </select>
         </div>
     </div>
     <label>'.$this->l('Limit queries in a cycle:').'</label>
     <div class="margin-form">
        '.$this->getLimit(Configuration::get('EXPRODUCT_NVN_LIMIT')).'
        <span>'.$this->l('If you have many products and is allowed memory size exhausted error, set lower limit.').'</span>
     </div>
     <label>'.$this->l('Minimum time limit (hour):').'</label>
     <div class="margin-form">
        '.$this->getMintime(Configuration::get('EXPRODUCT_NVN_MINTIME')).'
        <span>'.$this->l('The shortest interval re-generate the xml. (Useful with CRON)').'</span>
     </div>     
    <label>'.$this->l('Add language:').'</label>
     <div class="margin-form">
        '.$this->getLangs(Configuration::get('EXPRODUCT_NVN_LANG2')).'
        <span>'.$this->l('Select only if you require a second language. Default shop language is exported always.').'</span>
     </div>
    <label>'.$this->l('Encode XML JSON:').'</label>
     <div class="margin-form">     
     <input type="checkbox" class="checkme" id="json" name="json"  value="1"'.((Configuration::get('EXPRODUCT_NVN_JSON')) ? ' checked=""': '').'/>          
     <span style="text-align:left!important;float:left!important;"> '.$this->l('Encode special chars as "\uxxxx". Use if some problems with not UTF-8 characters. Same setting must be in import module - "Advanced options"!').' </span><p class="clear"></p>
     </div>
     <label>'.$this->l('CRON problem:').'</label>
     <div class="margin-form">     
     <input type="checkbox" class="checkme" id="tokenof" name="tokenof"  value="1"'.((Configuration::get('EXPRODUCT_NVN_TOKENOF')) ? ' checked=""': '').'/>          
     <span style="text-align:left!important;float:left!important;"> '.$this->l('Enable this option if some problem with CRON link. Do not forget change CRON link on Your Hosting!').' </span><p class="clear"></p>
     </div>     
     <div class="margin-form">
        <span style="text-align: left;display: table-cell;min-width: 10px;padding: 5px; background:#FE5A5A;">
        <input type="submit" name="submitSetting" style="float:left;" value="'.$this->l('Save Settings').'" class="button" />
        <span style="color:white;font-size:16px;float:left;">&nbsp;'.$this->l('Do not forget save settings!!!').'</span>
        </span>
     </div>
     </fieldset> 
     
     <fieldset>
     <legend><img src="'.$this->_path.'logo.gif" /> '.$this->l('Export products XML').'</legend>
      <div class="margin-form">
      '.$afail.'<p class="clear" ></p> 
      '.$cfail.'<p class="clear" ></p> 
      '.$pfail.'<p class="clear" ></p>
      <span style="text-align: left;display: table-cell;min-width: 10px;padding: 5px; background:#00AAAA;">
      <center><input type="submit" name="submitExport" value="'.$this->l('Export Products').'" class="button" /></center>
      </span>
      <p class="bold" style="color:green"><a style=" color:#f02200" href="http://netvianet.com/prestashop-module/40-import-xml-products.html" target="_blank"> Import this XML Products </a>'.$this->l('(categories,images,manufacturers,suppliers,attributes,features) into another installation of PrestaShop.').'</p>
      <p class="bold" style="color:black">'.$this->l('If import ends with fatal error, continue with Refresh page button in Your browser. Faulty product will be skipped in the next cycle. Or use').' <a style=" color:#f02200" href="http://tyx.cz/pretashop-export-xml-products.html" target="_blank"> this help</a>.'.'</p>
      </div>
     </fieldset> 
    <fieldset>
    <a href="http://www.netvianet.com" target="_blank"><img style="float:left;" src="'.$this->_path.'nvn_l.png" /></a>
        <legend><img src="'.$this->_path.'logo.gif" /> '.$this->l('Description').'</legend>
        <div class="margin-form">
         <div style="border:1px solid #707070; background:#e6fc7e;">
         <div class="bold" style="border-bottom:1px solid #707070; color:blue; background:#ffff00; width:100%;">'.$this->l('Link to Your XML export file. Copy and paste this link into ').'<a style=" color:#f02200" href="http://netvianet.com/prestashop-module/40-import-xml-products.html" target="_blank"> Import XML Products </a>module on target site. 
         <p style="color:red">'.$this->l('Each installation has its own link. Do not publish this link - anyone could download Your products.').'</p>
         </div>
         <p>LINK TO XML EXPORT FILE:</p>
         <span class="bold" style="color:#268626; background:#befb4d; padding:3px; border:1px solid #707070;">'.$slink.'</span><p></p>
         </div>         
         <p></p>
         <div style="border:1px solid #707070; background:#f9a4f9;">
         <span class="bold" style="color:blue">BEFORE BUY IMPORT MODULE, INSTALL THIS MODULE ON TARGET SITE FOR TEST REQUIRED LIBRARIES.</span><p></p>
         '.$jecurl.$jexmlr.'
         </div>         
         <p></p>
         <span>'.$this->l('Last export file: - ').'</span><span class="bold" style="color:blue">'.$lastexport.'</span><span>&nbsp;'.$this->l('Status: - ').'</span><span class="bold" style="color:blue">'.$comlet.'</span>
         <p>For proper function of the automatic export XML feed you must have setting CRON to script: </p><p class="bold" style="color:#FFFFFF; background:#000000">'.$url0.' </p>
         <p class="bold" style="color:red">ALWAYS USE SAME VERSION OF EXPORT AND IMPORT PRODUCT !!!</p>
         <p class="bold" style="color:green">Free version from <a style=" color:#f02200" href="http://www.netvianet.com" target="_blank"> www.netvianet.com </a>. Download <a style=" color:#f02200" href="http://netvianet.com/prestashop-module/40-import-xml-products.html" target="_blank"> Import XML Products </a> for import this XML to another PrestaShop.</p> 
        </div>
    </fieldset>
      <div class="margin-form" style="background:#FFFEC8; text-align:leftt;">
      <p class="bold" style="color:red">'.$this->l('If You not using "NVN Import XML Products" module for import this XML into another PrestaShop or if NVN Export module was updated, for security reasons delete the export file from server.').'</p> 
         <input type="submit" name="submitDelete" value="'.$this->l('Delete old export file. (Next export will begin from Scratch)').'" class="button" />
      </div>
    </form>
    <form action="'. _PS_BASE_URL_.__PS_BASE_URI__.'modules/nvn_export_products/download.php"  method="post" target="_blank">
      <span style="float:left;">Current version: '.$this->version.'</span><br />
         <div style="float:left;background:#C8CCFF;padding:5px;">
      </div><p class="clear"></p>
    </form>';
    return $this->_html;
    }
    
//************************************************************************************************************************    
    private function getLangs($saveas)
//************************************************************************************************************************    
    {   $sout = '';
        $lng = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $res = Language::getLanguages(false);
        if($saveas==0){$saveas=$lng;}
        $sout .= '<select name="glangs">';
          foreach ($res as $row){
              ($lng == (int)$row['id_lang']) ? $d='(default) ':$d='';
              if ($row['id_lang'] == $saveas)
               {$sout .= '<option value="'.(string)($row['id_lang']).'" selected="selected">'.$d.(string)($row['name']).'</option>'; }
              else {$sout .= '<option value="'.(string)($row['id_lang']).'">'.$d.(string)($row['name']).'</option>';}
            }
          $sout .= '</select>';
    return $sout;
    } 	            
//************************************************************************************************************************    
    private function getLimit($saveas)
//************************************************************************************************************************    
    {   $sout = '';
        $res = $this->limitF();
          $sout .= '<select name="qlimit">';
          foreach ($res as $row){
          if ($row == $saveas)
           {$sout .= '<option value="'.(string)($row).'" selected="selected">'.(string)($row).'</option>'; }
          else {$sout .= '<option value="'.(string)($row).'">'.(string)($row).'</option>';}
          }
          $sout .= '</select>';
    return $sout;
    }
//************************************************************************************************************************    
    private function getMintime($saveas)
//************************************************************************************************************************    
    {   $sout = '';
        $res = $this->limitT();
          $sout .= '<select name="mintime">';
          foreach ($res as $row){
          if ($row == $saveas)
           {$sout .= '<option value="'.(string)($row).'" selected="selected">'.(string)($row).'</option>'; }
          else {$sout .= '<option value="'.(string)($row).'">'.(string)($row).'</option>';}
          }
          $sout .= '</select>';
    return $sout;
    }     	        
//************************************************************************************************************************    
	public function mutex()
//************************************************************************************************************************        
 {
    if(time()- Configuration::get('EXPRODUCT_NVN_MUTEX') > 60){//tak uz 60 vterin nejede
     return TRUE;   
    }else{
     echo('<span class="bold" style="color:#ffffff; font-size: 16px; background:#ff0000; padding:3px; border:1px solid #707070;">Warning: Request to run but the script is still running.</span>');
     return FALSE;   
    }
 } 
//************************************************************************************************************************    
     private function getShopName($mshop)
//************************************************************************************************************************    
    {   $sout = '';
    $lng = (int)(Configuration::get('PS_LANG_DEFAULT'));
    /*$result = Db::getInstance()->getRow("SELECT * FROM `"._DB_PREFIX_."category` AS ca 
    INNER  JOIN `"._DB_PREFIX_."category_lang` AS cl ON (cl.id_category=ca.id_category) 
    WHERE cl.id_lang = ".$lng."  AND ca.id_category = 1");*/
    
    $sout .= '<select name="mshop" onchange="javascript:alterset();">';
    
    if($this->versionPS()>4){ //($result['name']=='Root')
        $foo = Db::getInstance()->ExecuteS("SELECT  `id_shop`,`name` FROM `"._DB_PREFIX_."shop`");
        if($mshop == "0|All"){Configuration::updateValue('EXPRODUCT_NVN_MSHOP','1|Init');}
          foreach ($foo as $r){
          $row = $r['id_shop'].'|'.$r['name'];
          if ($row == $mshop)
           {$sout .= '<option value="'.(string)($row).'" selected="selected">'.(string)($row).'</option>'; }
          else {$sout .= '<option value="'.(string)($row).'">'.(string)($row).'</option>';}
          }
      } else {$sout .= '<option value="0|All">0|All</option>'; }   
          $sout .= '</select>';
    return $sout;
    } 
//************************************************************************************************************************    
	private function getAllCategories($lng)
//************************************************************************************************************************    
    {$out = '';
    $lng = (int)(Configuration::get('PS_LANG_DEFAULT'));
    $mshop = explode('|',Configuration::get('EXPRODUCT_NVN_MSHOP'));     
    $js1 = "";$js2 = "";$nn = "";$ss = "";$filterMshop = ''; 
    if($mshop[0]){
     $js2 = " JOIN `"._DB_PREFIX_."shop` AS sn ON sn.`id_shop` = cs.`id_shop` ";
     $js1 = " JOIN `"._DB_PREFIX_."category_shop` AS cs ON cs.`id_category` = c.`id_category` ";
     $nn = ", sn.name AS sname";$ss = "sn.name, ";
     $filterMshop = ' AND cs.id_shop = '.$mshop[0]; 
    }
    $sq = "SELECT DISTINCT c.`id_category`, cl.`name` AS cname".$nn." FROM `"._DB_PREFIX_."category` AS c 
     LEFT JOIN `"._DB_PREFIX_."category_lang` AS cl ON c.`id_category` = cl.`id_category`
     ".$js1.$filterMshop.$js2."  
     WHERE cl.`id_lang` = ".(int)$lng." ORDER BY ".$ss."c.`id_category` ASC"; 
     $categories = Db::getInstance()->executeS($sq);
     foreach($categories as $row){
      $sel = '';$sname="";
      if(in_array($row['id_category'], self::$categs)) {$sel = 'selected="selected"';};  
      if(array_key_exists('sname',$row)){$i=64-mb_strlen($row['cname'],'UTF-8'); $sname = str_repeat('.',$i).'ID: '.$row['id_category'].' | '.$row['sname'] ;}
      $out .= '<option value="'.$row['id_category'].'" '.$sel.'>'.$row['cname'].$sname.'</option>';
     }
     return $out;
    }         

//------------------------------------------------------------------------------------------------------------------------
    
//************************************************************************************************************************
 	public function getContent()
//************************************************************************************************************************
	{   global $cookie;
        $output = '<h2>'.$this->displayName.'</h2>';     
        if (Tools::getValue('jsetingx')=="jsetingxsend"){
            Configuration::updateValue('EXPRODUCT_NVN_JSETG', Tools::getValue('altset'));
           if(Tools::getIsset('categs')){ 
           self::$categs = array();
            foreach (Tools::getValue('categs') as $selectedOption)
             {self::$categs[] = (int)$selectedOption;}
           Configuration::updateValue('EXPRODUCT_NVN_CATEG', implode(',',self::$categs));
         }
         Configuration::updateValue('EXPRODUCT_NVN_ACTIVE',Tools::getValue('activo'));
         Configuration::updateValue('EXPRODUCT_NVN_UPDNEW',Tools::getValue('updnew'));
         Configuration::updateValue('EXPRODUCT_NVN_MSHOP',Tools::getValue('mshop'));
         Configuration::updateValue('EXPRODUCT_NVN_DEFCAT',Tools::getValue('defcat'));
         Configuration::updateValue('EXPRODUCT_NVN_LIMIT',Tools::getValue('qlimit'));
         Configuration::updateValue('EXPRODUCT_NVN_MINTIME',Tools::getValue('mintime'));
         Configuration::updateValue('EXPRODUCT_NVN_LANG2',Tools::getValue('glangs'));
            return $output.$this->displayForm();
        }
       if(Tools::isSubmit('submitExport')){
       if($this->mutex()){
         $xml_path = dirname(__FILE__)."/download/nvn_products_export_".Configuration::get('EXPRODUCT_NVN_RND').".xml" ; 
         (file_exists($xml_path)) ? self::$lastexportime = filemtime($xml_path) : self::$lastexportime = 0;
         (time() - self::$lastexportime > Configuration::get('EXPRODUCT_NVN_MINTIME')*3600) ? $isPeriod = true : $isPeriod = false;
         $comlet = 1;
         if((int)(Configuration::get('EXPRODUCT_NVN_ALASTID')<>-1 OR Configuration::get('EXPRODUCT_NVN_CLASTID')<>-1 OR Configuration::get('EXPRODUCT_NVN_PLASTID')<>-1 OR self::$lastexportime == 0))
         {$comlet = 0;} 
         if($comlet == 0 OR $isPeriod){
          $this->js_on  = (int)(Configuration::get('EXPRODUCT_NVN_JSON'));
          Configuration::updateValue('EXPRODUCT_NVN_MUTEX', time());
          $next=Configuration::get('EXPRODUCT_NVN_NEXT');
    	  if($next==0){$output .= $this->exportAttributes();}
          $next=Configuration::get('EXPRODUCT_NVN_NEXT');
          if($next==1){$output .= $this->exportCategories();}
          $next=Configuration::get('EXPRODUCT_NVN_NEXT');
          if($next==2){$output .= $this->exportProducts();}
          Configuration::updateValue('EXPRODUCT_NVN_MUTEX',0);
          }else{
            $output .= '<div class="alerte">'.$this->l('Export xml file is completed and interval for generating xml was not yet been reached. Delete the old export file or change the interval. (Minimum time limit)').'</div>'; 
          }
          return $output.$this->displayForm(); 
        }
       } 
       if(Tools::isSubmit('submitSetting')){
           if(Tools::getIsset('categs')){ 
           self::$categs = array();
            foreach (Tools::getValue('categs') as $selectedOption)
             {self::$categs[] = (int)$selectedOption;}
           Configuration::updateValue('EXPRODUCT_NVN_CATEG', implode(',',self::$categs));
         }
         Configuration::updateValue('EXPRODUCT_NVN_ACTIVE',Tools::getValue('activo'));
         Configuration::updateValue('EXPRODUCT_NVN_UPDNEW',Tools::getValue('updnew'));
         Configuration::updateValue('EXPRODUCT_NVN_MSHOP',Tools::getValue('mshop'));
         Configuration::updateValue('EXPRODUCT_NVN_DEFCAT',Tools::getValue('defcat'));
         Configuration::updateValue('EXPRODUCT_NVN_LIMIT',Tools::getValue('qlimit'));
         Configuration::updateValue('EXPRODUCT_NVN_MINTIME',Tools::getValue('mintime'));
         Configuration::updateValue('EXPRODUCT_NVN_LANG2',Tools::getValue('glangs'));
         Configuration::updateValue('EXPRODUCT_NVN_JSON', (int)Tools::getValue('json'));
         Configuration::updateValue('EXPRODUCT_NVN_TOKENOF', (int)Tools::getValue('tokenof'));
           $output .= '<div class="confok"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';      
        return $output.$this->displayForm(); 
        }
       if(Tools::isSubmit('submitDelete')){
               $this->nvreset(true);
               @unlink(dirname(__FILE__)."/download/nvn_products_export_".Configuration::get('EXPRODUCT_NVN_RND').".xml");
               $output .= '<div class="confok"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Exports files was deleted').'</div>';
            return $output.$this->displayForm();
        } 
     return $output.$this->displayForm();   
    }
//************************************************************************************************************************
 	public function exportCategories()
//************************************************************************************************************************    
{
     $d1='|.|';$d2='|:|';$d3='|!|'; 
     $lng = (int)(Configuration::get('PS_LANG_DEFAULT'));
     $lng_L2 = (int)(Configuration::get('EXPRODUCT_NVN_LANG2'));
     $out = '';
     //ini_set("max_execution_time",15);//test
     $secureTime = ini_get("max_execution_time") - 10;
     $startTime = time();
     $nc = $this->ncafields();
     $dat = $this->ncafields();
     unset($dat['CAbegin']); 
     $fname = "nvn_products_export_".Configuration::get('EXPRODUCT_NVN_RND').".xml";		
     $xml_path = dirname(__FILE__)."/download/".$fname ;
     if(file_exists($xml_path))
          {$fp = fopen($xml_path, 'a+');}
          else{
          $out .= '<div class="alerte">'.$this->l('Export Categories. File does not exist. Click again Export Button. ').'</div>';
          $this->nvreset(true);
          return $out; 
           }
           
    $mshop = explode('|',Configuration::get('EXPRODUCT_NVN_MSHOP'));     
    $js1 = "";$filterMshop = ''; 
    if($mshop[0]){
     $js1 = " JOIN `"._DB_PREFIX_."category_shop` AS cs ON cs.`id_category` = ca.`id_category` ";
     $filterMshop = ' AND cs.id_shop = '.$mshop[0]; 
    }
      $result = Db::getInstance()->ExecuteS("SELECT * FROM `"._DB_PREFIX_."category` AS ca 
      INNER  JOIN `"._DB_PREFIX_."category_lang` AS cl ON (cl.id_category=ca.id_category)
      ".$js1.$filterMshop."  
      WHERE cl.id_lang = ".$lng." GROUP BY ca.`id_category` ORDER BY ca.nleft");
      $result_L2 = Db::getInstance()->ExecuteS("SELECT * FROM `"._DB_PREFIX_."category` AS ca 
      INNER  JOIN `"._DB_PREFIX_."category_lang` AS cl ON (cl.id_category=ca.id_category) 
      ".$js1.$filterMshop."
      WHERE cl.id_lang = ".$lng_L2." GROUP BY ca.`id_category` ORDER BY ca.nleft");
    $i=0;$j=0;
    foreach($result as $ca){
      $lastid = (int)(Configuration::get('EXPRODUCT_NVN_CLASTID'));
      $nextid = (int)(Configuration::get('EXPRODUCT_NVN_CNEXTID'));
      if($i==0 AND $ca['name']=='Root' OR $mshop[0]){$j=1;} 
        if($i>$lastid){
          if($nextid == $lastid){ // predchozi prosel
          Configuration::updateValue('EXPRODUCT_NVN_CNEXTID', $i);
          $dat['CAca-ii']=$ca['id_category'];
          $dat['CAca-na_L1']=$ca['name'];  
          $dat['CAca-na_L2']=$result_L2[$i]['name'];
          $dat['CAca-lr_L1']=$ca['link_rewrite'];  
          $dat['CAca-lr_L2']=$result_L2[$i]['link_rewrite'];
          $dat['CAca-de_L1']=$ca['description'];  
          $dat['CAca-de_L2']=$result_L2[$i]['description'];
          $dat['CAca-mt_L1']=$ca['meta_title'];  
          $dat['CAca-mt_L2']=$result_L2[$i]['meta_title'];
          $dat['CAca-mk_L1']=$ca['meta_keywords'];  
          $dat['CAca-mk_L2']=$result_L2[$i]['meta_keywords'];          
          $dat['CAca-md_L1']=$ca['meta_description'];  
          $dat['CAca-md_L2']=$result_L2[$i]['meta_description'];
          $dat['CAca-pa']=$ca['id_parent'];
          $dat['CAca-ac']=$ca['active'];          
          //sleep (10);//test
          $tr='    <'.$nc['CAbegin'].'>'."\r\n";
          foreach($dat as $k=>$v){
            $tr .= '        <'.$nc[$k].'><![CDATA['.$this->jsonEOn($v).']]></'.$nc[$k].'>'."\r\n";
            }
          $tr=$tr.'    </'.$nc['CAbegin'].'>'."\r\n";
          if((int)$ca['level_depth']>$j){fwrite($fp, $tr);}
          
          Configuration::updateValue('EXPRODUCT_NVN_CLASTID', $i);
          Configuration::updateValue('EXPRODUCT_NVN_MUTEX', time());
          if ((time()-$startTime) >  $secureTime){
           fclose($fp);
           $out .= '<div class="alerte">'.$this->l('Exceed ('.$secureTime.'s) Time Limit. Click again Export Button, until the export file will be completed. Latest exported Category: ').$ca['name'].'</div>'; 
           Configuration::updateValue('EXPRODUCT_NVN_MUTEX',0);
           return $out;}  
          }
          else{//neprosel preskoc
               $cfail = (Configuration::get('EXPRODUCT_NVN_CFAIL'));
               $cfail .= ' | '.$ca['id_category'];
               Configuration::updateValue('EXPRODUCT_NVN_CFAIL', $cfail);
               Configuration::updateValue('EXPRODUCT_NVN_CLASTID', $nextid);
            }
        }//i>lastid
 $i++;} 
 fclose($fp);
 Configuration::updateValue('EXPRODUCT_NVN_NEXT',2);
 return $out;     
}
//************************************************************************************************************************
 	public function exportAttributes()
//************************************************************************************************************************    
{
     $d1='|.|';$d2='|:|';$d3='|!|';
     $lng = (int)(Configuration::get('PS_LANG_DEFAULT'));
     $lng_L2 = (int)(Configuration::get('EXPRODUCT_NVN_LANG2'));
     $out = '';
     //ini_set("max_execution_time",15);//test
     $secureTime = ini_get("max_execution_time") - 10;
     $startTime = time();
     $na = $this->nagfields();
     $dat = $this->nagfields();
     unset($dat['GRbegin']);
     $lastid = (int)(Configuration::get('EXPRODUCT_NVN_ALASTID'));
     $fname = "nvn_products_export_".Configuration::get('EXPRODUCT_NVN_RND').".xml";		
     $xml_path = dirname(__FILE__)."/download/".$fname ;
     if($lastid <> -1 AND file_exists($xml_path))
          {$fp = fopen($xml_path, 'a+');}
          else{$fp = fopen($xml_path, 'w+');$lastid = -1;$nextid = -1;}
         if($lastid == -1){//jen poprve
             fwrite($fp, $this->hxml(''));
          }
     $mshop = explode('|',Configuration::get('EXPRODUCT_NVN_MSHOP'));     
         $groupsAttr = AttributeGroup::getAttributesGroups($lng);
         $groupsAttr_L2 = AttributeGroup::getAttributesGroups($lng_L2);
         $i=0;
         foreach($groupsAttr as $gr){
         $lastid = (int)(Configuration::get('EXPRODUCT_NVN_ALASTID'));
         $nextid = (int)(Configuration::get('EXPRODUCT_NVN_ANEXTID'));
         if($i>$lastid){ 
             if($nextid == $lastid){ // predchozi prosel
                   Configuration::updateValue('EXPRODUCT_NVN_ANEXTID', $i);
                   $attrInGr = AttributeGroup::getAttributes($lng,$gr['id_attribute_group']);
                   $attrInGr_L2 = AttributeGroup::getAttributes($lng_L2,$gr['id_attribute_group']);
                   $aval = '';$aval_L2 = '';$aimg = '';$acol = '';
                   $j=0; 
                   foreach($attrInGr as $k=>$v){
                    if($mshop[0]){
                      if($mshop[0] == $v['id_shop']){       
                        $aval .= $v['name'].$d1;
                        $aval_L2 .= $attrInGr_L2[$j]['name'].$d1;
                        $aimg .= $v['id_attribute'].$d1;
                        $acol .= $v['color'].$d1;
                        }
                    }else{
                        $aval .= $v['name'].$d1;
                        $aval_L2 .= $attrInGr_L2[$j]['name'].$d1;
                        $aimg .= $v['id_attribute'].$d1;
                        $acol .= $v['color'].$d1;                        
                    }
                   $j++;}
                   $aval = rtrim($aval,$d1);$aval_L2 = rtrim($aval_L2,$d1);$aimg = rtrim($aimg,$d1);$acol = rtrim($acol,$d1);
                   $dat['GRat-na_L1']=$gr['name'];
                   $dat['GRat-na_L2']=$groupsAttr_L2[$i]['name'];
                   $dat['GRat-pn_L1']=$gr['public_name'];
                   $dat['GRat-pn_L2']=$groupsAttr_L2[$i]['public_name'];           
                   if(array_key_exists('group_type',$gr))
                    {$dat['GRat-ty']=$gr['group_type'];}else{ $dat['GRat-ty']=''; }
                   $dat['GRat-ic']=$gr['is_color_group'];
                   $dat['GRat-va_L1']=$aval;
                   $dat['GRat-va_L2']=$aval_L2;
                   $dat['GRat-id']=$aimg;
                   $dat['GRat-co']=$acol;
                   //sleep (10);//test
                  $tr='    <'.$na['GRbegin'].'>'."\r\n";
                  foreach($dat as $k=>$v){
                    $tr .= '        <'.$na[$k].'><![CDATA['.$this->jsonEOn($v).']]></'.$na[$k].'>'."\r\n";
                    }
                  $tr=$tr.'    </'.$na['GRbegin'].'>'."\r\n";
                  fwrite($fp, $tr);
                  Configuration::updateValue('EXPRODUCT_NVN_ALASTID', $i);
                  Configuration::updateValue('EXPRODUCT_NVN_MUTEX', time());
                  if ((time()-$startTime) >  $secureTime){
                   fclose($fp);
                   $out .= '<div class="alerte">'.$this->l('Exceed ('.$secureTime.'s) Time Limit. Click again Export Button, until the export file will be completed. Latest exported Attribute Group: ').$gr['name'].'</div>'; 
                   Configuration::updateValue('EXPRODUCT_NVN_MUTEX',0);
                   return $out;}
             } // predchozi prosel
            else{//neprosel preskoc
               $afail = (Configuration::get('EXPRODUCT_NVN_AFAIL'));
               $afail .= ' | '.$gr['name'];
               Configuration::updateValue('EXPRODUCT_NVN_AFAIL', $afail);
               Configuration::updateValue('EXPRODUCT_NVN_ALASTID', $nextid);
            }
          }//i>lastid                
    $i++;}
    fclose($fp);
    Configuration::updateValue('EXPRODUCT_NVN_NEXT',1);
    return $out;
} 
//************************************************************************************************************************
 	public function exportProducts()
//************************************************************************************************************************
    {
     $d1='|.|';$d2='|:|';$d3='|!|'; $csvDel=';';
     $lng = (int)(Configuration::get('PS_LANG_DEFAULT'));
     $lng_L2 = (int)(Configuration::get('EXPRODUCT_NVN_LANG2'));
     $out = '';
     //ini_set("max_execution_time",15);//test
     $secureTime = ini_get("max_execution_time") - 10;
     $startTime = time();
     $lastid = (int)(Configuration::get('EXPRODUCT_NVN_PLASTID'));
     $ilimit = (Configuration::get('EXPRODUCT_NVN_LIMIT'));
     $fromid = ' pr.id_product > '.$lastid;
     $incat=Configuration::get('EXPRODUCT_NVN_CATEG');
     $defcat=Configuration::get('EXPRODUCT_NVN_DEFCAT');
     $selcat = Configuration::get('EXPRODUCT_NVN_JSETG');
     $np = $this->nprfields();
     $dat = $this->nprfields();
     unset($dat['SIbegin']);
     $wa1='';$wa2='';$wa3='';$limit='';
     if((int)(Configuration::get('EXPRODUCT_NVN_ACTIVE'))){
      $wa1=' AND pr.active = 1';    
     }
     if(!$selcat AND !empty($incat) AND $defcat){
      $wa2=' AND pr.id_category_default IN ('.$incat.')';   
     }
     if((int)(Configuration::get('EXPRODUCT_NVN_UPDNEW'))){
      $wa3=' AND pr.date_upd > from_unixtime('.self::$lastexportime.')' ;    
     } 
     if($ilimit<>'unlimited'){
     $limit=' LIMIT '.$ilimit;
     }
     $sq = 'SELECT MAX(pr.id_product) FROM '._DB_PREFIX_.'product AS pr
           WHERE pr.id_product > '.$lastid.$wa1.$wa2.$wa3;
     $result = Db::getInstance()->getRow($sq);
     $maxid = $result['MAX(pr.id_product)'];

     $sq = 'SELECT pr.id_product FROM '._DB_PREFIX_.'product AS pr
           WHERE '.$fromid.$wa1.$wa2.$wa3.' ORDER BY pr.id_product ASC'.$limit; 
     $result = Db::getInstance()->ExecuteS($sq);
     $pocet = Db::getInstance()->NumRows(); 
     if ($pocet==0){
        $out .= '<div class="alerte">'.$this->l('Sorry, but there are NO products to export with this setting.').'</div>'; }
      $fname = "nvn_products_export_".Configuration::get('EXPRODUCT_NVN_RND').".xml";		
      $xml_path = dirname(__FILE__)."/download/".$fname ;
      if(file_exists($xml_path))
          {$fp = fopen($xml_path, 'a+');}
          else{
           $out .= '<div class="alerte">'.$this->l('Export Products. File does not exist. Click again Export Button. ').'</div>';
           $this->nvreset(true);
          return $out; 
          }
      foreach ($result as $row){
      $jetam = 1;
      if(!$selcat AND !empty($incat) AND !$defcat){
         $sq = 'SELECT cp.id_category FROM '._DB_PREFIX_.'category_product AS cp
               WHERE cp.id_product = '.$row['id_product'].' AND cp.id_category IN ('.$incat.')'; 
         $result = Db::getInstance()->ExecuteS($sq);
         $jetam = Db::getInstance()->NumRows(); 
       }              
      $lastid = (int)(Configuration::get('EXPRODUCT_NVN_PLASTID'));
      $nextid = (int)(Configuration::get('EXPRODUCT_NVN_PNEXTID'));
       if($nextid == $lastid){ // predchozi prosel

           $nextid = (int)$row['id_product']; //uloz si dalsi
           Configuration::updateValue('EXPRODUCT_NVN_PNEXTID', $nextid);
      
           $myProduct = new Product($row['id_product'],true,$lng);
           $myProduct_L2 = new Product($row['id_product'],true,$lng_L2);
           $imgs = $myProduct->getImages($lng);
           $img=array();$imt=array();$imt_L2=array(); 
           foreach($imgs as $im){
            $sfile = _PS_BASE_URL_.__PS_BASE_URI__.'img/p/'.$this->fimgDir($im['id_image']).$this->fimgName($row['id_product'],$im['id_image']).'.jpg';
               $img[]=$sfile;
               $imt[]=$im['legend']; 
            }
           $imgs = $myProduct_L2->getImages($lng_L2);
           foreach($imgs as $im){$imt_L2[]=$im['legend'];}
           //$this->preprint($myProduct);
           //$ga=$myProduct->getAttributesGroups($lng);
           $gf=$myProduct->getFrontFeatures($lng);
           $cu=$myProduct->getCustomizationFields($lng);
            if (method_exists('Product','getAttributeCombinations')){$ga=$myProduct->getAttributeCombinations($lng);}
            else{$ga=$myProduct->getAttributeCombinaisons($lng);}

           $custfields = array();
           if(!empty($cu)){
            foreach($cu as $k=>$v){
                $custfields[] = $v['type'].$d2.$v['required'].$d2.$v['name'];  
            }
           }
           $features = array();
           foreach($gf as $k=>$v){
                $features[] = $v['name'].$d3.$v['value'];  
           }
           $attribgrs = array();$attribquant = array();$attribprice = array();$attribimgs = array();
           $attribreference = array();$attribsupplierreference = array();$attribean13 = array();
           $attribupc = array();$attribecotax = array();$attribweight = array();$attribisdef = array();
           
           $prodAttrId = 0; $nextProAttr = false;
           foreach($ga as $k=>$v){
                if($prodAttrId <> $v['id_product_attribute'] AND $prodAttrId <> 0){$attribgrs[] = $d1;$nextProAttr = false;}
                $attribgrs[] = $v['group_name'].$d3.$v['attribute_name'];
                if(!$nextProAttr){
                  $attribquant[] = $v['quantity'];
                  $attribprice[] = $v['price'];
                  $attribreference[] = $v['reference'];
                  $attribsupplierreference[] = $v['supplier_reference'];
                  $attribean13[] = $v['ean13'];
                  $attribupc[] = $v['upc'];
                  $attribecotax[] = $v['ecotax'];
                  $attribweight[] = $v['weight'];
                  $attribisdef[] = $v['default_on'];
                  $prodAttrId = $v['id_product_attribute'];
                  $attribimgs[] =$this->getAttrImgs($prodAttrId,$d2);
                  $nextProAttr = true; 
                }
           }
           $tags=array();$t=array();
              $foo = $myProduct->tags;
              if(is_array($foo)){
              if(array_key_exists($lng,$foo)){
                 $t[]=$myProduct->tags[$lng];
                 foreach($t[0] as $c){$tags[]=$c;} 
               }
              }
           $tags_L2=array();$t=array();
              $foo = $myProduct_L2->tags;
              if(is_array($foo)){
              if(array_key_exists($lng_L2,$foo)){
                 $t[]=$myProduct_L2->tags[$lng_L2];
                 foreach($t[0] as $c){$tags_L2[]=$c;} 
               }
              }           
              
          $kat_in=array(); 
          if (method_exists('Product','getProductCategories')){$kategoriein = Product::getProductCategories((int)($row['id_product']));}
            else{//PS<1.4.4
             $kategorieina = Product::getIndexedCategories((int)($row['id_product']));
             foreach($kategorieina as $ca){$kategoriein[] = $ca['id_category'];}
            }
            // $imt_L2=$imt_L2+$imt;//$tags_L2=array();//tagy nejdou indexem
            $dat['SIpr-id']=$row['id_product']; 
            $dat['SIpr-na_L1']=$myProduct->name;
            $dat['SIpr-na_L2']=$myProduct_L2->name;
            $dat['SIpr-dc']=$myProduct->id_category_default;
            $dat['SIpr-in']=implode($d1,$kategoriein);
            $dat['SIpr-im']=implode($d1,$img);
            $dat['SIpr-il_L1']=implode($d1,$imt);
            $dat['SIpr-il_L2']=implode($d1,$imt_L2);
            $dat['SIpr-ma_L1']=$myProduct->manufacturer_name;
            $dat['SIpr-ma_L2']=$myProduct_L2->manufacturer_name;
            $dat['SIpr-su_L1']=$myProduct->supplier_name;
            $dat['SIpr-su_L2']=$myProduct_L2->supplier_name;
            $dat['SIpr-de_L1']=$myProduct->description;
            $dat['SIpr-de_L2']=$myProduct_L2->description;
            $dat['SIpr-sd_L1']=$myProduct->description_short;
            $dat['SIpr-sd_L2']=$myProduct_L2->description_short;
            $dat['SIpr-qa']=$myProduct->quantity;
            $dat['SIpr-an_L1']=$myProduct->available_now;
            $dat['SIpr-an_L2']=$myProduct_L2->available_now;
            $dat['SIpr-al_L1']=$myProduct->available_later;
            $dat['SIpr-al_L2']=$myProduct_L2->available_later;
            $dat['SIpr-co']=$myProduct->condition;
            $dat['SIpr-vi']=$myProduct->visibility;
            $dat['SIpr-oo']=$myProduct->online_only;
            $dat['SIpr-ao']=$myProduct->available_for_order;
            //$dat['SIpr-pr']=Product::getPriceStatic($row['id_product'], false, false);// $myProduct->base_price;//
            $dat['SIpr-pr']=$this->productPriceBase($row['id_product']);
            $dat['SIpr-wp']=$myProduct->wholesale_price;
            $dat['SIpr-tr']=$myProduct->tax_rate;
            $dat['SIpr-et']=$myProduct->ecotax;
            $dat['SIpr-re']=$myProduct->reference;
            $dat['SIpr-sr']=$myProduct->supplier_reference;
            $dat['SIpr-wh']=$myProduct->width;
            $dat['SIpr-ht']=$myProduct->height;
            $dat['SIpr-dh']=$myProduct->depth;
            $dat['SIpr-wt']=$myProduct->weight;
            $dat['SIpr-ea']=$myProduct->ean13;
            $dat['SIpr-up']=$myProduct->upc;       
            $dat['SIpr-lr_L1']=$myProduct->link_rewrite;
            $dat['SIpr-lr_L2']=$myProduct_L2->link_rewrite;
            $dat['SIpr-md_L1']=$myProduct->meta_description;
            $dat['SIpr-md_L2']=$myProduct_L2->meta_description;
            $dat['SIpr-mk_L1']=$myProduct->meta_keywords;
            $dat['SIpr-mk_L2']=$myProduct_L2->meta_keywords;
            $dat['SIpr-mt_L1']=$myProduct->meta_title;
            $dat['SIpr-mt_L2']=$myProduct_L2->meta_title;
            $dat['SIpr-ac']=$myProduct->active;        
            $dat['SIpr-tg_L1']=implode($d1,$tags);
            $dat['SIpr-tg_L2']=implode($d1,$tags_L2);
            $dat['SIat-gr']=implode($d2,$attribgrs);
            $dat['SIat-qu']=implode($d1,$attribquant);
            $dat['SIat-pr']=implode($d1,$attribprice);
            $dat['SIat-re']=implode($d1,$attribreference);
            $dat['SIat-sr']=implode($d1,$attribsupplierreference);
            $dat['SIat-ea']=implode($d1,$attribean13);
            $dat['SIat-up']=implode($d1,$attribupc);
            $dat['SIat-et']=implode($d1,$attribecotax);
            $dat['SIat-wt']=implode($d1,$attribweight);
            $dat['SIat-de']=implode($d1,$attribisdef);
            $dat['SIat-im']=implode($d1,$attribimgs);
            $dat['SIpr-cu']=implode($d1,$custfields);
            $dat['SIpr-fe']=implode($d1,$features);
            $tr='    <'.$np['SIbegin'].'>'."\r\n";
             foreach($dat as $k=>$v){
                 $tr .= '        <'.$np[$k].'><![CDATA['.$this->jsonEOn($v).']]></'.$np[$k].'>'."\r\n";
             }
             $tr=$tr.'    </'.$np['SIbegin'].'>'."\r\n";
          if($jetam>0){fwrite($fp, $tr);}
          Configuration::updateValue('EXPRODUCT_NVN_PLASTID', (int)$row['id_product']); 
          Configuration::updateValue('EXPRODUCT_NVN_MUTEX', time());   
          if ((time()-$startTime) >  $secureTime){
              $out .= '<div class="alerte">'.$this->l('Exceed ('.$secureTime.'s) Time Limit. Click again Export Button, until the export file will be completed. Latest exported Product ID is ').(int)$row['id_product'].'</div>'; 
              Configuration::updateValue('EXPRODUCT_NVN_MUTEX', 0);
              return $out; 
              } 
        } // predchozi prosel
        else{//neprosel preskoc
           $pfail = (Configuration::get('EXPRODUCT_NVN_PFAIL'));
           $pfail .= ' | '.$nextid;
           Configuration::updateValue('EXPRODUCT_NVN_PFAIL', $pfail);
           Configuration::updateValue('EXPRODUCT_NVN_PLASTID', $nextid);
        }
           
       }
     if(isset($row['id_product'])){  
      if ((int)$row['id_product']<$maxid){
          $out .= '<div class="alerte">'.$this->l('Exceed count('.$ilimit.') products per cycle limit. Click again Export Button, until the export file will be completed. Latest exported Product ID is ').(int)$row['id_product'].'</div>'; 
          return $out;}
     }      
     fwrite($fp,$this->fxml);   
     //Configuration::updateValue('EXPRODUCT_NVN_PLASTID', -1); //hotovo snad 
    $this->nvreset(false);
      if(file_exists($xml_path)){
         $out .= '<div class="confok"><img src="../img/admin/ok.gif" alt="'.$this->l('EXPORT OK').'" />'.$this->l('For use in nvn_import_products module no need to download. It will be downloaded when you import. For Your use download here:')
               .'<div style="background:#99ff33; width:800px; height:20px; padding:5px; text-align:left;">
                   <a style=" color:#002200"  
                      href="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/nvn_export_products/download/'.$fname.'" target="_blank">'._PS_BASE_URL_.__PS_BASE_URI__.'modules/nvn_export_products/download/'.$fname
                   .'</a>
				 </div>
                </div>';   
      }
      else {
        $out .= '<div class="alerte">'.$this->l('Sorry, but something is wrong with output file: ').'</div>'. $csv_path;  
      }
      fclose($fp);
      $this->nvreset(false); 
      return $out;
    }
//************************************************************************************************************************
    private function nvreset($e)
//************************************************************************************************************************    
{
       Configuration::updateValue('EXPRODUCT_NVN_ALASTID', -1);
       Configuration::updateValue('EXPRODUCT_NVN_ANEXTID', -1);
       Configuration::updateValue('EXPRODUCT_NVN_CLASTID', -1);
       Configuration::updateValue('EXPRODUCT_NVN_CNEXTID', -1);
       Configuration::updateValue('EXPRODUCT_NVN_PLASTID', -1);
       Configuration::updateValue('EXPRODUCT_NVN_PNEXTID', -1); 
       if($e){                             
        Configuration::updateValue('EXPRODUCT_NVN_AFAIL','');
        Configuration::updateValue('EXPRODUCT_NVN_CFAIL','');
        Configuration::updateValue('EXPRODUCT_NVN_PFAIL','');
       }
       Configuration::updateValue('EXPRODUCT_NVN_NEXT',0);
       Configuration::updateValue('EXPRODUCT_NVN_MUTEX', 0);    
}
//------------------------------------------------------------------------------------------------------------------------
    public function jsonEOn($txt)
//------------------------------------------------------------------------------------------------------------------------
{
  if($this->js_on){return json_encode($txt);}
  return $txt;   
}
//************************************************************************************************************************
    private function hxml($chu)
//************************************************************************************************************************
    {if (empty($chu)){ $chu = "UTF-8";}
     (Configuration::get('EXPRODUCT_NVN_JSON')) ? $json="ON" : $json="OFF"; 
     return '<?xml version="1.0" encoding="'.$chu.'"?>'."\r\n".'<nvn_export_products JSON="'.$json.'" EXPDATE="'.Time().'">'."\r\n";
    }   
    private $fxml='</nvn_export_products>'; 
//************************************************************************************************************************
    private function generateRandomString($length = 10) 
//************************************************************************************************************************
   {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

//************************************************************************************************************************
    private function getAttrImgs($idattr,$d2)
//************************************************************************************************************************
    {
    $result = Db::getInstance()->ExecuteS("SELECT `id_image` FROM `"._DB_PREFIX_."product_attribute_image` WHERE `id_product_attribute` = ".$idattr);
    $ids = ''; $d='';
    foreach($result as $res)
     {$ids .= $d.$res['id_image'];$d=$d2;}
    return $ids;
    }
    
//************************************************************************************************************************
    private function productPriceBase($searchId) 
//************************************************************************************************************************
    {
    $result = Db::getInstance()->getRow("SELECT `price` FROM `"._DB_PREFIX_."product` WHERE `id_product` = ".$searchId);
    if (Db::getInstance()->NumRows() > 0){return $result['price'];}
    else  {return false;}    
    }
//************************************************************************************************************************
    private function preprint($s, $return=false) //for testing only
//************************************************************************************************************************
    { 
        $x = "<pre>"; 
        $x .= print_r($s, 1); 
        $x .= "</pre>"; 
        if ($return) return $x; 
        else print $x; 
    }
//************************************************************************************************************************
    private function fimgName($productid,$imageid)
//************************************************************************************************************************
  {
    $legacy = Configuration::get('PS_LEGACY_IMAGES'); 
    if ($legacy == 1 OR $legacy == ""){ 
    return $productid."-".$imageid;
    }
    else {  
    return $imageid;
    }
  } 
//************************************************************************************************************************
    private function fimgDir($imageid)
//************************************************************************************************************************
  {  // vraci jen adresar
    $legacy = Configuration::get('PS_LEGACY_IMAGES');
    if ($legacy == 1 OR $legacy == ""){ // stara verze  product ID - image ID
    return "";
    }
    else {  // nova verze image ID img/p/1/5/15.jpg   _PS_PROD_IMG_DIR_ jiz obsahuje /
    return chunk_split($imageid, 1, "/");
    }
  }
//************************************************************************************************************************
 	private function versionPS()
//************************************************************************************************************************
    { 
      $version_mask = explode('.', _PS_VERSION_);
      if( $version_mask[0]  < 1){return 0;} 
      if( $version_mask[1] == 5){return 5;}
      if( $version_mask[1] == 6){return 6;}
      if( $version_mask[1] == 4){return 4;}
    }   
//************************************************************************************************************************
	private function file_get_contents_curl($url) 
//************************************************************************************************************************
    {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
    $data = curl_exec($ch);
    curl_close($ch);
    if(substr($data,0,9)=='<!DOCTYPE'){$data='';}//to jsem rozhodne nechtel
    return $data;
}
 private function limitF()
    { return array('unlimited','100','500','1000','2000');}

 private function limitT()
    { return array('48','24','16','8','4','2','1','0');}    

    
//************************************************************************************************************************
    private function nagfields()
//************************************************************************************************************************
    { return array(
'GRbegin'=>'ATRIBUTE_GR',
    'GRat-na_L1'=>'ATRIBUTE_NAME_L1',
    'GRat-na_L2'=>'ATRIBUTE_NAME_L2',
    'GRat-pn_L1'=>'ATRIBUTE_PUBLIC_NAME_L1',
    'GRat-pn_L2'=>'ATRIBUTE_PUBLIC_NAME_L2',
    'GRat-ty'=>'GROUP_TYPE',
    'GRat-ic'=>'IS_COLOR',
    'GRat-va_L1'=>'ATRIBUTE_VALUES_L1',
    'GRat-va_L2'=>'ATRIBUTE_VALUES_L2',
    'GRat-id'=>'ATRIBUTE_IMG_IDS',
    'GRat-co'=>'ATRIBUTE_COLORS'
    );}
//************************************************************************************************************************
    private function ncafields()
//************************************************************************************************************************
    { return array(
'CAbegin'=>'CATEGORIES',
    'CAca-ii'=>'CATEGORY_IMG_IDS',
    'CAca-na_L1'=>'CATEGORY_NAME_L1',
    'CAca-na_L2'=>'CATEGORY_NAME_L2',
    'CAca-lr_L1'=>'CATEGORY_LINKREWRITE_L1',
    'CAca-lr_L2'=>'CATEGORY_LINKREWRITE_L2',
    'CAca-de_L1'=>'CATEGORY_DESCRIPTION_L1',
    'CAca-de_L2'=>'CATEGORY_DESCRIPTION_L2',
    'CAca-mt_L1'=>'CATEGORY_META_TITLE_L1',
    'CAca-mt_L2'=>'CATEGORY_META_TITLE_L2',      
    'CAca-mk_L1'=>'CATEGORY_META_KEY_L1',
    'CAca-mk_L2'=>'CATEGORY_META_KEY_L2',   
    'CAca-md_L1'=>'CATEGORY_META_DESCR_L1',
    'CAca-md_L2'=>'CATEGORY_META_DESCR_L2',
    'CAca-pa'=>'CATEGORY_PARENT',
    'CAca-ac'=>'CATEGORY_ACTIVE' 
    );}    
    
    
//************************************************************************************************************************
    private function nprfields()
//************************************************************************************************************************
    { return array(
'SIbegin'=>'SHOPITEM',
    'SIpr-id'=>'PRODUCT_ID',
    'SIpr-na_L1'=>'PRODUCT_NAME_L1',
    'SIpr-na_L2'=>'PRODUCT_NAME_L2',
    'SIpr-dc'=>'CATEGORY_DEFAULT',
    'SIpr-in'=>'IN_CATEGORIES',
    'SIpr-im'=>'IMAGES',
    'SIpr-il_L1'=>'IMAGES_LEGEND_L1',
    'SIpr-il_L2'=>'IMAGES_LEGEND_L2',
    'SIpr-ma_L1'=>'MANUFACTURER_NAME_L1',
    'SIpr-ma_L2'=>'MANUFACTURER_NAME_L2',
    'SIpr-su_L1'=>'SUPPLIER_NAME_L1',
    'SIpr-su_L2'=>'SUPPLIER_NAME_L2',
    'SIpr-de_L1'=>'DESCRIPTION_L1',
    'SIpr-de_L2'=>'DESCRIPTION_L2',
    'SIpr-sd_L1'=>'DESCRIPTION_SHORT_L1',
    'SIpr-sd_L2'=>'DESCRIPTION_SHORT_L2',
    'SIpr-qa'=>'QUANTITY',
    'SIpr-an_L1'=>'AVAILABLE_NOW_L1',
    'SIpr-an_L2'=>'AVAILABLE_NOW_L2',
    'SIpr-al_L1'=>'AVAILABLE_LATER_L1',
    'SIpr-al_L2'=>'AVAILABLE_LATER_L2',
    'SIpr-co'=>'CONDITION',
    'SIpr-vi'=>'VISIBILITY',
    'SIpr-oo'=>'ONLINE_ONLY',
    'SIpr-ao'=>'AVAILABLE_FOR_ORDER',
    'SIpr-pr'=>'PRICE',
    'SIpr-wp'=>'WHOLESALE_PRICE',
    'SIpr-tr'=>'TAX_RATE',
    'SIpr-et'=>'ECOTAX',
    'SIpr-re'=>'REFERENCE',
    'SIpr-sr'=>'SUPPLIER_REFERENCE',
    'SIpr-wh'=>'WIDTH',
    'SIpr-ht'=>'HEIGHT',
    'SIpr-dh'=>'DEPTH',
    'SIpr-wt'=>'WEIGHT',
    'SIpr-ea'=>'EAN13',
    'SIpr-up'=>'UPC',
    'SIpr-lr_L1'=>'LINK_REWRITE_L1',
    'SIpr-lr_L2'=>'LINK_REWRITE_L2',
    'SIpr-md_L1'=>'META_DESCRIPTION_L1',
    'SIpr-md_L2'=>'META_DESCRIPTION_L2',
    'SIpr-mk_L1'=>'META_KEYWORDS_L1',
    'SIpr-mk_L2'=>'META_KEYWORDS_L2',
    'SIpr-mt_L1'=>'META_TITLE_L1',
    'SIpr-mt_L2'=>'META_TITLE_L2', 
    'SIpr-ac'=>'ACTIVE',
    'SIpr-tg_L1'=>'TAGS_L1',
    'SIpr-tg_L2'=>'TAGS_L2',
    'SIat-gr'=>'ATTRIB_GROUPS',
    'SIat-qu'=>'ATTRIB_QUANT',
    'SIat-pr'=>'ATTRIB_PRICE',
    'SIat-re'=>'ATTRIB_REFERENCE',
    'SIat-sr'=>'ATTRIB_SUPPLIERREFERENCE',
    'SIat-ea'=>'ATTRIB_EAN13',
    'SIat-up'=>'ATTRIB_UPC',
    'SIat-et'=>'ATTRIB_ECOTAX',
    'SIat-wt'=>'ATTRIB_WEIGHT',
    'SIat-de'=>'ATTRIB_ISDEF',
    'SIat-im'=>'ATTRIB_IMGIDS',
    'SIpr-cu'=>'CUSTOM_FIELDS',
    'SIpr-fe'=>'FEATURES'
    );}         
 }
?>