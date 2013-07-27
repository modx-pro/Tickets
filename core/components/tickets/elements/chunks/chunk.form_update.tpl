<form class="well" method="post" action="" id="ticketForm">
	<div id="ticket-preview-placeholder"></div>

	<label for="ticket-sections">
		<select name="parent" class="input-xxlarge" id="ticket-sections">[[+sections]]</select>
		<span class="error"></span>
	</label>

	<label for="ticket-pagetitle">
		<input type="text" class="input-xxlarge" placeholder="[[%ticket_pagetitle]]" name="pagetitle" value="[[+pagetitle]]" maxlength="50" id="ticket-pagetitle"/>
		<span class="error"></span>
	</label>

	<label for="ticket-editor">
		<textarea class="input-xxlarge" placeholder="[[%ticket_content]]" name="content" id="ticket-editor" rows="20"></textarea>
		<span class="error"></span>
	</label>

	<div class="form-actions">
		<input type="hidden" name="tid" value="[[+id]]" />
		<input type="button" class="btn" value="[[%ticket_preview]]" onclick="Tickets.ticket.preview(this.form, this);"/>
		<input type="submit" class="btn btn-primary" value="[[%ticket_save]]" />&nbsp;&nbsp;
		<label class="checkbox" for="ticket-publish"><input type="checkbox" name="published" value="1" [[+published:is=`1`:then=`checked`]] id="ticket-publish" /> [[%ticket_publish]]</label>
	</div>
</form>