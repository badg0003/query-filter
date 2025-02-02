import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

export default function Edit({ attributes, setAttributes }) {
  const { emptyLabel, label, showLabel } = attributes;

  // Fetch authors instead of taxonomies
  const authors = useSelect(
    (select) =>
      select('core').getUsers({ who: 'authors', per_page: 100 }) || [],
    []
  );

  // Provide a default label if none is set
  if (authors.length && !label) {
    setAttributes({
      label: __('Authors', 'query-filter'),
    });
  }

  return (
    <div {...useBlockProps()}>
      <InspectorControls>
        <PanelBody title={__('Author Filter Settings', 'query-filter')}>
          <TextControl
            label={__('Label', 'query-filter')}
            value={label}
            onChange={(val) => setAttributes({ label: val })}
          />
          <TextControl
            label={__('Empty Option Label', 'query-filter')}
            value={emptyLabel}
            onChange={(val) => setAttributes({ emptyLabel: val })}
          />
          <ToggleControl
            label={__('Show Label', 'query-filter')}
            checked={!!showLabel}
            onChange={(val) => setAttributes({ showLabel: val })}
          />
        </PanelBody>
      </InspectorControls>
      <div {...useBlockProps({ className: 'wp-block-query-filter' })}>
        {showLabel && (
          <label className="wp-block-query-filter-author__label wp-block-query-filter__label">
            {label}
          </label>
        )}
        <select
          className="wp-block-query-filter-author__select wp-block-query-filter__select"
          inert
        >
          <option>{emptyLabel || __('All', 'query-filter')}</option>
          {authors.map((term) => (
            <option key={term.slug}>{term.name}</option>
          ))}
        </select>
      </div>
    </div>
  );
}
