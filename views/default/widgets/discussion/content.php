<?php
$entity = elgg_extract('entity', $vars);
/* @var $entity ElggWidget */

if (elgg_in_context('dashboard')) {
	$page_owner = elgg_get_site_entity();
	$href = "discussion/all";
} else {
	$page_owner = elgg_get_page_owner_entity();
	$href = $page_owner instanceof ElggUser ? "discussion/owner/$page_owner->guid" : "discussion/group/$page_owner->guid";
}

echo elgg_view('lists/discussions', array(
	'entity' => $page_owner,
	'options' => array(
		'limit' => $entity->num_display ? : 5,
		'pagination' => false,
	),
));

$add_link = '';
$show_form = isset($entity->show_form) ? $entity->show_form : $page_owner->canWriteToContainer(0, 'object', 'discussion');
if ($show_form) {
	$title = elgg_echo('discussion:add');
	$form = elgg_view_form('discussion/save');
	$mod = elgg_view_module('info', $title, $form, array(
		'id' => "group-discussion-add-$entity->guid",
		'class' => 'hidden',
	));
	$add_link = elgg_view('output/url', array(
		'text' => $title,
		'href' => "#group-discussion-add-$entity->guid",
		'rel' => 'toggle',
		'class' => 'mrl',
	));
	echo elgg_format_element('div', ['class' => 'group-discussion-widget-form'], $mod);
}

$more_link = elgg_view('output/url', array(
	'text' => elgg_echo('link:view:all'),
	'href' => elgg_normalize_url($href),
		));
echo elgg_format_element('span', [
	'class' => 'elgg-widget-more',
		], $add_link . $more_link);
?>
<script>
	require(['widgets/discussion/content']);
</script>