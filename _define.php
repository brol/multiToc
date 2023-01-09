<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of multiToc, a plugin for Dotclear.
# 
# Copyright (c) 2009-2016 Tomtom and contributors
# 
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'multiToc',
    'Makes posts or content\'s tables of content',
    'Tomtom, Kozlika, Franck Paul, Pierre Van Glabeke and contributors',
    '2.0-dev',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
        'type'       => 'plugin',
        'support'    => 'http://forum.dotclear.org/viewtopic.php?pid=332972#p332972',
        'details'    => 'https://plugins.dotaddict.org/dc2/details/' . basename(__DIR__),
    ]
);
