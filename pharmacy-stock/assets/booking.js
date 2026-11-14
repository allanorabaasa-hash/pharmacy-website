/**
 * God's Care Pharmacy — booking form helper.
 * When a date is picked, only the times the pharmacy is open that day stay
 * selectable (and, for today, only times at least 30 minutes away).
 * The server checks the same rules, so this is a convenience, not security.
 */
( function () {
	'use strict';

	var DAYS = [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ];
	var NAMES = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];

	function minutes( hhmm ) {
		var p = hhmm.split( ':' );
		return parseInt( p[ 0 ], 10 ) * 60 + parseInt( p[ 1 ], 10 );
	}

	function label( mins ) {
		var h = Math.floor( mins / 60 ), m = mins % 60;
		return ( h % 12 || 12 ) + ':' + ( m < 10 ? '0' : '' ) + m + ( h < 12 ? ' am' : ' pm' );
	}

	function init( form ) {
		var hours = JSON.parse( form.getAttribute( 'data-hours' ) || '{}' );
		var slot = parseInt( form.getAttribute( 'data-slot' ), 10 ) || 30;
		var today = form.getAttribute( 'data-today' );
		var now = minutes( form.getAttribute( 'data-now' ) || '00:00' );
		var date = form.querySelector( '[name="appointment_date"]' );
		var time = form.querySelector( '[name="appointment_time"]' );
		var help = form.querySelector( '.psm-day-hours' );
		if ( ! date || ! time ) {
			return;
		}

		function update() {
			var value = date.value;
			var options = time.querySelectorAll( 'option[value]:not([value=""])' );

			if ( ! value ) {
				options.forEach( function ( o ) { o.hidden = false; o.disabled = false; } );
				help.textContent = '';
				return;
			}

			var d = new Date( value + 'T12:00:00' );
			var key = DAYS[ d.getDay() ];
			var day = hours[ key ];
			var open = 0, close = -1;
			if ( day ) {
				open = minutes( day[ 0 ] );
				close = minutes( day[ 1 ] ) - slot;
			}
			var earliest = value === today ? now + 30 : 0;
			var any = false;

			options.forEach( function ( o ) {
				var t = minutes( o.value );
				var ok = day && t >= open && t <= close && t >= earliest;
				o.hidden = ! ok;
				o.disabled = ! ok;
				any = any || ok;
			} );

			if ( time.selectedOptions[ 0 ] && time.selectedOptions[ 0 ].disabled ) {
				time.value = '';
			}

			if ( ! day ) {
				help.textContent = 'We are closed on ' + NAMES[ d.getDay() ] + 's. Please choose another date.';
			} else if ( ! any ) {
				help.textContent = 'No more times left today. Please choose another date.';
			} else {
				help.textContent = NAMES[ d.getDay() ] + ': appointments from ' + label( open ) + ' to ' + label( close ) + '.';
			}
		}

		date.addEventListener( 'change', update );
		update();
	}

	function boot() {
		document.querySelectorAll( 'form.psm-booking' ).forEach( init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
