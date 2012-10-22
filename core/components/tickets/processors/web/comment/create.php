<?php
$errors = array();

/* cleanse body from XSS and other junk */
$fields['body'] = $quip->cleanse($fields['comment'],$this->getProperties());
if (empty($fields['body'])) $errors['comment'] = $modx->lexicon('quip.message_err_ns');

/* run preHooks */
/*
$quip->loadHooks('pre');
$quip->preHooks->loadMultiple($this->getProperty('preHooks',''),$this->getProperties(),array(
	'hooks' => $this->getProperty('preHooks',''),
));
*/

/* if a prehook sets a field, do so here */
/*
$fs = $quip->preHooks->getFields();
if (!empty($fs)) {
	// better handling of checkbox values when input name is an array[]
	foreach ($fs as $f => $v) {
		if (is_array($v)) { implode(',',$v); }
		$fs[$f] = $v;
	}
	// assign new fields values
	$fields = $quip->preHooks->getFields();
}
*/
/* if any errors in preHooks */
/*
if (!empty($quip->preHooks->errors)) {
	foreach ($quip->preHooks->errors as $key => $error) {
		$errors[$key] = $error;
	}
}
*/
/* if no errors, save comment */
if (!empty($errors)) return $errors;

/** @var quipComment $comment */
$comment = $modx->newObject('quipComment');
$comment->fromArray($fields);
$comment->set('ip',$_SERVER['REMOTE_ADDR']);
$comment->set('createdon',strftime('%b %d, %Y at %I:%M %p',time()));
$comment->set('body',$fields['body']);

/* if moderation is on, don't auto-approve comments */
if ($this->getProperty('moderate',false,'isset')) {
	/* by default moderate, unless special cases pass */
	$approved = false;

	/* never moderate mgr users */
	if ($modx->user->hasSessionContext('mgr') && $this->getProperty('dontModerateManagerUsers',true,'isset')) {
		$approved = true;
		/* check logged in status in current context*/
	} else if ($modx->user->hasSessionContext($modx->context->get('key'))) {
		/* if moderating only anonymous users, go ahead and approve since the user is logged in */
		if ($this->getProperty('moderateAnonymousOnly',false,'isset')) {
			$approved = true;

		} else if ($this->getProperty('moderateFirstPostOnly',true,'isset')) {
			/* if moderating only first post, check to see if user has posted and been approved elsewhere.
			 * Note that this only works with logged in users.
			 */
			$ct = $modx->getCount('quipComment',array(
				'author' => $modx->user->get('id'),
				'approved' => true,
			));
			if ($ct > 0) $approved = true;
		}
	}
	$comment->set('approved',$approved);
	if ($approved) {
		$comment->set('approvedon',strftime('%Y-%m-%d %H:%M:%S',time()));
	}
}

/* URL preservation information
 * @deprecated 0.5.0, this now goes on the Thread, will remove code in 0.5.1
 */

if (!empty($fields['parent'])) {
	$parentComment = $modx->getObject('quipComment',$fields['parent']);
	if ($parentComment) {
		$comment->set('resource',$parentComment->get('resource'));
		$comment->set('idprefix',$parentComment->get('idprefix'));
		$comment->set('existing_params',$parentComment->get('existing_params'));
	}
} else {
	$comment->set('resource',$this->getProperty('resource',$modx->resource->get('id')));
	$comment->set('idprefix',$this->getProperty('idPrefix','qcom'));
	$p = $modx->request->getParameters();
	unset($p['reported']);
	$comment->set('existing_params',$p);
}

/* ensure author is set */
if ($this->hasAuth) {
	$comment->set('author', $modx->user->get('id'));
}


/* save comment */
if ($comment->save() == false) {
	$errors['message'] = $modx->lexicon('quip.comment_err_save');
	return $errors;
} elseif ($this->getProperty('requireAuth',false)) {
	/* if successful and requireAuth is true, update user profile */
	$profile = $modx->user->getOne('Profile');
	if ($profile) {
		if (!empty($fields['name'])) $profile->set('fullname',$fields['name']);
		if (!empty($fields['email'])) $profile->set('email',$fields['email']);
		$profile->set('website',$fields['website']);
		$profile->save();
	}
}

/* if comment is approved, send emails */
if ($comment->get('approved')) {
	/** @var quipThread $thread */
	$thread = $comment->getOne('Thread');
	if ($thread) {
		if ($thread->notify($comment) == false) {
			$modx->log(modX::LOG_LEVEL_ERROR,'[Quip] Notifications could not be sent for comment: '.print_r($comment->toArray(),true));
		}
	} else {
		$modx->log(modX::LOG_LEVEL_ERROR,'[Quip] Thread not found for comment: '.print_r($comment->toArray(),true));
	}
} else {
	if (!$comment->notifyModerators()) {
		$modx->log(modX::LOG_LEVEL_ERROR,'[Quip] Moderator Notifications could not be sent for comment: '.print_r($comment->toArray(),true));
	}
}

/* if notify is set to true, add user to quipCommentNotify table */
if (!empty($fields['notify'])) {
	/** @var quipCommentNotify $quipCommentNotify */
	$quipCommentNotify = $modx->getObject('quipCommentNotify',array(
		'thread' => $comment->get('thread'),
		'email' => $fields['email'],
	));
	if (empty($quipCommentNotify)) {
		$quipCommentNotify = $modx->newObject('quipCommentNotify');
		$quipCommentNotify->set('thread',$comment->get('thread'));
		$quipCommentNotify->set('email',$fields['email']);
		$quipCommentNotify->set('user',$isLoggedIn ? $modx->user->get('id') : 0);
		$quipCommentNotify->save();
	}
}

/* run postHooks */
/*
$commentArray = $comment->toArray();
$quip->loadHooks('post');
$quip->postHooks->loadMultiple($this->getProperty('postHooks',''),$commentArray,array(
	'hooks' => $this->getProperty('postHooks',''),
));
*/

return $comment;