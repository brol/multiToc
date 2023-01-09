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

if (!defined('DC_CONTEXT_ADMIN')) { return; }

dcCore::app()->addBehavior('adminPostHeaders',array('multiTocBehaviors','postHeaders'));
dcCore::app()->addBehavior('adminPageHeaders',array('multiTocBehaviors','postHeaders'));

// Admin sidebar menu
dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
    __('Tables of content'),
    dcCore::app()->adminurl->get('admin.plugin.' . basename(__DIR__)),
    dcPage::getPF(basename(__DIR__) . '/icon.png'),
    preg_match(
        '/' . preg_quote(dcCore::app()->adminurl->get('admin.plugin.' . basename(__DIR__))) . '(&.*)?$/',
        $_SERVER['REQUEST_URI']
    ),
    dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
        dcAuth::PERMISSION_CONTENT_ADMIN,
    ]), dcCore::app()->blog->id)
);

// Admin dashbaord favorite
dcCore::app()->addBehavior('adminDashboardFavoritesV2', function ($favs) {
    $favs->register(basename(__DIR__), [
        'title'       => __('Tables of content'),
        'url'         => dcCore::app()->adminurl->get('admin.plugin.' . basename(__DIR__)),
        'small-icon'  => dcPage::getPF(basename(__DIR__) . '/icon.png'),
        'large-icon'  => dcPage::getPF(basename(__DIR__) . '/icon-big.png'),
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
    ]);
});
