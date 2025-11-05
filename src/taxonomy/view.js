import { store, getElement } from '@wordpress/interactivity';

const updateURL = async ( action, value, name ) => {
	const url = new URL( action );
	if ( value || name === 's' ) {
		url.searchParams.set( name, value );
	} else {
		url.searchParams.delete( name );
	}
	const { actions } = await import( '@wordpress/interactivity-router' );
	await actions.navigate( url.toString() );
};

const { state } = store( 'query-filter', {
	actions: {
                *navigate( e ) {
                        e.preventDefault();
                        const { actions } = yield import(
                                '@wordpress/interactivity-router'
                        );
                        yield actions.navigate( e.target.value );
                },
                *toggleCheckboxes( e ) {
                        e.preventDefault();
                        const { ref } = getElement();
                        const container = ref.closest('[data-query-filter-base-url]');

                        if ( ! container ) {
                                return;
                        }

                        const baseUrl = container.dataset.queryFilterBaseUrl;
                        const queryVar = container.dataset.queryFilterQueryVar;
                        const pageVar = container.dataset.queryFilterPageVar;
                        const checkboxes = Array.from(
                                container.querySelectorAll('input[type="checkbox"]')
                        );
                        const selected = checkboxes
                                .filter( ( input ) => input.checked )
                                .map( ( input ) => input.value )
                                .filter( Boolean );
                        const url = new URL( baseUrl, window.location.origin );

                        url.searchParams.delete( queryVar );
                        url.searchParams.delete( pageVar );

                        if ( selected.length > 0 ) {
                                url.searchParams.set( queryVar, selected.join( ',' ) );
                        }

                        const { actions } = yield import(
                                '@wordpress/interactivity-router'
                        );
                        yield actions.navigate( url.toString() );
                },
                *search( e ) {
                        e.preventDefault();
                        const { ref } = getElement();
                        let action, name, value;
			if ( ref.tagName === 'FORM' ) {
				const input = ref.querySelector( 'input[type="search"]' );
				action = ref.action;
				name = input.name;
				value = input.value;
			} else {
				action = ref.closest( 'form' ).action;
				name = ref.name;
				value = ref.value;
			}

			// Don't navigate if the search didn't really change.
			if ( value === state.searchValue ) return;

			state.searchValue = value;

			yield updateURL( action, value, name );
		},
	},
} );
