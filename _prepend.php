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

Clearbricks::lib()->autoload(['multiTocPost' => __DIR__ .'/inc/class.multi.toc.php']);
Clearbricks::lib()->autoload(['multiTocUi' => __DIR__ .'/inc/class.multi.toc.php']);

dcCore::app()->addBehavior('publicBeforeDocument',array('multiTocBehaviors','addTplPath'));
dcCore::app()->addBehavior('coreBlogGetPosts',array('multiTocBehaviors','coreBlogGetPosts'));
dcCore::app()->addBehavior('initStacker',array('multiTocBehaviors','initStacker'));

dcCore::app()->url->register('multitoc','multitoc','^multitoc/(.*)$',array('multiTocUrl','multiToc'));

require_once __DIR__ . '/_widgets.php';

class multiTocBehaviors
{
	public static function addTplPath()
        
	{
		
		$tplset = dcCore::app()->themes->moduleInfo(dcCore::app()->blog->settings->system->theme,'tplset');
        if (!empty($tplset) && is_dir(dirname(__FILE__).'/default-templates/'.$tplset)) {
            dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), dirname(__FILE__).'/default-templates/'.$tplset);
        } else {
            dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), dirname(__FILE__).'/default-templates/'.DC_DEFAULT_TPLSET);
        }
	}
	
	public static function coreBlogGetPosts($rs)
	{
		$rs->extend('rsMultiTocPost');
	}
	
  public static function adminPostEditor($editor = '')
	{
    $res = '';
		$s = unserialize(dcCore::app()->blog->settings->multiToc->multitoc_settings);
    if (isset($s['post']['enable']) && $s['post']['enable']) {
      if ($editor == 'dcLegacyEditor') {
        $res = dcPage::jsJson('dc_editor_multitoc', ['title' => __('Table of content')]) .
          dcPage::jsModuleLoad('multiToc/js/post.js', dcCore::app()->getVersion('multiToc'));
      } elseif ($editor == 'dcCKEditor') {
        $res = dcPage::jsJson('ck_editor_multitoc', [
          'title'        => __('Table of content'),
        ]);
      }
    }
    return $res;
	}

  public static function ckeditorExtraPlugins(ArrayObject $extraPlugins)
  {
    $extraPlugins[] = [
      'name'   => 'multitoc',
      'button' => 'multiToc',
      'url'    => DC_ADMIN_URL . 'index.php?pf=multiToc/js/ckeditor-multitoc-plugin.js',
    ];
  }
	
	public static function initStacker()
	{
		dcCore::app()->stacker->addFilter(
			'multiTocFilter',
			'multiTocBehaviors',
			'multiTocFilter',
			'any',
			100,
			'multiToc',
			__('Add post TOC')
		);
	}
	
	public static function multiTocFilter($rs,$text,$absolute_urls = false)
	{
		if ($rs->hasToc()) {
			$toc = new multiTocPost($rs);
			$text = $toc->process($text);
			unset($toc);
		}
		
		return $text;
	}
}

class rsMultiTocPost
{
	public static function hasToc($rs)
	{
		if (preg_match('/<p>::TOC::<\/p>/',$rs->post_excerpt_xhtml.$rs->post_content_xhtml)) {
			return true;
		}
		else {
			return false;
		}
	}
}
