<?php

/**
 * Group Discussions
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2015, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'group_discussion_init');

/**
 * Initialize the plugin
 * @return void
 */
function group_discussion_init() {

	elgg_register_plugin_hook_handler('view_vars', 'forms/discussion/save', 'group_discussion_filter_form_vars');
	elgg_register_plugin_hook_handler('view_vars', 'page/layouts/widgets', 'group_discussion_filter_widget_layout_vars');

	// Add a group picker before the discussions edit form
	elgg_extend_view('forms/discussion/save', 'input/discussions/container', 100);

	// Group tools cleanup
	elgg_unregister_widget_type('start_discussion');
	elgg_unregister_widget_type('group_forum_topics');
	elgg_register_widget_type('discussion', elgg_echo('group:discussion:widget'), elgg_echo('group:discussion:widget:desc'), ['dashboard', 'profile', 'groups']);

	elgg_register_ajax_view('lists/discussions');
	elgg_register_ajax_view('input/discussions/access');

	add_group_tool_option('admin_only_discussions', elgg_echo('group:discussion:admin_only'), false);
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'group_discussion_container_permissions_check');
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'group_discussion_reply_container_permissions_check');
}

/**
 * Filter form variables
 *
 * @param string $hook   "view_vars"
 * @param string $type   "forms/discussions/save"
 * @param array  $return View vars
 * @param array  $params Hook params
 * @return array
 */
function group_discussion_filter_form_vars($hook, $type, $return, $params) {

	$guid = elgg_extract('guid', $return);
	$container_guid = elgg_extract('container_guid', $return);
	if ($container_guid) {
		return;
	}

	$entity = null;
	if ($guid) {
		$entity = get_entity($guid);
	}

	if ($entity) {
		$return['entity'] = $entity;
		$container_guid = $entity->getContainerGUID();
	} else {
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof ElggGroup) {
			$container_guid = $page_owner->guid;
		}
	}

	$return['container_guid'] = $container_guid;
	return $return;
}

/**
 * Remove discussions widget from group context if discussions are not enabled
 *
 * @param string $hook   "view_vars"
 * @param string $type   "page/layouts/widgets"
 * @param array  $return View vars
 * @param array  $params Hook params
 * @return void
 */
function group_discussion_filter_widget_layout_vars($hook, $type, $return, $params) {

	//$owner_guid = elgg_extract('owner_guid', $return, elgg_get_page_owner_guid()); // not yet supported
	$owner = elgg_get_page_owner_entity();
	if ($owner instanceof ElggGroup && $owner->forum_enable != 'yes') {
		elgg_unregister_widget_type('group_discussions');
	}
}

/**
 * Check group settings to disallow creation of new discussions
 *
 * @param string $hook   "container_permissions_check"
 * @param string $type   "object"
 * @param bool   $return Permission
 * @param array  $params Hook params
 * @return bool
 */
function group_discussion_container_permissions_check($hook, $type, $return, $params) {

	$user = elgg_extract('user', $params);
	$container = elgg_extract('container', $params);
	$subtype = elgg_extract('subtype', $params);

	if ($subtype !== 'discussion') {
		return;
	}

	if (!$container instanceof ElggGroup) {
		return;
	}

	if ($container->forum_enable != 'yes') {
		return false;
	}

	if ($container->admin_only_discussions_enable == 'yes' && !$container->canEdit($user->guid)) {
		return false;
	}
}

/**
 * Discussion replies should not inherit permissions from discussion but from the parent (group)
 *
 * @param string $hook   "container_permissions_check"
 * @param string $type   "object"
 * @param bool   $return Permission
 * @param array  $params Hook params
 * @return bool
 */
function group_discussion_reply_container_permissions_check($hook, $type, $return, $params) {

	$user = elgg_extract('user', $params);
	$container = elgg_extract('container', $params);
	$subtype = elgg_extract('subtype', $params);

	if (!elgg_instanceof($container, 'object', 'discussion') || $subtype !== 'discussion_reply') {
		return;
	}

	$group = $container->getContainerEntity();
	if ($group instanceof \ElggGroup && $group->forum_enable != 'yes') {
		return false;
	}
	return $group->canWriteToContainer($user->guid);
}
