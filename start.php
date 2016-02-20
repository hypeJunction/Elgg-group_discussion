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
	elgg_register_widget_type('discussion', elgg_echo('group:discussion:widget'), elgg_echo('group:discussion:widget:desc'));

	elgg_register_ajax_view('lists/discussions');
	elgg_register_ajax_view('input/discussions/access');

	elgg_extend_view('elgg.css', 'widgets/discussion/content.css');
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