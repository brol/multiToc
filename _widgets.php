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
if (!defined('DC_RC_PATH')) { return; }

dcCore::app()->addBehavior('initWidgets',array('multiTocWidgets','initWidgets'));

class multiTocWidgets
{
	public static function initWidgets($w)
	{
		$w->create('multiToc',__('MultiToc: table of content'),array('multiTocWidgets','widget'),
			null,
			__('Contents by category, keyword and alphabetical order'));
		$w->multiToc->setting('title',__('Title:'),__('Table of content'));
		$w->multiToc->setting('homeonly',__('Display on:'),0,'combo',
			array(
				__('All pages') => 0,
				__('Home page only') => 1,
				__('Except on home page') => 2
				)
		);
		$w->multiToc->setting('content_only',__('Content only'),0,'check');
		$w->multiToc->setting('class',__('CSS class:'),'');
		$w->multiToc->setting('offline',__('Offline'),0,'check');
	}
	
	public static function widget($w)
	{
		
		if ($w->offline)
		return;

        if (!$w->checkHomeOnly(dcCore::app()->url->type)) {
            return null;
        }
		
		$amask = '<a href="%1$s">%2$s</a>';
		$limask = '<li class="%1$s">%2$s</li>';
		
		$res = '';
		
		$settings = unserialize(dcCore::app()->blog->settings->multiToc->multitoc_settings);
		
		if ($settings['cat']['enable']) {
			$link = sprintf($amask,dcCore::app()->blog->url.dcCore::app()->url->getBase('multitoc').'/cat',__('By category'));
			$res .= sprintf($limask,'toc-cat',$link);
		}
		if ($settings['tag']['enable']) {
			$link = sprintf($amask,dcCore::app()->blog->url.dcCore::app()->url->getBase('multitoc').'/tag',__('By tag'));
			$res .= sprintf($limask,'toc-tag',$link);
		}
		if ($settings['alpha']['enable']) {
			$link = sprintf($amask,dcCore::app()->blog->url.dcCore::app()->url->getBase('multitoc').'/alpha',__('By alpha order'));
			$res .= sprintf($limask,'toc-alpha',$link);
		}
		
		if (!empty($res)) {
    $res =
		($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '').
		'<ul>'.$res.'</ul>';

		return $w->renderDiv($w->content_only,'info-blog '.$w->class,'',$res);

		}
		
	}
}