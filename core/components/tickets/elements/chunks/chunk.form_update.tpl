<form class="well update" method="post" action="" id="ticketForm">
    <div id="ticket-preview-placeholder"></div>

    <input type="hidden" name="tid" value="[[+id]]"/>

    <div class="form-group">
        <label for="ticket-sections">[[%tickets_section]]</label>
        <select name="parent" class="form-control" id="ticket-sections">[[+sections]]</select>
        <span class="error"></span>
    </div>

    <div class="form-group">
        <label for="ticket-pagetitle">[[%ticket_pagetitle]]</label>
        <input type="text" class="form-control" placeholder="[[%ticket_pagetitle]]" name="pagetitle"
               value="[[+pagetitle]]" maxlength="50" id="ticket-pagetitle"/>
        <span class="error"></span>
    </div>

    <div class="form-group">
        <textarea class="form-control" placeholder="[[%ticket_content]]" name="content" id="ticket-editor" rows="10">[[+content]]</textarea>
        <span class="error"></span>
    </div>

    <div class="ticket-form-files">
        [[+files]]
    </div>

    <div class="form-actions row">
        <div class="col-md-4">
            <input type="button" class="btn btn-default preview" value="[[%ticket_preview]]" title="Ctrl + Enter"/>
        </div>
        <div class="col-md-8 move-right">
            [[+published:is=`1`:then=`
            <a href="[[~[[+id]]?scheme=`full`]]" class="btn btn-default btn-xs" target="_blank">[[%ticket_open]]</a>
            <input type="button" class="btn btn-danger draft" name="draft" value="[[%ticket_draft]]" title=""/>
            `:else=`
            <input type="button" class="btn btn-primary publish" name="publish" value="[[%ticket_publish]]" title=""/>
            `]]
            <input type="submit" class="btn btn-default save" name="save" value="[[%ticket_save]]"
                   title="Ctrl + Shift + Enter"/>
            [[+allowDelete:is=`1`:then=`
                [[+deleted:is=`1`:then=`
                <input type="button" class="btn btn-default undelete" data-confirm="[[%ticket_undelete_text]]" name="undelete" value="[[%ticket_undelete]]"/>
                `:else=`
                <input type="button" class="btn btn-default delete" data-confirm="[[%ticket_delete_text]]" name="delete" value="[[%ticket_delete]]"/>
                `]]
            `]]
        </div>
    </div>
</form>