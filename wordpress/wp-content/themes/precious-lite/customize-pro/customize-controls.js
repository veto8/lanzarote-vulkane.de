( function( api ) {

	// Extends our custom "precious-lite" section.
	api.sectionConstructor['precious-lite'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );