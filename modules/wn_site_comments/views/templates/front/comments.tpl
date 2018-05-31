

<div class='block_content' >
<center><a href="javascript:;" onclick="$('#comm_ask').toggle();return false;" style=""><button type="button" class="btn btn-primary">{l mod='wn_site_comments' s='Оставить отзыв'}</button></a></center>
<div class="row"></div>
<form action="" method="POST" id="comm_ask" style="display:none;">
<div class="row">
            <div class="form-group col-sm-4">
                <label for="comm_name" class="h4">{l mod='wn_site_comments' s='Имя:'}</label>
                <input type="text" class="form-control" name="comm_name" id="comm_name" placeholder="{l mod='wn_site_comments' s='Введите имя:'}" required>
            </div>
            <div class="form-group col-sm-4">
                <label for="comm_email" class="h4">{l mod='wn_site_comments' s='E-mail:'}</label>
				<div class="input-group">
				 <span class="input-group-addon">@</span>
                <input type="email" class="form-control" name="comm_email" id="comm_email" placeholder="{l mod='wn_site_comments' s='Введите E-mail:'}" required>
				</div>
            </div>
        </div>
<div class="row">		
        <div class="form-group col-sm-8">
            <label for="comm_q" class="h4 ">{l mod='wn_site_comments' s='Комментарии:'} </label>
            <textarea id="comm_q" name="comm_q" class="form-control " rows="5"  placeholder="{l mod='wn_site_comments' s='Ваш комментарий:'} " required></textarea>
        </div>
		</div>
		<div class="row">	
		<div class="form-group col-sm-4">
                <label for="comm_cap" class="h4">{l mod='wn_site_comments' s='Любое число от 2 до 5'}</label>
                <input type="text" class="form-control" name="comm_cap" id="comm_cap" placeholder="{l mod='wn_site_comments' s='Может быть 8?'}" required>
            </div>
			</div>
        <button type="submit" id="submitcomm" title="{l mod='wn_site_comments' s='Send comment!'}" class="btn btn-success btn-lg pull-left ">{l mod='wn_site_comments' s='Добавить'}</button>
<div id="msgSubmit" class="h3 text-center hidden">Message Submitted!</div>
</form>	
<div  class="row"></div>
<!-- content -->
<p><center><a href="#" id="prev" class="btn btn-default">{l mod='wn_site_comments' s=' << Назад'}</a>
<span id="total"></span>
<a href="#" id="next" class="btn btn-default">{l mod='wn_site_comments' s=' Вперед >>'}</a></center></p>
<br />


<div class="page-header" id="page-header"></div>

{literal}<script>
{/literal}gravatar = '<img src="http://www.gravatar.com/avatar/{$gravatar}?s=36&d=wavatar"/>'{literal}	;
window.onload = loadJSON();
</script>{/literal}	
<script>
	var comm_error = "{l mod='wn_site_comments' s='Упс! Кажется, что ваш запрос не может быть подтвержден, повторите попытку' mod='wn_site_comments'}",
		comm_badcontent = "{l mod='wn_site_comments' s='Неверное содержание сообщения!' mod='wn_site_comments'}",
		comm_badname = "{l mod='wn_site_comments' s='Неверное имя!' mod='wn_site_comments'}",
		comm_bademail = "{l mod='wn_site_comments' s='неверный E-mail!' mod='wn_site_comments'}",
		comm_confirm = "{l mod='wn_site_comments' s='Спасибо за Ваш отзыв .' mod='wn_site_comments'}";

</script>	
	
</div>
