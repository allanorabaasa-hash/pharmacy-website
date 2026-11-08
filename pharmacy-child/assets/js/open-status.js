/**
 * God's Care Pharmacy — "Open now / Closed" badge.
 * Reads opening hours from the [gcp_open_status] shortcode's data attributes
 * and works out the current time in the pharmacy's time zone (Africa/Kampala),
 * so visitors abroad or on cached pages still see the correct status.
 */
( function () {
	'use strict';

	var DAYS = [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ];

	function nowInZone( tz ) {
		var parts = new Intl.DateTimeFormat( 'en-GB', {
			timeZone: tz,
			weekday: 'short',
			hour: '2-digit',
			minute: '2-digit',
			hour12: false
		} ).formatToParts( new Date() );

		var out = {};
		parts.forEach( function ( p ) { out[ p.type ] = p.value; } );
		return {
			day: out.weekday.toLowerCase().slice( 0, 3 ),
			minutes: ( parseInt( out.hour, 10 ) % 24 ) * 60 + parseInt( out.minute, 10 )
		};
	}

	function toMinutes( hhmm ) {
		var p = hhmm.split( ':' );
		return parseInt( p[ 0 ], 10 ) * 60 + parseInt( p[ 1 ], 10 );
	}

	function nextOpening( hours, today ) {
		var start = DAYS.indexOf( today );
		for ( var i = 1; i <= 7; i++ ) {
			var d = DAYS[ ( start + i ) % 7 ];
			if ( hours[ d ] ) {
				var label = i === 1 ? 'tomorrow' : d.charAt( 0 ).toUpperCase() + d.slice( 1 );
				return label + ' at ' + hours[ d ][ 0 ];
			}
		}
		return '';
	}

	function update( el ) {
		var hours;
		try {
			hours = JSON.parse( el.getAttribute( 'data-hours' ) );
		} catch ( e ) {
			return;
		}

		var now = nowInZone( el.getAttribute( 'data-tz' ) || 'Africa/Kampala' );
		var today = hours[ now.day ];
		var open = today && now.minutes >= toMinutes( today[ 0 ] ) && now.minutes < toMinutes( today[ 1 ] );

		el.classList.toggle( 'is-open', !! open );
		el.classList.toggle( 'is-closed', ! open );

		if ( open ) {
			el.textContent = 'Open now · closes at ' + today[ 1 ];
		} else if ( today && now.minutes < toMinutes( today[ 0 ] ) ) {
			el.textContent = 'Closed · opens today at ' + today[ 0 ];
		} else {
			el.textContent = 'Closed · opens ' + nextOpening( hours, now.day );
		}
	}

	function init() {
		var badges = document.querySelectorAll( '.gcp-open-status[data-hours]' );
		badges.forEach( update );
		setInterval( function () { badges.forEach( update ); }, 60000 );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
