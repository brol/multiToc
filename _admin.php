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

dcCore::app()->addBehavior('adminPostEditor', array('multiTocBehaviors', 'adminPostEditor'));
dcCore::app()->addBehavior('ckeditorExtraPlugins', array('multiTocBehaviors', 'ckeditorExtraPlugins'));

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
            dcAuth::PERMISSION_USAGE,
            dcAuth::PERMISSION_CONTENT_ADMIN,
        ]),
    ]);
});
