<?php

$entity = elgg_extract('entity', $vars);
$guid = (int) $entity->guid;

$base_url = elgg_normalize_url("ajax/view/lists/discussions") . '?' . parse_url(current_page_url(), PHP_URL_QUERY);
$base_url = elgg_http_add_url_query_elements($bse_url, array('guid' => $guid));

$list_class = (array) elgg_extract('list_class', $vars, array());
$list_class[] = 'elgg-list-discussions';

$item_class = (array) elgg_extract('item_class', $vars, array());
$item_class[] = 'elgg-discussion';

$options = (array) elgg_extract('options', $vars, array());

$list_options = array(
	'full_view' => false,
	'limit' => elgg_extract('limit', $vars, elgg_get_config('default_limit')) ? : 10,
	'list_class' => implode(' ', $list_class),
	'item_class' => implode(' ', $item_class),
	'no_results' => elgg_echo('discussion:none'),
	'pagination' => elgg_is_active_plugin('hypeLists') || !elgg_in_context('widgets'),
	'pagination_type' => 'infinite',
	'base_url' => $base_url,
	'list_id' => "discussions-$guid",
);

$getter_options = array(
	'types' => array('object'),
	'subtypes' => array('discussion'),
);

if ($entity instanceof ElggUser) {
	$getter_options['owner_guids'] = $guid;
} else if ($entity instanceof ElggGroup) {
	$getter_options['container_guids'] = $guid;
}

$options = array_merge($list_options, $options, $getter_options);

if (elgg_view_exists('lists/objects')) {
	$params = $vars;
	if (!isset($params['sort'])) {
		$params['sort'] = 'last_action::desc';
	}
	$params['options'] = $options;
	$params['callback'] = 'elgg_list_entities';
	echo elgg_view('lists/objects', $params);
} else {
	echo elgg_list_entities_from_relationship($options);
}