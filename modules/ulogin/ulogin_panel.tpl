<form id="ulogin" class="std">
<h3>{l s='Enter from social networks'  mod='ulogin'}</h3>

<fieldset>

  <div id="uLogin_p" x-ulogin-params="display=panel&fields=first_name,last_name,email,sex&optional=bdate&providers={if $providers_set}{$providers_set}{else}vkontakte,odnoklassniki,mailru,facebook{/if}&hidden={if $providers_sub}{$providers_sub}{else}other{/if}&redirect_uri={$link->getPageLink('authentication.php')|escape:'url'}"></div>
  <br>

</fieldset>
</form>
