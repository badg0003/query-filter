<?php
if ($block->context['query']['inherit']) {
	$query_var = 'query-date';
	$page_var  = 'page';
	$base_url  = str_replace('/page/' . get_query_var('paged'), '', remove_query_arg([$query_var, $page_var]));
} else {
	$query_id  = $block->context['queryId'] ?? 0;
	$query_var = sprintf('query-%d-date', $query_id);
	$page_var  = isset($block->context['queryId']) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$base_url  = remove_query_arg([$query_var, $page_var]);
}

global $wpdb;
$years = $wpdb->get_col($wpdb->prepare(
	"SELECT DISTINCT YEAR(post_date) as year FROM $wpdb->posts WHERE post_status = %s AND post_type = %s ORDER BY year DESC",
	'publish',
	'post'
));

if (empty($years)) {
	return;
}

$id = 'query-filter-' . wp_generate_uuid4();
?>

<div <?php echo get_block_wrapper_attributes(['class' => 'wp-block-query-filter']); ?> data-wp-interactive="query-filter" data-wp-context="{}">
    <label class="wp-block-query-filter-date__label wp-block-query-filter__label<?php echo $attributes['showLabel'] ? '' : ' screen-reader-text' ?>" for="<?php echo esc_attr($id); ?>">
        <?php echo esc_html($attributes['label'] ?? __('Years', 'query-filter')); ?>
    </label>
    <select class="wp-block-query-filter-date__select wp-block-query-filter__select" id="<?php echo esc_attr($id); ?>" data-wp-on--change="actions.navigate">
        <option value="<?php echo esc_attr($base_url); ?>">
            <?php echo esc_html($attributes['emptyLabel'] ?: __('All Years', 'query-filter')); ?>
        </option>
        <?php foreach ($years as $year) : ?>
            <?php
            $url = add_query_arg([$query_var => $year, $page_var => false], $base_url);
            $selected = selected($year, wp_unslash($_GET[$query_var] ?? ''), false);
            ?>
            <option value="<?php echo esc_url($url); ?>" <?php echo $selected; ?>>
                <?php echo esc_html($year); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>