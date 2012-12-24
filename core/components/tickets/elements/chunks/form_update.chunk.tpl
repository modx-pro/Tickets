<script type="text/javascript" src="[[+assetsUrl]]js/web/tickets.js"></script>
<form class="well" method="post" action="[[~[[*id]]]]?tid=[[+id]]" id="ticketForm">
    <div id="ticket-preview-placeholder"></div>
    
    <select name="parent" class="input-xxlarge">[[+sections]]</select>
	<span class="error">[[+error.parent]]</span>
	<br/>
	<input type="text" class="input-xxlarge" placeholder="[[%ticket_pagetitle]]" name="pagetitle" value="[[+pagetitle]]" maxlength="50"/>
	<span class="error">[[+error.pagetitle]]</span>
	<br/>
	<textarea class="input-xxlarge" placeholder="[[%ticket_content]]" name="content" id="ticket-editor" rows="20">[[+content]]</textarea>
	<span class="error">[[+error.content]]</span>

	<div class="form-actions">
		<input type="hidden" name="tid" value="[[+id]]" />
		<input type="hidden" name="action" value="updateTicket" />
		<input type="button" class="btn" value="[[%ticket_preview]]" onclick="Tickets.ticket.preview(this.form, this);"/>
		<input type="submit" class="btn btn-primary" value="[[%ticket_save]]" />&nbsp;&nbsp;
		<label class="checkbox"><input type="checkbox" name="published" value="1" [[+published:is=`1`:then=`checked`]] /> [[%ticket_publish]]</label>
	</div>
</form>
[[+error:notempty=`<div class="alert alert-block alert-error">[[+error]]</div>`]]