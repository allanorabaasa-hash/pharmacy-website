/**
 * God's Care Pharmacy — homepage photo slider.
 *
 * Turns the image blocks inside a ".gcp-slides" group into a crossfading
 * slideshow behind the hero text. Adds dot navigation and a pause button
 * (auto-moving content must be pausable for accessibility). Autoplay is
 * off for visitors who ask their device to reduce motion.
 */
( function () {
	'use strict';

	var INTERVAL = 6000;
	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function init( wrap ) {
		var slides = Array.prototype.slice.call( wrap.children ).filter( function ( el ) {
			return el.classList.contains( 'wp-block-image' );
		} );
		if ( slides.length < 2 ) {
			return;
		}

		var hero = wrap.closest( '.gcp-hero' ) || wrap.parentNode;
		var current = 0;
		var timer = null;
		var paused = reduceMotion;

		wrap.classList.add( 'is-ready' );
		wrap.setAttribute( 'role', 'region' );
		wrap.setAttribute( 'aria-roledescription', 'carousel' );
		wrap.setAttribute( 'aria-label', "Photos of God's Care Pharmacy" );

		var nav = document.createElement( 'div' );
		nav.className = 'gcp-slides-nav';

		var dots = slides.map( function ( slide, n ) {
			slide.setAttribute( 'aria-roledescription', 'slide' );
			slide.setAttribute( 'aria-label', ( n + 1 ) + ' of ' + slides.length );

			var dot = document.createElement( 'button' );
			dot.type = 'button';
			dot.className = 'gcp-dot';
			dot.setAttribute( 'aria-label', 'Show photo ' + ( n + 1 ) + ' of ' + slides.length );
			dot.addEventListener( 'click', function () {
				show( n );
				start();
			} );
			nav.appendChild( dot );
			return dot;
		} );

		var toggle = document.createElement( 'button' );
		toggle.type = 'button';
		toggle.className = 'gcp-slides-toggle';
		toggle.addEventListener( 'click', function () {
			paused = ! paused;
			updateToggle();
			if ( paused ) {
				stop();
			} else {
				start();
			}
		} );
		nav.appendChild( toggle );
		hero.appendChild( nav );

		function show( n ) {
			slides[ current ].classList.remove( 'is-active' );
			slides[ current ].setAttribute( 'aria-hidden', 'true' );
			dots[ current ].removeAttribute( 'aria-current' );

			current = ( n + slides.length ) % slides.length;

			slides[ current ].classList.add( 'is-active' );
			slides[ current ].removeAttribute( 'aria-hidden' );
			dots[ current ].setAttribute( 'aria-current', 'true' );
		}

		function start() {
			stop();
			if ( ! paused ) {
				timer = window.setInterval( function () {
					show( current + 1 );
				}, INTERVAL );
			}
		}

		function stop() {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
		}

		function updateToggle() {
			toggle.classList.toggle( 'is-paused', paused );
			toggle.setAttribute( 'aria-label', paused ? 'Play slideshow' : 'Pause slideshow' );
		}

		// Hold the current photo while someone reads or uses the hero.
		hero.addEventListener( 'mouseenter', stop );
		hero.addEventListener( 'mouseleave', start );
		hero.addEventListener( 'focusin', stop );
		hero.addEventListener( 'focusout', start );
		document.addEventListener( 'visibilitychange', function () {
			if ( document.hidden ) {
				stop();
			} else {
				start();
			}
		} );

		slides.forEach( function ( slide ) {
			slide.setAttribute( 'aria-hidden', 'true' );
		} );
		show( 0 );
		updateToggle();
		start();
	}

	function boot() {
		document.querySelectorAll( '.gcp-slides' ).forEach( init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
