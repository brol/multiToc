<?php
/**
 * @brief multiToc, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Tomtom, Kozlika, Franck Paul, Pierre Van Glabeke and contributors
 *
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_CONTEXT_ADMIN')) { return; }

$page_title = __('Tables of content');

if (!empty($_POST['save']))
{
	$settings	= unserialize(dcCore::app()->blog->settings->multiToc->multitoc_settings);
	
	$types = array('cat','tag','alpha','post');
	
	foreach ($types as $type) {
		foreach ($settings[$type] as $k => $v) {
			$settings[$type][$k] = '';
		}
	}
	
	foreach ($_POST as $k => $v) {
		if (preg_match('#^('.implode('|',$types).')_(.*)$#',$k,$match)) {
			$settings[$match[1]][$match[2]] = $v;
		}
	}
	
	dcCore::app()->blog->settings->multiToc->put('multitoc_settings',serialize($settings));
	http::redirect(dcCore::app()->admin->getPageURL().'&upd=1');
}

echo
'<html>'.
'<head>'.
	'<title>'.$page_title.'</title>'.
'</head>'.
'<body>'.
	dcPage::breadcrumb(
    array(
    html::escapeHTML(dcCore::app()->blog->name) => '',
    '<span class="page-title">'.$page_title.'</span>' => ''
    ));

# Information message
if (!empty($_GET['upd'])) {
  dcPage::success(__('Configuration has been saved successfully'));
}

echo
'<form method="post" action="'.dcCore::app()->admin->getPageURL().'">'.
	multiTocUi::form('post').
	multiTocUi::form('cat').
	multiTocUi::form('tag').
	multiTocUi::form('alpha').
	dcCore::app()->formNonce().
'<p><input name="save" value="'.__('Save').'" type="submit" /></p>'.
'</form>'.
 dcPage::helpBlock('multiToc').
'</body>'.
'</html>';