{if isset($orderby) AND isset($orderway)}
	<ul id="displayType" class="display ps-sort__display-type" data-display="list">
		<!-- <li class="display-title">{l s='View:'}</li> -->
		<li class="display-title" style="text-align: right" >Способ отображения:</li>
		<li id="grid"><a rel="nofollow" href="#" title="{l s='Grid'}"><i class="fa fa-th-large" aria-hidden="true"></i></a></li>
		<li id="list"><a rel="nofollow" href="#" title="{l s='List'}"><i class="fa fa-list" aria-hidden="true"></i></a></li>
		<li id="tab"><a rel="nofollow" href="#" title="{l s='Tab'}"><i class="fa fa-table" aria-hidden="true"></i></a></li>
		<li id="extend"><a rel="nofollow" href="#" title="{l s='Extended'}"><i class="fa fa-tachometer" aria-hidden="true"></i></a></li>
	</ul>
	{if !isset($request)}
		<!-- Sort products -->
		{if isset($smarty.get.id_category) && $smarty.get.id_category}
			{assign var='request' value=$link->getPaginationLink('category', $category, false, true)
	}	{elseif isset($smarty.get.id_manufacturer) && $smarty.get.id_manufacturer}
			{assign var='request' value=$link->getPaginationLink('manufacturer', $manufacturer, false, true)}
		{elseif isset($smarty.get.id_supplier) && $smarty.get.id_supplier}
			{assign var='request' value=$link->getPaginationLink('supplier', $supplier, false, true)}
		{else}
			{assign var='request' value=$link->getPaginationLink(false, false, false, true)}
		{/if}
	{/if}
{/if}