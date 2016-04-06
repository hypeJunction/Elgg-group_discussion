<?php
$entity = elgg_extract('entity', $vars);
?>
<div class="elgg-field">
	<label class="elgg-field-label"><?php echo elgg_echo('widget:numbertodisplay'); ?></label>
	<?php
	echo elgg_view('input/select', array(
		'name' => 'params[num_display]',
		'value' => $entity->num_display,
		'options' => array(5, 10, 15, 20),
	));
	?>
</div>
<?php
if (elgg_in_context('dashboard')) {
	$page_owner = elgg_get_site_entity();
} else {
	$page_owner = elgg_get_page_owner_entity();
}
if ($page_owner && $page_owner->canWriteToContainer(0, 'object', 'discussion')) {
	?>
	<div class="elgg-field">
		<label class="elgg-field-label"><?php echo elgg_echo('group:discussion:widget:show_form'); ?></label>
		<?php
		echo elgg_view('input/select', array(
			'name' => 'params[show_form]',
			'value' => isset($entity->show_form) ? $entity->show_form : true,
			'options_values' => array(
				0 => elgg_echo('option:no'),
				1 => elgg_echo('option:yes'),
			)
		));
		?>
	</div>
	<?php
}