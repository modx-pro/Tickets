<li class="ticket-comment [[-+cls]]" id="[[+idprefix]][[+id]]" data-parent="[[+parent]]">
    <div id="[[+idprefix]][[+id]]-div" class="ticket-comment-body [[+alt]]">
        <div class="ticket-header">
            [[+gravatarUrl:notempty=`<img src="[[+gravatarUrl]]" class="ticket-avatar" alt="" />`]]
            <span class="ticket-comment-author">[[+authorName]]</span>
            <span class="ticket-comment-createdon">[[+createdon]]</span>
            <span class="ticket-comment-link"><a href="/[[+url]]">#</a></span>
            [[+approved:if=`[[+approved]]`:is=`1`:then=``:else=`- <em>[[%quip.unapproved? &namespace=`quip` &topic=`default`]]</em>`]]
        </div>
    
        <div class="ticket-comment-text">
            [[+body]]
        </div>
    </div>
    [[+replyUrl:notempty=`<div class="comment-reply"><a href="#reply" onclick="return showReplyForm([[+id]])">[[%quip.reply? &namespace=`quip` &topic=`default`]]</a></div>`]]
    <ol class="comments-list">[[+children]]</ol>
</li>
