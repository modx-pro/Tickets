<form class="well" method="post" action="" id="ticketForm">
	<div id="ticket-preview-placeholder"></div>

	<div class="form-group">
		<label for="ticket-sections">[[%tickets_section]]</label>
		<select name="parent" class="form-control" id="ticket-sections">[[+sections]]</select>
		<span class="error"></span>
	</div>

	<div class="form-group">
		<label for="ticket-pagetitle">[[%ticket_pagetitle]]</label>
		<input type="text" class="form-control" placeholder="[[%ticket_pagetitle]]" name="pagetitle" value="[[+pagetitle]]" maxlength="50" id="ticket-pagetitle"/>
		<span class="error"></span>
	</div>

	<div class="form-group">
		<textarea class="form-control" placeholder="[[%ticket_content]]" name="content" id="ticket-editor" rows="10">[[+content]]</textarea>
		<span class="error"></span>
	</div>

	<div class="form-actions">
		<input type="hidden" name="tid" value="[[+id]]" />
		<input type="button" class="btn btn-default" value="[[%ticket_preview]]" onclick="return Tickets.ticket.preview(this.form, this);"/>
		<input type="submit" class="btn btn-primary" value="[[%ticket_save]]" />&nbsp;&nbsp;
		<label class="checkbox" for="ticket-publish"><input type="checkbox" name="published" value="1" [[+published:is=`1`:then=`checked`]] id="ticket-publish" /> [[%ticket_publish]]</label>
	</div>
</form>