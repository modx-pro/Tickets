<form class="well" method="post" action="[[~[[*id]]]]" id="ticketForm">
	<input type="text" class="pagetitle" placeholder="Заголовок" name="pagetitle" value="[[+pagetitle]]" maxlength="50"/>
	<span class="error">[[+error.pagetitle]]</span>

	<br/>
	<textarea class="content" placeholder="Текст вопроса" name="content" id="editor" />[[+content]]</textarea>
	<span class="error">[[+error.content]]</span>

	<br/>
	<input type="hidden" name="action" value="createTicket" />
	<button class="btn" id="getPreview">Предпросмотр</button>
	<button type="submit" class="btn">Отправить</button>
</form>
[[+error:notempty=`<div class="alert alert-block alert-error">[[+error]]</div>`]]