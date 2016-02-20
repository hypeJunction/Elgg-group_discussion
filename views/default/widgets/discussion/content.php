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

$widget_form_id = "discussion-add-{$page_owner->guid}";
$widget_form = elgg_view('output/url', array(
	'rel' => 'toggle',
	'href' => "#$widget_form_id",
	'text' => elgg_echo('discussion:add'),
	'class' => 'elgg-button elgg-button-action elgg-button-discussion-add',
		));

$form = elgg_view_form('discussion/save');
$form .= elgg_view('output/url', array(
	'href' => '#',
	'text' => elgg_view_icon('remove'),
	'rel' => 'close',
));

$widget_form .= elgg_format_element('div', [
	'id' => "discussion-add-{$page_owner->guid}",
	'class' => 'hidden',
], $form);

echo elgg_format_element('div', ['class' => 'group-discussion-widget-form'], $widget_form);

echo elgg_view('lists/discussions', array(
	'entity' => $page_owner,
	'limit' => $entity->limit ? : 5,
	'pagination' => false,
));

$more_link = elgg_view('output/url', array(
	'text' => elgg_echo('link:view:all'),
	'href' => elgg_normalize_url($more_href),
		));
echo elgg_format_element('span', [
	'class' => 'elgg-widget-more',
		], $more_link);
?>
<script>
	require(['widgets/discussion/content']);
</script>