<script type="text/javascript" src="[[+assetsUrl]]js/web/tickets.js"></script>
<div id="preview"></div>
<form class="well" method="post" action="[[~[[*id]]]]" id="ticketForm">
	<select name="parent" class="input-xxlarge">
		[[+sections]]
	</select>
	<span class="error">[[+error.section]]</span>

	<br/>
	<input type="text" class="input-xxlarge" placeholder="Заголовок" name="pagetitle" value="[[+pagetitle]]" maxlength="50"/>
	<span class="error">[[+error.pagetitle]]</span>

	<br/>
	<textarea class="input-xxlarge" placeholder="Опишите вашу проблему" name="content" id="editor" rows="20">[[+content]]</textarea>
	<span class="error">[[+error.content]]</span>

	<br/>
	<input type="hidden" name="action" value="saveTicket" />
	<button class="btn" id="previewTicket">Предпросмотр</button>
	<button type="submit" class="btn">Отправить</button>
</form>
[[+error:notempty=`<div class="alert alert-block alert-error">[[+error]]</div>`]]