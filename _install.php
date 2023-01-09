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

$m_version = dcCore::app()->plugins->moduleInfo('multiToc','version');
$i_version = dcCore::app()->getVersion('multiToc');
if (version_compare($i_version,$m_version,'>=')) {
	return;
}

# Création du setting
$settings = array(
	'cat' => array(
		'enable' => '',
		'order_group' => '',
		'display_nb_entry' => '',
		'order_entry' => '',
		'display_date' => '',
		'format_date' => dcCore::app()->blog->settings->system->date_format,
		'display_author' => '',
		'display_cat' => '',
		'display_nb_com' => '',
		'display_nb_tb' => '',
		'display_tag' => ''
	),
	'tag' => array(
		'enable' => '',
		'order_group' => '',
		'display_nb_entry' => '',
		'order_entry' => '',
		'display_date' => '',
		'format_date' => dcCore::app()->blog->settings->system->date_format,
		'display_author' => '',
		'display_cat' => '',
		'display_nb_com' => '',
		'display_nb_tb' => '',
		'display_tag' => ''
	),
	'alpha' => array(
		'enable' => '',
		'order_group' => '',
		'display_nb_entry' => '',
		'order_entry' => '',
		'display_date' => '',
		'format_date' => dcCore::app()->blog->settings->system->date_format,
		'display_author' => '',
		'display_cat' => '',
		'display_nb_com' => '',
		'display_nb_tb' => '',
		'display_tag' => ''
	),
	'post' => array(
		'enable' => '',
		'numbering' => ''
	)
);
dcCore::app()->blog->settings->addNamespace('multiToc');
dcCore::app()->blog->settings->multiToc->put('multitoc_settings',serialize($settings),'string','multiToc settings',false,true);

dcCore::app()->setVersion('multiToc',$m_version);

return true;