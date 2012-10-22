<h4 id="comment-new-link"><a href="#reply" onclick="return showCommentForm()">[[%quip.comment_add_new]]</a></h4>
<div id="[[+idprefix]]form-placeholder">
    <form id="[[+idprefix]]form" action="[[~[[*id]]]]" method="post">
        <div id="comment-preview-placeholder"></div>
    
        <input type="hidden" name="thread" value="[[+thread]]" />
        <input type="hidden" name="parent" value="[[+parent]]" />
        
        <textarea name="comment" id="comment-editor" cols="30" rows="10">[[+comment]]</textarea>
        
        <div class="form-actions">
            [[+can_post:is=`1`:then=`
                <input type="button" class="btn" value="[[%quip.preview]]" onclick="previewComment(this.form, this)" />
                <input type="button" class="btn btn-primary" value="[[%quip.post]]" onclick="sendComment(this.form, this)" />
                &nbsp;&nbsp;<label class="checkbox"><input type="checkbox" name="notify" value="1" checked />[[%quip.notify_me]]</label>
            `]]
        </div>
    </form>
</div>