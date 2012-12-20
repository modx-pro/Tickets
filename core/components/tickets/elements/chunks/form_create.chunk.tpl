<form class="well" method="post" action="[[~[[*id]]]]" id="ticketForm">
	<div id="ticket-preview-placeholder"></div>

	<select name="parent" class="input-xxlarge">[[+sections]]</select>
	<span class="error">[[+error.parent]]</span>
	<br/>
	<input type="text" class="input-xxlarge" placeholder="Заголовок" name="pagetitle" value="[[+pagetitle]]" maxlength="50"/>
	<span class="error">[[+error.pagetitle]]</span>
	<br/>
	<textarea class="input-xxlarge" placeholder="Опишите вашу проблему" name="content" id="ticket-editor" rows="20">[[+content]]</textarea>
	<span class="error">[[+error.content]]</span>

	<div class="form-actions">
		<input type="hidden" name="action" value="saveTicket" />
		<input type="button" class="btn" value="Предпросмотр" onclick="Tickets.ticket.preview(this.form, this)"/>
		<input type="submit" class="btn btn-primary" value="Отправить" />&nbsp;&nbsp;
		<label class="checkbox"><input type="checkbox" name="published" value="1" [[+published:is=`1`:then=`checked`]] /> Опубликовать?</label>
	</div>
</form>
[[+error:notempty=`<div class="alert alert-block alert-error">[[+error]]</div>`]]