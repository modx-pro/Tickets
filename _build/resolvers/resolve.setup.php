<?php
/**
 * Resolves setup-options settings
 *
 * @package tickets
 * @subpackage build
 */
$success= false;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:
	case xPDOTransport::ACTION_UPGRADE:
		/* @var modSnippet $snippet */
		if (!$snippet = $modx->getObject('modSnippet', array('name' => 'Jevix'))) {
			/* @var modTransportProvider $provider */
			$provider = $modx->getObject('transport.modTransportProvider',1);
			$provider->getClient();
			$modx->getVersionData();
			$productVersion = $modx->version['code_name'].'-'.$modx->version['full_version'];
			$packageName = 'Jevix';

			$response = $provider->request('package','GET',array(
				'supports' => $productVersion,
				'query' => $packageName
			));

			if(!empty($response)) {
				$foundPackages = simplexml_load_string($response->response);
				foreach($foundPackages as $foundPackage) {
					/* @var modTransportPackage $foundPackage */
					if($foundPackage->name == $packageName) {
						$sig = explode('-',$foundPackage->signature);
						$versionSignature = explode('.',$sig[1]);
						$url = $foundPackage->location;
						if (function_exists('curl_init')) {
							$curl = curl_init($url);
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($curl, CURLOPT_HEADER, false);
							curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
							curl_setopt($curl, CURLOPT_URL, $url);
							curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
							curl_setopt($curl, CURLOPT_TIMEOUT, 60);
							$file = curl_exec($curl);
							if ($file === false) {
								$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not download package '.$packageName.': '.curl_error($curl));
							}
							curl_close($curl);
						} else {
							$file = file_get_contents($url);
						}
						$file = file_get_contents($url);
						file_put_contents($modx->getOption('core_path').'packages/'.$foundPackage->signature.'.transport.zip',$file);

						/* add in the package as an object so it can be upgraded */
						/** @var modTransportPackage $package */
						$package = $modx->newObject('transport.modTransportPackage');
						$package->set('signature',$foundPackage->signature);
						$package->fromArray(array(
							'created' => date('Y-m-d h:i:s'),
							'updated' => null,
							'state' => 1,
							'workspace' => 1,
							'provider' => 1,
							'source' => $foundPackage->signature.'.transport.zip',
							'package_name' => $sig[0],
							'version_major' => $versionSignature[0],
							'version_minor' => !empty($versionSignature[1]) ? $versionSignature[1] : 0,
							'version_patch' => !empty($versionSignature[2]) ? $versionSignature[2] : 0,
						));
						if (!empty($sig[2])) {
							$r = preg_split('/([0-9]+)/',$sig[2],-1,PREG_SPLIT_DELIM_CAPTURE);
							if (is_array($r) && !empty($r)) {
								$package->set('release',$r[0]);
								$package->set('release_index',(isset($r[1]) ? $r[1] : '0'));
							} else {
								$package->set('release',$sig[2]);
							}
						}
						$success = $package->save();
						if($success && $package->install()) {
							$modx->log(xPDO::LOG_LEVEL_INFO,'[Tickets] '.$packageName.' was successfully installed');
						}
						else {
							$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not save package '.$packageName);
						}
						break;
					}
				}
			}
			else {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not find '.$packageName.' in MODX repository');
			}
		}

		if ($snippet = $modx->getObject('modSnippet', array('name' => 'Jevix'))) {
			if (!$prop_ticket = $modx->getObject('modPropertySet', array('name' => 'Ticket'))) {
				$prop_ticket = $modx->newObject('modPropertySet', array(
					'name' => 'Ticket'
					,'description' => 'Filter rules for Tickets'
					,'properties' => array(
						'cfgAllowTagParams' => array (
							'name' => 'cfgAllowTagParams','desc' => 'cfgAllowTagParams','type' => 'textfield','options' => array (),'lexicon' => 'jevix:properties','area' => '',
							'value' => '{"pre":{"class":["prettyprint"]},"cut":{"title":["#text"]},"a":["title","href"],"img":{"0":"src","alt":"#text","1":"title","align":["right","left","center"],"width":"#int","height":"#int","hspace":"#int","vspace":"#int"}}',
						),
						'cfgAllowTags' => array (
							'name' => 'cfgAllowTags','desc' => 'cfgAllowTags','type' => 'textfield','options' => array (),'lexicon' => 'jevix:properties','area' => '',
							'value' => 'a,img,i,b,u,em,strong,li,ol,ul,sup,abbr,pre,acronym,h1,h2,h3,h4,h5,h6,cut,br,code,s,blockquote,table,tr,th,td',
						),
						'cfgSetTagChilds' => array(
							'name' => 'cfgSetTagChilds','desc' => 'cfgSetTagChilds','type' => 'textfield','options' => array (),'lexicon' => 'jevix:properties','area' => '',
							'value' => '[["ul",["li"],false,true],["ol",["li"],false,true],["table",["tr"],false,true],["tr",["td","th"],false,true]]'
						),
						'cfgSetAutoReplace' => array (
							'name' => 'cfgSetAutoReplace','desc' => 'cfgSetAutoReplace','type' => 'textfield','options' => array (),'lexicon' => 'jevix:properties','area' => '',
							'value' => '[["+/-","(c)","(с)","(r)","(C)","(С)","(R)","<code","code>"],["±","©","©","®","©","©","®","<pre class=\\"prettyprint\\"","pre>"]]',
						),
						'cfgSetTagShort' => array(
							'name' => 'cfgSetTagShort','desc' => 'cfgSetTagShort','type' => 'textfield','options' => array (),'lexicon' => 'jevix:properties','area' => '',
							'value' => 'br,img,cut',
						)
					)
				));
				if ($prop_ticket->save() && $snippet->addPropertySet($prop_ticket)) {
					$modx->log(xPDO::LOG_LEVEL_INFO,'[Tickets] Property set "Ticket" for snippet '.$packageName.' was created');
				}
				else {
					$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not create property set "Ticket" for '.$packageName);
				}
			}

			if (!$prop_comment = $modx->getObject('modPropertySet', array('name' => 'Comment'))) {
				$prop_comment = $modx->newObject('modPropertySet', array(
					'name' => 'Comment'
					,'description' => 'Filter rules for Tickets comments'
					,'properties' => array(
						'cfgAllowTagParams' => array (
							'name' => 'cfgAllowTagParams','desc' => 'cfgAllowTagParams','type' => 'textfield','options' => array (),'lexicon' => 'jevix:properties','area' => '',
							'value' => '{"pre":{"class":["prettyprint"]},"a":["title","href"],"img":{"0":"src","alt":"#text","1":"title","align":["right","left","center"],"width":"#int","height":"#int","hspace":"#int","vspace":"#int"}}',
						),
						'cfgAllowTags' => array (
							'name' => 'cfgAllowTags','desc' => 'cfgAllowTags','type' => 'textfield','options' => array (),'lexicon' => 'jevix:properties','area' => '',
							'value' => 'a,img,i,b,u,em,strong,li,ol,ul,sup,abbr,pre,acronym,br,code,s,blockquote',
						),
						'cfgSetTagChilds' => array(
							'name' => 'cfgSetTagChilds','desc' => 'cfgSetTagChilds','type' => 'textfield','options' => array (),'lexicon' => 'jevix:properties','area' => '',
							'value' => '[["ul",["li"],false,true],["ol",["li"],false,true]]'
						),
						'cfgSetAutoReplace' => array (
							'name' => 'cfgSetAutoReplace','desc' => 'cfgSetAutoReplace','type' => 'textfield','options' => array (),'lexicon' => 'jevix:properties','area' => '',
							'value' => '[["+/-","(c)","(с)","(r)","(C)","(С)","(R)","<code","code>"],["±","©","©","®","©","©","®","<pre class=\\"prettyprint\\"","pre>"]]',
						),
						'cfgSetTagShort' => array(
							'name' => 'cfgSetTagShort','desc' => 'cfgSetTagShort','type' => 'textfield','options' => array (),'lexicon' => 'jevix:properties','area' => '',
							'value' => 'br,img',
						)
					)
				));
				if ($prop_comment->save() && $snippet->addPropertySet($prop_comment)) {
					$modx->log(xPDO::LOG_LEVEL_INFO,'[Tickets] Property set "Comment" for snippet '.$packageName.' was created');
				}
				else {
					$modx->log(xPDO::LOG_LEVEL_ERROR,'[Tickets] Could not create property set "Comment" for '.$packageName);
				}
			};
		}

		$success= true;
		break;
	case xPDOTransport::ACTION_UNINSTALL:
		$success= true;
		break;
}
return $success;