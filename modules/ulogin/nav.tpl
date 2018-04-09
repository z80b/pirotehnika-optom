<!-- ulogin module NAV  -->
{if !$logged}
<script src="//ulogin.ru/js/ulogin.js"></script>
<div class="ulogin-header">
<div id="uLogin" data-ulogin="display=small;fields=first_name,last_name,email;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=other;redirect_uri={$link->getPageLink('authentication.php')|escape:'url'}">
</div>
</div>
{/if}

<!-- /ulogin module NAV -->
