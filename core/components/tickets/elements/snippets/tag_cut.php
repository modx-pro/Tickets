<?php
$input = preg_replace('!(<code.*?>)(.*?)</code>!ise', " '<pre class=\"prettyprint\">' .  stripslashes( str_replace(array('<','>','[',']','`'),array('&lt;','&gt;','&#91;','&#93;','&#96;'),'$2') )  . '</pre>' ", $input);
$input = strip_tags(nl2br($input), '<a>,<img>,<i>,<b>,<u>,<em>,<strong>,<li>,<ol>,<ul>,<sup>,<abbr>,<pre>,<acronym>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<cut>,<br>,<code>,<s>,<blockquote>');

$pos = strpos($input, '<cut');
if ($pos === false) {return $input;}

$text = substr($input,0,$pos);

return $text.'<p><a href="'.$options.'">Читать дальше &rarr;</a></p>';