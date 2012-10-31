<?php
require MODX_CORE_PATH . 'components/quip/controllers/web/LatestComments.php';

class CommentLatestCommentsController extends QuipLatestCommentsController {


	/**
	 * Get the latest comments and output
	 * @return string
	 */
	public function process() {
		$output = array();
		$alt = false;
		$rowCss = $this->getProperty('rowCss','quip-latest-comment');
		$altRowCss = $this->getProperty('altRowCss','quip-latest-comment-alt');
		$bodyLimit = $this->getProperty('bodyLimit',30);
		$tpl = $this->getProperty('tpl','quipLatestComment');
		$sections = array();
		/* Initialize page placeholders */
		$pagePlaceholders = array();
		$pagePlaceholders['resource'] = '';
		$pagePlaceholders['pagetitle'] = '';
		$placeholderPrefix = $this->getProperty('placeholderPrefix','quip.latest');

		/* Add the comments */
		$comments = $this->getComments();
		if (!empty($comments)) {
			$commentArray = array();
			/** @var quipComment $comment */
			foreach ($comments as $comment) {
				$commentArray = $comment->toArray();
				if (!array_key_exists($commentArray['section'], $sections)) {
					$q = $this->modx->newQuery('modResource', array('id' => $commentArray['section']));
					$q->select('pagetitle');
					if ($q->prepare() && $q->stmt->execute()) {
						$sections[$commentArray['section']] = $q->stmt->fetch(PDO::FETCH_COLUMN);
					}
				}
				$commentArray['sectiontitle'] = $sections[$commentArray['section']];
				$commentArray['bodyLimit'] = $bodyLimit;
				$commentArray['comments'] = $this->modx->getCount('quipComment',array('resource' => $commentArray['resource'],'deleted' => 0, 'approved' => 1));
				$commentArray['cls'] = $rowCss;
				if ($altRowCss && $alt) $commentArray['alt'] = ' '.$altRowCss;
				$commentArray['url'] = $comment->makeUrl();

				if (!empty($stripTags)) {
					$commentArray['body'] = strip_tags($commentArray['body']);
				}
				$commentArray['ago'] = $this->quip->getTimeAgo($commentArray['createdon']);
				//echo '<pre>';print_r($commentArray);echo '</pre>';
				$output[] = $this->quip->getChunk($tpl,$commentArray);
				$alt = !$alt;
			}

			$pagePlaceholders['resource'] = $commentArray['resource'];
			$pagePlaceholders['pagetitle'] = !empty($commentArray['pagetitle']) ? $commentArray['pagetitle'] : '';
		}

		$this->modx->toPlaceholders($pagePlaceholders,$placeholderPrefix);
		return $this->output($output);
	}

	/**
	 * Get all the latest comments
	 * @return array
	 */
	public function getComments() {

		$c = $this->modx->newQuery('quipComment');
		$c->select($this->modx->getSelectColumns('quipComment','quipComment'));
		$c->select('`Resource`.`pagetitle`,`Resource`.`parent` AS `section`');
		$c->leftJoin('modUser','Author');
		$c->leftJoin('modResource','Resource');
		$type = $this->getProperty('type','all');
		switch ($type) {
			case 'user':
				$user = $this->getProperty('user',0);
				if (empty($user)) return array();
				if (intval($user) > 0) {
					$c->where(array(
						'Author.id' => $user,
					));
				} else {
					$c->where(array(
						'Author.username' => $user,
					));
				}
				break;
			case 'thread':
				$thread = $this->getProperty('thread','');
				if (empty($thread)) return array();
				$c = $this->modx->newQuery('quipComment');
				$c->where(array(
					'quipComment.thread' => $thread,
				));
				break;
			case 'family':
				$family = $this->getProperty('family','');
				if (empty($family)) return array();
				$c = $this->modx->newQuery('quipComment');
				$c->where(array(
					'quipComment.thread:LIKE' => '%'.$family.'%',
				));
				break;
			case 'last':
				// Selecting last topics
				$q = $this->modx->newQuery('quipComment');
				$q->select('DISTINCT(`resource`)');
				$q->sortby('createdon', 'DESC');
				$q->limit($this->getProperty('limit',10),$this->getProperty('start',0));
				if ($q->prepare() && $q->stmt->execute()) {
					$tmp = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
					foreach ($tmp as $v) {
						// Getting one last comment for each topic
						$q = $this->modx->newQuery('quipComment', array('resource' => $v, 'deleted' => 0, 'approved' => 1));
						$q->select('`id`');
						$q->sortby('createdon','DESC');
						$q->limit(1);
						if ($q->prepare() && $q->stmt->execute()) {
							$ids[] = $q->stmt->fetch(PDO::FETCH_COLUMN);
						}
					}
					$c->where(array('id:IN' => $ids));
				}
				break;
			case 'all':
				$c->groupby('`quipComment`.`resource`','DESC');
			default:
				break;
		}
		$contexts = $this->getProperty('contexts','');
		if (!empty($contexts)) {
			$c->where(array(
				'Resource.context_key:IN' => explode(',',$contexts),
			));
		}
		$c->where(array(
			'quipComment.deleted' => false,
			'quipComment.approved' => true,
		));
		$c->sortby($this->modx->escape($this->getProperty('sortByAlias','quipComment')).'.'.$this->modx->escape($this->getProperty('sortBy','createdon')),$this->getProperty('sortDir','DESC'));
		$c->limit($this->getProperty('limit',10),$this->getProperty('start',0));
		//$c->prepare();echo $c->toSql();die;
		return $this->modx->getCollection('quipComment',$c);
	}
}

return 'CommentLatestCommentsController';