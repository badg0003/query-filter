<?php
if ($block->context['query']['inherit']) {
	$query_var = 'query-author';
	$page_var = 'page';
	$base_url = str_replace('/page/' . get_query_var('paged'), '', remove_query_arg([$query_var, $page_var]));
} else {
	$query_id = $block->context['queryId'] ?? 0;
	$query_var = sprintf('query-%d-author', $query_id);
	$page_var = isset($block->context['queryId']) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$base_url = remove_query_arg([$query_var, $page_var]);
}

$authors = get_users([
	'fields'             => 'all',
	'orderby'            => 'display_name',
	'order'              => 'ASC',
	'has_published_posts' => ['post'], // Limit to users who have authored at least one post
]);

if (empty($authors)) {
	return;
}

$id = 'query-filter-' . wp_generate_uuid4();
?>

<div <?php echo get_block_wrapper_attributes(['class' => 'wp-block-query-filter']); ?> data-wp-interactive="query-filter" data-wp-context="{}">
	<label class="wp-block-query-filter-post-type__label wp-block-query-filter__label<?php echo $attributes['showLabel'] ? '' : ' screen-reader-text' ?>" for="<?php echo esc_attr($id); ?>">
		<?php echo esc_html($attributes['label'] ?? __('Authors', 'query-filter')); ?>
	</label>
	<select class="wp-block-query-filter-post-type__select wp-block-query-filter__select" id="<?php echo esc_attr($id); ?>" data-wp-on--change="actions.navigate">
		<option value="<?php echo esc_attr($base_url) ?>">
			<?php echo esc_html($attributes['emptyLabel'] ?: __('All Authors', 'query-filter')); ?>
		</option>
		<?php foreach ($authors as $author) : ?>
			<?php
			$url = add_query_arg([$query_var => $author->user_nicename, $page_var => false], $base_url);
			$selected = selected($author->user_nicename, wp_unslash($_GET[$query_var] ?? ''), false);
			?>
			<option value="<?php echo esc_url($url); ?>" <?php echo $selected; ?>>
				<?php echo esc_html($author->display_name); ?>
			</option>
		<?php endforeach; ?>
	</select>
</div>