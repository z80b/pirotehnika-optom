<script type="text/javascript">
$('document').ready(function(){
	$('#iex').click(function() {
		$('#exreg').slideToggle();
	});
});
</script>




<fieldset class="account_creation">

	<h3>{l s='Registration as person' mod='exreg'}</h3>
	<p class="checkbox">
		<input type="checkbox" name="iex" id="iex" value="1" {if isset($smarty.post.iex)}checked="checked"{/if} />
		<label for="iex">{l s='Registration as person.' mod='exreg'}</label>
	</p>

	<div id="exreg" class="{if !$smarty.post.iex}unvisible{/if}">
	<div class="required form-group">
		<label for="org_name" class="required">{l s='Name' mod='exreg'} </label>
		<input type="text" class="is_required validate form-control" data-validate="isGenericName" id="org_name" name="org_name" value="{if isset($smarty.post.org_name)}{$smarty.post.org_name|escape:'htmlall':'UTF-8'}{/if}" />
	</div>
		<div class="checkbox">
			<label for="isnds" class="required form-group">
			<input type="checkbox" name="isnds" id="isnds" value="1" {if isset($company.isnds) && $company.isnds == '1'}checked="checked"{/if} />
			Общая система налогооблажения (с НДС)</label>
		</div>
	<div class="required form-group">
		<label for="inn" class="required">{l s='INN' mod='exreg'}</label>
		<input type="text" class="is_required validate form-control" data-validate="isGenericName" id="inn" name="inn" value="{if isset($smarty.post.inn)}{$smarty.post.inn|escape:'htmlall':'UTF-8'}{/if}" />
	</div>
	<div class="required form-group">
		<label for="kpp" class="required">{l s='KPP' mod='exreg'}</label>
		<input type="text" class="is_required validate form-control" data-validate="isGenericName" id="kpp" name="kpp" value="{if isset($smarty.post.kpp)}{$smarty.post.kpp|escape:'htmlall':'UTF-8'}{/if}" />
	</div>
	<div class="required form-group">
		<label for="org_ur_addr" class="required">{l s='Legal address' mod='exreg'}</label>
		<input type="text" class="is_required validate form-control" data-validate="isAddress" id="org_ur_addr" name="org_ur_addr" value="{if isset($smarty.post.org_ur_addr)}{$smarty.post.org_ur_addr|escape:'htmlall':'UTF-8'}{/if}" />
	</div>
	<div class="required form-group">
		<label for="org_post_addr" class="required">{l s='Postal address' mod='exreg'}</label>
		<input type="text" class="is_required validate form-control" data-validate="isAddress" id="org_post_addr" name="org_post_addr" value="{if isset($smarty.post.org_post_addr)}{$smarty.post.org_post_addr|escape:'htmlall':'UTF-8'}{/if}" />
	</div>
	<div class="required form-group">
		<label for="rs" class="required">{l s='R/S' mod='exreg'}</label>
		<input type="text" class="is_required validate form-control" data-validate="isGenericName" id="rs" name="rs" value="{if isset($smarty.post.rs)}{$smarty.post.rs|escape:'htmlall':'UTF-8'}{/if}" />
	</div>
	<div class="required form-group">
		<label for="bank" class="required">{l s='Bank' mod='exreg'}</label>
		<input type="text" class="is_required validate form-control" data-validate="isGenericName" id="bank" name="bank" value="{if isset($smarty.post.bank)}{$smarty.post.bank|escape:'htmlall':'UTF-8'}{/if}" />
	</div>
	<div class="required form-group">
		<label for="bik" class="required">{l s='BIK' mod='exreg'}</label>
		<input type="text" class="is_required validate form-control" data-validate="isGenericName" id="bik" name="bik" value="{if isset($smarty.post.bik)}{$smarty.post.bik|escape:'htmlall':'UTF-8'}{/if}" />
	</div>
	</div>
			<br>
			<label style="color:red">{l s='Attention!' mod='exreg'}</label>
			<br>
			<label style="color:red">{l s='You will be able to log in to your account and make purchases after you confirm your registration by our staff. You will receive a notice on Your mail.' mod='exreg'}</label>
			<br>
	
</fieldset>