<?php

elgg_pop_breadcrumb();
elgg_push_breadcrumb(elgg_echo('discussion'));

elgg_register_title_button();

$content = elgg_view('lists/discussions', array(
	'show_rel' => true,
	'show_search' => true,
	'show_sort' => true,
	'sort' => get_input('sort', 'last_action::desc'),
	'options' => array(
		'order_by' => 'e.last_action desc',
		'preload_owners' => true,
	)
));

$title = elgg_echo('discussion:latest');

$params = array(
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('discussion/sidebar'),
	'filter' => '',
);
$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);