<?php
$container_guid = (int) elgg_extract('container_guid', $vars);

$dbprefix = elgg_get_config('dbprefix');
$user = elgg_get_logged_in_user_entity();
$groups = new ElggBatch('elgg_get_entities_from_relationship', array(
	'selects' => array(
		'ge.name AS name',
	),
	'joins' => array(
		"JOIN {$dbprefix}groups_entity ge ON ge.guid = e.guid",
	),
	'order_by' => 'ge.name ASC',
	'relationship' => 'member',
	'relationship_guid' => (int) $user->guid,
	'inverse_relationship' => false,
	'metadata_name_value_pairs' => array(
		'name' => 'forum_enable',
		'value' => 'yes',
	),
	'limit' => 0,
	'callback' => false,
		));

$options_values = array();
foreach ($groups as $group) {
	$options_values["$group->guid"] = $group->name;
}

$options_values = elgg_trigger_plugin_hook('allowed_containers', 'object:discussion', $vars, $options_values);
if (empty($options_values)) {
	// @todo: do we need to terminate form rendering all together?
}

if (array_key_exists("$container_guid", $options_values)) {
	return;
}

asort($options_values);

$placeholder = array('' => elgg_echo('group:discussions:container:select'));
$options_values = $placeholder + $options_values;
?>
<div>
	<label><?php echo elgg_echo('group:discussions:container'); ?></label><br />
	<?php
	echo elgg_view('input/select', array(
		'options_values' => $options_values,
		'class' => 'select-discussion-container',
		'required' => true,
	));
	?>
</div>
<script>
	require(['input/discussions/container']);
</script>
