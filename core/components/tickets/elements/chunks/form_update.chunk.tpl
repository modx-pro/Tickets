<script type="text/javascript" src="[[+assetsUrl]]js/web/tickets.js"></script>
<div id="preview"></div>
<form class="well" method="post" action="[[~[[*id]]]]" id="ticketForm">
	<select name="section" class="input-xxlarge">
		<option value="0" disabled selected>Выберите раздел для тикета</option>
		[[+sections]]
	</select>
	<span class="error">[[+error.section]]</span>

	<br/>
	<input type="text" class="input-xxlarge" placeholder="Заголовок" name="pagetitle" value="[[+pagetitle]]" maxlength="50"/>
	<span class="error">[[+error.pagetitle]]</span>

	<br/>
	<textarea class="input-xxlarge" placeholder="Текст вопроса" name="content" id="editor" rows="20">[[+content]]</textarea>
	<span class="error">[[+error.content]]</span>

	<br/>
	<input type="hidden" class="input-xxlarge" name="action" value="saveTicket" />
	<button class="btn" id="previewTicket">Предпросмотр</button>
	<button type="submit" class="btn">Отправить</button>
</form>
[[+error:notempty=`<div class="alert alert-block alert-error">[[+error]]</div>`]]