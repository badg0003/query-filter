import { useState, useEffect } from '@wordpress/element';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

const Edit = ({ attributes, setAttributes }) => {
  const { showLabel, emptyLabel, label } = attributes;
  const [dates, setDates] = useState([]);

  // Fetch distinct years via the custom REST endpoint.
  useEffect(() => {
    apiFetch({ path: '/query-filter/v1/years' }).then((years) => {
      setDates(years);
    });
  }, []);

  // Provide a default label if none is set.
  if (dates.length && !label) {
    setAttributes({
      label: __('Dates', 'query-filter'),
    });
  }
  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Date Filter Settings', 'query-filter')}>
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
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps({ className: 'wp-block-query-filter' })}>
        {showLabel && (
          <label className="wp-block-query-filter-post-type__label wp-block-query-filter__label">
            {label || __('Content Type', 'query-filter')}
          </label>
        )}
        <select
          className="wp-block-query-filter-date__select wp-block-query-filter__select"
          inert
        >
          <option value="">
            {emptyLabel || __('All Dates', 'query-filter')}
          </option>
          {dates.map((year) => (
            <option key={year} value={year}>
              {year}
            </option>
          ))}
        </select>
      </div>
    </>
  );
};

export default Edit;
