<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of multiToc, a plugin for Dotclear.
# 
# Copyright (c) 2009-2015 Tomtom and contributors
# 
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

$this->registerModule(
		/* Name */			"multiToc",
		/* Description*/		"Makes posts or content's tables of content",
		/* Author */			"Tomtom, Kozlika, Franck Paul, Pierre Van Glabeke",
		/* Version */			'1.10',
	/* Properties */
	array(
		'permissions' => 'usage,contentadmin',
		'type' => 'plugin',
		'dc_min' => '2.8',
		'support' => 'http://forum.dotclear.org/viewtopic.php?pid=332972#p332972',
		'details' => 'http://plugins.dotaddict.org/dc2/details/multiToc'
		)
);