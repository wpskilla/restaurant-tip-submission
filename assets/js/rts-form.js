/**
 * Restaurant Tip Submission — front-end AJAX handler.
 *
 * Depends on rtsData being present (set via wp_localize_script):
 *   rtsData.ajaxUrl  – admin-ajax.php URL
 *   rtsData.nonce    – wp_create_nonce( 'rts_submit_tip' )
 *   rtsData.action   – WP action name ('rts_submit_tip')
 *   rtsData.i18n     – translatable strings
 *
 * No jQuery. No build step required.
 */

( function () {
	'use strict';

	/* ── DOM refs ─────────────────────────────────────────────────────── */
	const form       = document.getElementById( 'rts-tip-form' );
	const statusBox  = document.getElementById( 'rts-form-status' );
	const submitBtn  = document.getElementById( 'rts-submit' );
	const btnText    = submitBtn && submitBtn.querySelector( '.rts-btn-text' );
	const btnSpinner = submitBtn && submitBtn.querySelector( '.rts-btn-spinner' );

	if ( ! form || ! statusBox || ! submitBtn ) {
		return; // Guard: bail if our elements are not on the page.
	}

	/** Field IDs that can carry inline error spans. */
	const FIELD_IDS = [ 'rts-name', 'rts-email', 'rts-restaurant', 'rts-message' ];

	/* ── Helpers ──────────────────────────────────────────────────────── */

	/**
	 * Clear all inline field-level error messages.
	 */
	function clearFieldErrors() {
		FIELD_IDS.forEach( function ( id ) {
			const input = document.getElementById( id );
			const error = document.getElementById( id + '-error' );
			if ( input ) {
				input.removeAttribute( 'aria-invalid' );
				input.classList.remove( 'rts-input--error' );
			}
			if ( error ) {
				error.textContent = '';
			}
		} );
	}

	/**
	 * Show field-level errors returned from the server.
	 *
	 * The server sends { fields: { rts_name: "msg", … } }.
	 * Field keys use underscores; element IDs use hyphens — we convert.
	 *
	 * @param {Object} fields  – map of field key → error message string
	 */
	function showFieldErrors( fields ) {
		Object.keys( fields ).forEach( function ( key ) {
			// 'rts_name' → 'rts-name'
			const id    = key.replace( /_/g, '-' );
			const input = document.getElementById( id );
			const error = document.getElementById( id + '-error' );

			if ( input ) {
				input.setAttribute( 'aria-invalid', 'true' );
				input.classList.add( 'rts-input--error' );
			}
			if ( error ) {
				error.textContent = fields[ key ];
			}
		} );

		// Move focus to the first invalid field for keyboard users.
		const firstInvalid = form.querySelector( '[aria-invalid="true"]' );
		if ( firstInvalid ) {
			firstInvalid.focus();
		}
	}

	/**
	 * Update the aria-live status region.
	 *
	 * @param {string} message   – text to announce
	 * @param {'success'|'error'} type
	 */
	function setStatus( message, type ) {
		statusBox.textContent  = ''; // Clear first so repeat messages re-trigger AT.
		statusBox.className    = 'rts-form-status rts-form-status--' + type;
		// Tiny timeout ensures screen readers re-read even identical messages.
		setTimeout( function () {
			statusBox.textContent = message;
		}, 50 );
	}

	/**
	 * Toggle the loading state of the submit button.
	 *
	 * @param {boolean} loading
	 */
	function setLoading( loading ) {
		submitBtn.disabled = loading;

		if ( loading ) {
			btnText.textContent = rtsData.i18n.sending;
			btnSpinner.classList.add( 'rts-btn-spinner--active' );
			submitBtn.setAttribute( 'aria-busy', 'true' );
		} else {
			btnText.textContent = rtsData.i18n.send;
			btnSpinner.classList.remove( 'rts-btn-spinner--active' );
			submitBtn.removeAttribute( 'aria-busy' );
		}
	}

	/* ── Submit handler ───────────────────────────────────────────────── */

	form.addEventListener( 'submit', async function ( event ) {
		event.preventDefault();

		clearFieldErrors();
		statusBox.textContent = '';
		statusBox.className   = 'rts-form-status';
		setLoading( true );

		/* Build FormData – automatically includes all named form fields,
		   including the hidden rts_nonce input rendered by PHP.             */
		const body = new FormData( form );

		/*
		 * We also append the nonce from rtsData as a belt-and-suspenders
		 * measure; the server accepts either.  The authoritative nonce is
		 * the one baked into the hidden input by PHP.
		 */
		body.set( 'action',    rtsData.action );
		body.set( 'rts_nonce', rtsData.nonce );

		try {
			const response = await fetch( rtsData.ajaxUrl, {
				method:      'POST',
				credentials: 'same-origin',  // Send cookies so wp_verify_nonce works.
				body:        body,
			} );

			/* fetch() only rejects on network failure, not on HTTP error
			   codes, so we parse JSON regardless of status.               */
			let data;
			try {
				data = await response.json();
			} catch {
				throw new Error( rtsData.i18n.networkError );
			}

			if ( data.success ) {
				// ── Success path ──────────────────────────────────────── //
				setStatus( data.data.message, 'success' );
				form.reset();
				// Return focus to the status box so AT users hear the message.
				statusBox.setAttribute( 'tabindex', '-1' );
				statusBox.focus();

			} else {
				// ── Error path ────────────────────────────────────────── //
				if ( data.data && data.data.fields ) {
					// Field-level validation errors.
					showFieldErrors( data.data.fields );
					setStatus(
						Object.values( data.data.fields ).join( ' ' ),
						'error'
					);
				} else {
					// Generic error (nonce failure, server error, etc.).
					const msg = ( data.data && data.data.message )
						? data.data.message
						: rtsData.i18n.networkError;
					setStatus( msg, 'error' );
				}
			}

		} catch ( err ) {
			setStatus( err.message || rtsData.i18n.networkError, 'error' );
		} finally {
			setLoading( false );
		}
	} );

} )();