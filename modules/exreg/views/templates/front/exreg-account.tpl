{capture name=path}<a href="{$link->getPageLink('my-account', true)}">{l s='My account' mod='exreg'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Company details' mod='exreg'}{/capture}

<div id="address" class="box">
<h2>{l s='Company details' mod='exreg'}</h2>
{if isset($confirmation) && $confirmation}
<div class="alert alert-success">
	{l s='Company details has been successfully updated.' mod='exreg'}
</div>
{/if}


	<p class="required">
		<sup>*</sup>Обязательные поля
	</p>
	<form class="std" action="{$link->getModuleLink('exreg', 'account')}" method="post">
	<fieldset>
		<div class="required form-group">
			<label for="org_name" class="required">{l s='Name' mod='exreg'}</label>
			<input class="is_required validate form-control" type="text" id="org_name" name="org_name" value="{$company.org_name|escape:'html':'UTF-8'}" />
		</div>
		<div class="checkbox">
			<label for="isnds" class="required form-group">
			<input type="checkbox" name="isnds" id="isnds" value="1" {if isset($company.isnds) && $company.isnds == '1'}checked="checked"{/if} />
			Общая система налогооблажения (с НДС)</label>
		</div>
		<div class="required form-group">
			<label for="inn" class="required">{l s='INN' mod='exreg'}</label>
			<input type="text" class="is_required validate form-control" id="inn" name="inn" value="{$company.inn|escape:'html':'UTF-8'}" />
		</div>
		<div class="required form-group">
			<label for="kpp" class="required">{l s='KPP' mod='exreg'}</label>
			<input type="text" class="is_required validate form-control" id="kpp" name="kpp" value="{$company.kpp|escape:'html':'UTF-8'}" />
		</div>
		<div class="required form-group">
			<label for="org_ur_addr" class="required">{l s='Legal address' mod='exreg'}</label>
			<input type="text" class="is_required validate form-control" id="org_ur_addr" name="org_ur_addr" value="{$company.org_ur_addr|escape:'html':'UTF-8'}" />
		</div>
		<div class="required form-group">
			<label for="org_post_addr" class="required">{l s='Postal address' mod='exreg'}</label>
			<input type="text" class="is_required validate form-control" id="org_post_addr" name="org_post_addr" value="{$company.org_post_addr|escape:'html':'UTF-8'}" />
		</div>
		<div class="required form-group">
			<label for="rs" class="required">{l s='R/S' mod='exreg'}</label>
			<input type="text" class="is_required validate form-control" id="rs" name="rs" value="{$company.rs|escape:'html':'UTF-8'}" />
		</div>
		<div class="required form-group">
			<label for="bank" class="required">{l s='Bank' mod='exreg'}</label>
			<input type="text" class="is_required validate form-control" id="bank" name="bank" value="{$company.bank|escape:'html':'UTF-8'}" />
		</div>
		<div class="required form-group">
			<label for="bik" class="required">{l s='BIK' mod='exreg'}</label>
			<input type="text" class="is_required validate form-control" id="bik" name="bik" value="{$company.bik|escape:'html':'UTF-8'}" />
		</div>
	</fieldset>


	<br />
	<div class="form-group">
	<button class="btn btn-default button button-medium" name="submitCompany" type="submit">
	<span>{l s='Save information' mod='exreg'}<i class="icon-chevron-right right"></i></span>
	</button>
	</div>
	</form>
	
</div>
