<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="{$comments_dir}">
        <img alt="Brand" src="{$module_dir}logo3.png"> Отзывы о сайте
      </a>
    </div>
  </div>
</nav>
<div id="wnsitecomments_block" class="block">
	
	 {if isset($comments) && $comments}
	<div class="block_content comments">
	
		{foreach from=$comments item=link}
		
		<strong>
		{$link.title}{$link.name}
		</strong>
		<p>
		{$link.post|strip_tags:'UTF-8'|truncate:105:'...'} {$link.message|strip_tags:'UTF-8'|truncate:105:'...'}
		<br>
		<strong style="color:#5bc0de">{$link.date|date_format:"%D"}</strong></p>
		{/foreach}
		
	</div>
        
	{/if}
	<strong style='text-align:center'><a href="{$comments_dir}">
		<button type="submit" class="btn btn-success">{l mod='wn_site_comments' s='Оставить свой отзыв'}</button>
		</a></strong>	
</div>
