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

dcCore::app()->tpl->addValue('MultiTocUrl', array('multiTocTpl','multiTocUrl'));
dcCore::app()->tpl->addValue('MultiTocCss', array('multiTocTpl','multiTocCss'));
dcCore::app()->tpl->addValue('MultiTocGroupTitle', array('multiTocTpl','multiTocGroupTitle'));
dcCore::app()->tpl->addValue('MultiTocGroupDesc', array('multiTocTpl','multiTocGroupDesc'));
dcCore::app()->tpl->addValue('MultiTocGroupCount', array('multiTocTpl','multiTocGroupCount'));
dcCore::app()->tpl->addValue('MultiTocItemUrl', array('multiTocTpl','multiTocItemUrl'));
dcCore::app()->tpl->addValue('MultiTocItemTitle', array('multiTocTpl','multiTocItemTitle'));
dcCore::app()->tpl->addValue('MultiTocItemDate', array('multiTocTpl','multiTocItemDate'));
dcCore::app()->tpl->addValue('MultiTocItemCategory', array('multiTocTpl','multiTocItemCategory'));
dcCore::app()->tpl->addValue('MultiTocItemAuthor', array('multiTocTpl','multiTocItemAuthor'));
dcCore::app()->tpl->addValue('MultiTocItemNbComments', array('multiTocTpl','multiTocItemNbComments'));
dcCore::app()->tpl->addValue('MultiTocItemNbTrackbacks', array('multiTocTpl','multiTocItemNbTrackbacks'));
dcCore::app()->tpl->addValue('MultiTocPageTitle', array('multiTocTpl','multiTocPageTitle'));

dcCore::app()->tpl->addBlock('MultiTocGroup', array('multiTocTpl','multiTocGroup'));
dcCore::app()->tpl->addBlock('MultiTocItem', array('multiTocTpl','multiTocItem'));
dcCore::app()->tpl->addBlock('MultiTocIf',array('multiTocTpl','multiTocIf'));
dcCore::app()->tpl->addBlock('MultiTocMetaData',array('multiTocTpl','multiTocMetaData'));

class multiTocUrl extends dcUrlHandlers
{
	public static function multiToc($args)
	{
		
		$settings = unserialize(dcCore::app()->blog->settings->multiToc->multitoc_settings);
		
		if ($settings['cat']['enable']) {
			$types[] = 'cat';
		}
		if ($settings['tag']['enable']) {
			$types[] = 'tag';
		}
		if ($settings['alpha']['enable']) {
			$types[] = 'alpha';
		}
		
		//if (count($args) == 0) {
		if ($args === null) {
			$type = 'cat';
		}
		//elseif (count($args) == 1) {
		elseif ($args) {
			$type = in_array($args,$types) ? $args : null;
			unset($types);
		}
		else {
			$type = null;
		}
		
		dcCore::app()->ctx->multitoc_type = $type;
		
		if ($type === null) {
			self::p404();
		}
		else {
			self::serveDocument('multitoc.html');
		}
		
		exit;
	}
}

class multiTocTpl
{

	public static function multiTocUrl($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'dcCore::app()->blog->url.dcCore::app()->url->getBase("multitoc")').'; ?>';
	}
	
	public static function multiTocCss()
	{
		
		$plop =
			dcCore::app()->blog->themes_path.'/'.
			dcCore::app()->blog->settings->system->theme.'/styles/multitoc.css';
			
		$tagada = dcCore::app()->blog->themes_path.'/default/multitoc.css';
		
		if (file_exists($plop)) {
			$css =
				dcCore::app()->blog->settings->system->themes_url.'/'.
				dcCore::app()->blog->settings->system->theme.'/styles/multitoc.css';
		} elseif (file_exists($tagada)) {
			$css =
				dcCore::app()->blog->settings->system->themes_url.'/default/multitoc.css';
		} else {
			$css =
				dcCore::app()->blog->url.
				((dcCore::app()->blog->settings->system->url_scan == 'path_info')?'?':'').
				'pf=multiToc/css/multitoc.css';
		}
		$res =
			"\n<?php \n".
			"echo '<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"$css\" />';\n".
			"?>\n";
			
		return $res;
	}
	
	public static function multiTocGroup($attr,$content)
	{
		$p = "\dcCore::app()->ctx->multitoc_settings = unserialize(\dcCore::app()->blog->settings->multiToc->multitoc_settings);\n";
		$p .= "\$params = array();\n";
		$p .= "if (\dcCore::app()->ctx->multitoc_type == 'cat') :\n";
			$p .= "\dcCore::app()->ctx->multitoc_group = \dcCore::app()->blog->getCategories(array('post_type'=>'post'));\n";
		$p .= "elseif (\dcCore::app()->ctx->multitoc_type == 'tag') :\n";
			$p .= "\$meta = new dcMeta(\dcCore::app());\n";
			$p .= "\$meta_rs = \$meta->getMetadata(array('meta_type' => 'tag'));\n";
			$p .= "\dcCore::app()->ctx->multitoc_group = \$meta->computeMetaStats(\$meta_rs);\n";
			$p .= "\dcCore::app()->ctx->multitoc_group->sort('meta_id_lower',\dcCore::app()->ctx->multitoc_settings['tag']['order_group']);\n";
		$p .= "elseif (\dcCore::app()->ctx->multitoc_type == 'alpha') :\n";
			$p .= "if (\dcCore::app()->con->driver() == 'pgsql') :\n";
				$p .= "\dcCore::app()->ctx->multitoc_group = ".
				"multiTocPublic::multiTocGroupPGSQL(\dcCore::app(),".
				"\dcCore::app()->ctx->multitoc_settings['alpha']['order_group']);\n";
			$p .= "else :\n";
				$p .= "\$params['columns'] = array('UPPER(SUBSTRING(post_title,1,1)) AS post_letter','COUNT(*) as count');\n";
				$p .= "\$params['sql'] = 'GROUP BY post_letter';\n";
				$p .= "\$params['no_content'] = true;\n";
				$p .= "\$params['order'] = \dcCore::app()->ctx->multitoc_settings['alpha']['order_group'];\n";
				$p .= "\dcCore::app()->ctx->multitoc_group = \dcCore::app()->blog->getPosts(\$params);\n";
			$p .= "endif;\n";
		$p .= "endif;\n";
		
		$res = "<?php\n";
		$res .= $p;
		$res .= "unset(\$params);\n";
		$res .= "?>\n";
		
		$res .=
		'<?php while (dcCore::app()->ctx->multitoc_group->fetch()) : ?>'.$content.'<?php endwhile; dcCore::app()->ctx->multitoc_group = null; dcCore::app()->ctx->multitoc_settings = null; ?>';
		
		return $res;
	}
	
	public static function multiTocGroupTitle($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		
		$res = "<?php if (\dcCore::app()->ctx->multitoc_type == 'cat') :\n";
			$res .= "echo ".sprintf($f,'dcCore::app()->ctx->multitoc_group->cat_title').";\n";
		$res .= "elseif (\dcCore::app()->ctx->multitoc_type == 'tag') :\n";
			$res .= "echo ".sprintf($f,'dcCore::app()->ctx->multitoc_group->meta_id').";\n";
		$res .= "elseif (\dcCore::app()->ctx->multitoc_type == 'alpha') :\n";
			$res .= "echo ".sprintf($f,'dcCore::app()->ctx->multitoc_group->post_letter').";\n";
		$res .= "endif; ?>\n";
		
		return $res;
	}
	
	public static function multiTocGroupDesc($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		
		$res = "<?php if (\dcCore::app()->ctx->multitoc_type == 'cat') :\n";
			$res .= "echo ".sprintf($f,'dcCore::app()->ctx->multitoc_group->cat_desc').";\n";
		$res .= "endif; ?>\n";
		
		return $res;
	}
	
	public static function multiTocGroupCount($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		
		$res = "<?php\n";
		$res .= "\$mask = '<span class=\"toc-group-count\">%s</span>';\n";
		$res .= "if (\dcCore::app()->ctx->multitoc_type == 'cat' && \dcCore::app()->ctx->multitoc_settings['cat']['display_nb_entry']) :\n";
			$res .= "echo sprintf(\$mask,'('.".sprintf($f,'dcCore::app()->ctx->multitoc_group->nb_post').".')');\n";
		$res .= "elseif (\dcCore::app()->ctx->multitoc_type == 'tag' && \dcCore::app()->ctx->multitoc_settings['tag']['display_nb_entry']) :\n";
			$res .= "echo sprintf(\$mask,'('.".sprintf($f,'dcCore::app()->ctx->multitoc_group->count').".')');\n";
		$res .= "elseif (\dcCore::app()->ctx->multitoc_type == 'alpha' && \dcCore::app()->ctx->multitoc_settings['alpha']['display_nb_entry']) :\n";
			$res .= "echo sprintf(\$mask,'('.".sprintf($f,'dcCore::app()->ctx->multitoc_group->count').".')');\n";
		$res .= "endif;\n";
		$res .= "?>\n";
		
		return $res;
	}
	
	public static function multiTocItem($attr,$content)
	{
	
		$p = "\$params = array();\n";
		$p .= "\$params['no_content'] = true;\n";
		
		$p .= "if (\dcCore::app()->ctx->multitoc_type == 'cat') :\n";
			$p .= "\$params['order'] = \dcCore::app()->ctx->multitoc_settings['cat']['order_entry'];\n";
			$p .= "\$params['cat_id'] = \dcCore::app()->ctx->multitoc_group->cat_id;\n";
			$p .= "\dcCore::app()->ctx->multitoc_items = \dcCore::app()->blog->getPosts(\$params);\n";
		$p .= "elseif (\dcCore::app()->ctx->multitoc_type == 'tag') :\n";
			$p .= "\$params['meta_id'] = \dcCore::app()->ctx->multitoc_group->meta_id;\n";
			$p .= "\$params['meta_type'] = 'tag';\n";
			$p .= "\$params['post_type'] = '';\n";
			$p .= "\$params['order'] = \dcCore::app()->ctx->multitoc_settings['tag']['order_entry'];\n";
			$p .= "\dcCore::app()->ctx->multitoc_items = \$meta->getPostsByMeta(\$params);\n";
		$p .= "elseif (\dcCore::app()->ctx->multitoc_type == 'alpha') :\n";
			$p .= "\$params['order'] = \dcCore::app()->ctx->multitoc_settings['alpha']['order_entry'];\n";
			$p .= "\$params['sql'] = ' AND UPPER(SUBSTRING(post_title,1,1)) = \''.\dcCore::app()->ctx->multitoc_group->post_letter.'\'';\n";
			$p .= "\dcCore::app()->ctx->multitoc_items = \dcCore::app()->blog->getPosts(\$params);\n";
		$p .= "endif;\n";
		
		$res = "<?php\n";
		$res .= $p;
		$res .= 'unset($params);'."\n";
		$res .= "?>\n";
		
		$res .=
		'<?php while (dcCore::app()->ctx->multitoc_items->fetch()) : ?>'.$content.'<?php endwhile; dcCore::app()->ctx->multitoc_items = null; ?>';
		
		return $res;
	}
	
	public static function multiTocItemUrl($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'dcCore::app()->ctx->multitoc_items->getURL()').'; ?>';
	}
	
	public static function multiTocItemTitle($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'dcCore::app()->ctx->multitoc_items->post_title').'; ?>';
	}
	
	public static function multiTocItemDate($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		
		$mask = isset($attr['mask']) ? sprintf($f,'"'.$attr['mask'].'"') : '\'<span class="toc-item-date">%s</span> - \'';
		
		$res = "<?php\n";
		$res .= "\$mask = ".$mask.";\n";
		$res .= "if ((\dcCore::app()->ctx->multitoc_type == 'cat' && \dcCore::app()->ctx->multitoc_settings['cat']['display_date'])\n";
		$res .= "|| (\dcCore::app()->ctx->multitoc_type == 'tag' && \dcCore::app()->ctx->multitoc_settings['tag']['display_date'])\n";
		$res .= "|| (\dcCore::app()->ctx->multitoc_type == 'alpha' && \dcCore::app()->ctx->multitoc_settings['alpha']['display_date'])\n";
		$res .= ") :\n";
			$res .= "echo sprintf(\$mask,\dcCore::app()->ctx->multitoc_items->getDate(\dcCore::app()->ctx->multitoc_settings[\dcCore::app()->ctx->multitoc_type]['format_date']));\n";
		$res .= "endif;\n";
		$res .= "?>\n";
		
		return $res;
	}
	
	public static function multiTocItemAuthor($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		
		$mask = isset($attr['mask']) ? sprintf($f,'"'.$attr['mask'].'"') : '\' - <span class="toc-item-author">%s</span>\'';
		
		$res = "<?php\n";
		$res .= "\$mask = ".$mask.";\n";
		$res .= "if ((\dcCore::app()->ctx->multitoc_type == 'cat' && \dcCore::app()->ctx->multitoc_settings['cat']['display_author'])\n";
		$res .= "|| (\dcCore::app()->ctx->multitoc_type == 'tag' && \dcCore::app()->ctx->multitoc_settings['tag']['display_author'])\n";
		$res .= "|| (\dcCore::app()->ctx->multitoc_type == 'alpha' && \dcCore::app()->ctx->multitoc_settings['alpha']['display_author'])\n";
		$res .= ") :\n";
			$res .= "echo sprintf(\$mask,\dcCore::app()->ctx->multitoc_items->getAuthorLink());\n";
		$res .= "endif;\n";
		$res .= "?>\n";
		
		return $res;
	}

	public static function multiTocItemCategory($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		
		$mask = isset($attr['mask']) ? sprintf($f,'"'.$attr['mask'].'"') : '\' - <span class="toc-item-cat">%s</span>\'';
		
		$res = "<?php\n";
		$res .= "\$mask = ".$mask.";\n";
		$res .= "if (((\dcCore::app()->ctx->multitoc_type == 'cat' && \dcCore::app()->ctx->multitoc_settings['cat']['display_cat'])\n";
		$res .= "|| (\dcCore::app()->ctx->multitoc_type == 'tag' && \dcCore::app()->ctx->multitoc_settings['tag']['display_cat'])\n";
		$res .= "|| (\dcCore::app()->ctx->multitoc_type == 'alpha' && \dcCore::app()->ctx->multitoc_settings['alpha']['display_cat']))\n";
		$res .= "&& \dcCore::app()->ctx->multitoc_items->cat_title !== null\n";
		$res .= ") :\n";
			$res .= 
			"\$link = sprintf('<a href=\"%1\$s\">%2\$s</a>',".
			sprintf($f,'dcCore::app()->blog->url.dcCore::app()->url->getBase("category")."/".dcCore::app()->ctx->multitoc_items->cat_url').",".
			sprintf($f,'dcCore::app()->ctx->multitoc_items->cat_title').");\n".
			"echo sprintf(\$mask,\$link);\n";
		$res .= "endif;\n";
		$res .= "?>\n";
		
		return $res;
	}

	public static function multiTocItemNbComments($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		
		$mask = isset($attr['mask']) ? sprintf($f,'"'.$attr['mask'].'"') : '\' - <span class="toc-item-com">%s</span>\'';
		
		$res = "<?php\n";
		$res .= "\$mask = ".$mask.";\n";
		$res .= "if ((\dcCore::app()->ctx->multitoc_type == 'cat' && \dcCore::app()->ctx->multitoc_settings['cat']['display_nb_com'])\n";
		$res .= "|| (\dcCore::app()->ctx->multitoc_type == 'tag' && \dcCore::app()->ctx->multitoc_settings['tag']['display_nb_com'])\n";
		$res .= "|| (\dcCore::app()->ctx->multitoc_type == 'alpha' && \dcCore::app()->ctx->multitoc_settings['alpha']['display_nb_com'])\n";
		$res .= ") :\n";
			$res .= "echo sprintf(\$mask,\dcCore::app()->ctx->multitoc_items->nb_comment);\n";
		$res .= "endif;\n";
		$res .= "?>\n";
		
		return $res;
	}

	public static function multiTocItemNbTrackbacks($attr)
	{
		$f = dcCore::app()->tpl->getFilters($attr);
		
		$mask = isset($attr['mask']) ? sprintf($f,'"'.$attr['mask'].'"') : '\' - <span class="toc-item-tb">%s</span>\'';
		
		$res = "<?php\n";
		$res .= "\$mask = ".$mask.";\n";
		$res .= "if ((\dcCore::app()->ctx->multitoc_type == 'cat' && \dcCore::app()->ctx->multitoc_settings['cat']['display_nb_tb'])\n";
		$res .= "|| (\dcCore::app()->ctx->multitoc_type == 'tag' && \dcCore::app()->ctx->multitoc_settings['tag']['display_nb_tb'])\n";
		$res .= "|| (\dcCore::app()->ctx->multitoc_type == 'alpha' && \dcCore::app()->ctx->multitoc_settings['alpha']['display_nb_tb'])\n";
		$res .= ") :\n";
			$res .= "echo sprintf(\$mask,\dcCore::app()->ctx->multitoc_items->nb_trackback);\n";
		$res .= "endif;\n";
		$res .= "?>\n";
		
		return $res;
	}
	
	public static function multiTocPageTitle()
	{
		$res = "<?php\n";
		$res .= "echo __('Table of content');\n";
		
		$res .= "if (\dcCore::app()->ctx->multitoc_type == 'cat') :\n";
			$res .= "echo ' - '.__('By category');\n";
		$res .= "elseif (\dcCore::app()->ctx->multitoc_type == 'tag') :\n";
			$res .= "echo ' - '.__('By tag');\n";
		$res .= "elseif (\dcCore::app()->ctx->multitoc_type == 'alpha') :\n";
			$res .= "echo ' - '.__('By alpha order');\n";
		$res .= "endif;\n";
		$res .= "?>\n";
		
		return $res;
	}

	public static function multiTocIf($attr,$content)
	{
		$if = array();
		
		$operator = isset($attr['operator']) ? $this->getOperator($attr['operator']) : '&&';
		
		if (isset($attr['type'])) {
			$if[] = "\dcCore::app()->ctx->multitoc_type == '".addslashes($attr['type'])."'";
		}
		
		if (!empty($if)) {
			return '<?php if('.implode(' '.$operator.' ',$if).') : ?>'.$content.'<?php endif; ?>';
		} else {
			return $content;
		}
	}
	
	public static function multiTocMetaData($attr,$content)
	{
		$type = isset($attr['type']) ? addslashes($attr['type']) : 'tag';
		
		$sortby = 'meta_id_lower';
		if (isset($attr['sortby']) && $attr['sortby'] == 'count') {
			$sortby = 'count';
		}
		
		$order = 'asc';
		if (isset($attr['order']) && $attr['order'] == 'desc') {
			$order = 'desc';
		}
		
		$res =
		"<?php\n".
		'$objMeta = new dcMeta(dcCore::app()); '.
		"\dcCore::app()->ctx->meta = \$objMeta->getMetaRecordset(\dcCore::app()->ctx->multitoc_items->post_meta,'".$type."'); ".
		"\dcCore::app()->ctx->meta->sort('".$sortby."','".$order."'); ".
		'?>';
		
		$res .= "<?php if (\dcCore::app()->ctx->multitoc_settings[\dcCore::app()->ctx->multitoc_type]['display_tag']) : ?>\n";
		$res .= 
		'<?php while (dcCore::app()->ctx->meta->fetch()) : ?>'.$content.'<?php endwhile; '.
		'dcCore::app()->ctx->meta = null; unset($objMeta); ?>';
		$res .= "<?php endif; ?>\n";
		
		return $res;
	}
}

class multiTocPublic
{
	public static function multiTocGroupPGSQL($order_group)
	{
		$params = array();
		$params['columns'] = array(
			'UPPER(SUBSTRING(post_title,1,1)) AS post_letter'
		);
		$params['no_content'] = true;
		$params['order'] = $order_group;
		
		$rs = dcCore::app()->blog->getPosts($params);
		
		$array = array();
		
		# the "post_letter" of the previous post in the list
		$previous_letter = '';
		
		# read the "post_letter" field of the posts
		# and count occurences of each letter
		while ($rs->fetch())
		{
			# this letter
			$letter = $rs->post_letter;
			
			# the letter has changed
			if ($letter != $previous_letter)
			{
				# the letter has changed but the previous letter was
				# the empty string, ignore this case
				if (!empty($previous_letter))
				{
					# store the previous letter,
					# which counter has been incremented
					$array[] = array(
						'post_letter' => $previous_letter,
						'count' => $letter_count
					);
				}
				
				# initialize the counter
				$letter_count = 1;
				# remember this letter to count it
				$previous_letter = $letter;
			}
			else
			{
				# the letter has not changed, increment the counter
				$letter_count++;
			}
		}
		
		# don't forget the last letter
		$array[] = array(
			'post_letter' => $letter,
			'count' => $letter_count
		);
		
		return(staticRecord::newFromArray($array));
	}
}

dcCore::app()->addBehavior('publicBreadcrumb',array('extMultiToc','publicBreadcrumb'));

class extMultiToc
{
    public static function publicBreadcrumb($context,$separator)
    {
        if ($context == 'multitoc') {
            return __('Table of content');
        }
    }
}