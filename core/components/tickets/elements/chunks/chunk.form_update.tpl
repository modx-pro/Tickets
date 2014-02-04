<form class="well" method="post" action="" id="ticketForm">
	<div id="ticket-preview-placeholder"></div>

	<input type="hidden" name="tid" value="[[+id]]" />
	<input type="hidden" name="published" value="1" />

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
		<input type="button" class="btn btn-default preview" value="[[%ticket_preview]]" onclick="return Tickets.ticket.preview(this.form, this);" title="Ctrl + Enter" />
		<input type="submit" class="btn btn-primary submit" value="[[%ticket_save]]" title="Ctrl + Shift + Enter" />
	</div>
</form>