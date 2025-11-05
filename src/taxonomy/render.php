<?php
if ( empty( $attributes['taxonomy'] ) ) {
	return;
}

$id = 'query-filter-' . wp_generate_uuid4();

$taxonomy = get_taxonomy( $attributes['taxonomy'] );

if ( empty( $block->context['query']['inherit'] ) ) {
	$query_id = $block->context['queryId'] ?? 0;
	$query_var = sprintf( 'query-%d-%s', $query_id, $attributes['taxonomy'] );
	$page_var = isset( $block->context['queryId'] ) ? 'query-' . $block->context['queryId'] . '-page' : 'query-page';
	$base_url = remove_query_arg( [ $query_var, $page_var ] );
} else {
	$query_var = sprintf( 'query-%s', $attributes['taxonomy'] );
	$page_var = 'page';
	$base_url = str_replace( '/page/' . get_query_var( 'paged' ), '', remove_query_arg( [ $query_var, $page_var ] ) );
}

$terms = get_terms( [
        'hide_empty' => true,
        'taxonomy' => $attributes['taxonomy'],
        'number' => 100,
] );

if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return;
}

$display_type = $attributes['displayType'] ?? 'select';

$query_value = wp_unslash( $_GET[ $query_var ] ?? '' );
$selected_terms = [];

if ( is_array( $query_value ) ) {
        $selected_terms = array_filter( array_map( 'sanitize_title', $query_value ) );
} elseif ( is_string( $query_value ) && $query_value !== '' ) {
        $selected_terms = array_filter( array_map( 'sanitize_title', explode( ',', $query_value ) ) );
}

$wrapper_attributes = [
        'class' => 'wp-block-query-filter',
        'data-wp-interactive' => 'query-filter',
        'data-wp-context' => '{}',
];

if ( $display_type === 'checkboxes' ) {
        $wrapper_attributes['data-query-filter-base-url'] = esc_url( $base_url );
        $wrapper_attributes['data-query-filter-query-var'] = esc_attr( $query_var );
        $wrapper_attributes['data-query-filter-page-var'] = esc_attr( $page_var );
}

$label_text = $attributes['label'] ?? $taxonomy->label;
$label_classes = 'wp-block-query-filter-taxonomy__label wp-block-query-filter__label';

if ( empty( $attributes['showLabel'] ) ) {
        $label_classes .= ' screen-reader-text';
}
?>

<div <?php echo get_block_wrapper_attributes( $wrapper_attributes ); ?>>
        <?php if ( $display_type === 'buttons' ) : ?>
                <?php
                $is_all_selected = empty( $selected_terms );
                $label_id = esc_attr( $id . '-label' );
                ?>
                <span id="<?php echo $label_id; ?>" class="<?php echo esc_attr( $label_classes ); ?>"><?php echo esc_html( $label_text ); ?></span>
                <div class="wp-block-query-filter-taxonomy__buttons" role="group" aria-labelledby="<?php echo $label_id; ?>">
                        <button type="button" class="wp-block-query-filter-taxonomy__button<?php echo $is_all_selected ? ' is-active' : ''; ?>" value="<?php echo esc_attr( $base_url ); ?>" data-wp-on--click="actions.navigate">
                                <?php echo esc_html( $attributes['emptyLabel'] ?: __( 'All', 'query-filter' ) ); ?>
                        </button>
                        <?php foreach ( $terms as $term ) :
                                $term_url = add_query_arg(
                                        [
                                                $query_var => $term->slug,
                                                $page_var => false,
                                        ],
                                        $base_url
                                );
                                ?>
                                <button
                                        type="button"
                                        class="wp-block-query-filter-taxonomy__button<?php echo in_array( $term->slug, $selected_terms, true ) ? ' is-active' : ''; ?>"
                                        value="<?php echo esc_attr( $term_url ); ?>"
                                        data-wp-on--click="actions.navigate"
                                >
                                        <?php echo esc_html( $term->name ); ?>
                                </button>
                        <?php endforeach; ?>
                </div>
        <?php elseif ( $display_type === 'checkboxes' ) : ?>
                <fieldset class="wp-block-query-filter-taxonomy__checkboxes" data-wp-on--change="actions.toggleCheckboxes">
                        <legend class="<?php echo esc_attr( $label_classes ); ?>"><?php echo esc_html( $label_text ); ?></legend>
                        <?php foreach ( $terms as $term ) : ?>
                                <label class="wp-block-query-filter-taxonomy__checkbox">
                                        <input
                                                type="checkbox"
                                                value="<?php echo esc_attr( $term->slug ); ?>"
                                                <?php checked( in_array( $term->slug, $selected_terms, true ) ); ?>
                                        />
                                        <span><?php echo esc_html( $term->name ); ?></span>
                                </label>
                        <?php endforeach; ?>
                </fieldset>
        <?php else : ?>
                <label class="<?php echo esc_attr( $label_classes ); ?>" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label_text ); ?></label>
                <select class="wp-block-query-filter-post-type__select wp-block-query-filter__select" id="<?php echo esc_attr( $id ); ?>" data-wp-on--change="actions.navigate">
                        <option value="<?php echo esc_attr( $base_url ) ?>"><?php echo esc_html( $attributes['emptyLabel'] ?: __( 'All', 'query-filter' ) ); ?></option>
                        <?php foreach ( $terms as $term ) : ?>
                                <option value="<?php echo esc_attr( add_query_arg( [ $query_var => $term->slug, $page_var => false ], $base_url ) ) ?>" <?php selected( in_array( $term->slug, $selected_terms, true ) ); ?>><?php echo esc_html( $term->name ); ?></option>
                        <?php endforeach; ?>
                </select>
        <?php endif; ?>
</div>
