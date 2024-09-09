// Contains logic related to elementor.
( function( $, undefined ) {
	"use strict";

	window.VAMTAM_FRONT.elementor = window.VAMTAM_FRONT.elementor || {};

	var VAMTAM_ELEMENTOR = {
		domLoaded: function () {
			this.VamtamMainNavHandler.init();
			this.VamtamActionLinksHandler.init();
			this.VamtamPopupsHandler.init();

			if ( elementorFrontend.config.experimentalFeatures.e_optimized_css_loading ) {
				this.VamtamImprovedCSSLoadingHandler.init();
			}
		},
		pageLoaded: function () {
			// this.VamtamPopupToggleHandler.init();
		},
		// Hanldes issues related to the main na menu.
		VamtamMainNavHandler: {
			init: function() {
				this.fixMenuDrodownScrolling();
			},
			fixMenuDrodownScrolling: function () {
				var $mainMenuDropdown = $( '.elementor-location-header .elementor-nav-menu--dropdown-tablet .elementor-nav-menu--dropdown.elementor-nav-menu__container' ).first();
				var menuToggle        = $mainMenuDropdown.siblings( '.elementor-menu-toggle' )[ 0 ];

				if ( ! $mainMenuDropdown.length || ! menuToggle.length ) {
					return;
				}

				var onMenuToggleClick = function () {
					if ( $( menuToggle ).hasClass( 'elementor-active' ) ) {
						// For safari we substract an additional 100px to account for the bottom action-bar (different size per iOS version). Uggh..
						var height = $( 'html' ).hasClass( 'ios-safari' ) ? $mainMenuDropdown[ 0 ].getBoundingClientRect().top + 100 : $mainMenuDropdown[ 0 ].getBoundingClientRect().top;
						$mainMenuDropdown.css( 'max-height', 'calc(100vh - ' + height + 'px)' );
						menuToggle.removeEventListener( 'click', onMenuToggleClick );
					}
				}
				menuToggle.addEventListener( 'click', onMenuToggleClick );
			},
		},
		// Handles funcionality regarding action-linked popups.
		VamtamActionLinksHandler: {
			init: function() {
				this.popupActionLinks();
			},
			popupActionLinks: function() {
				var _self               = this,
					prevIsBelowMax      = window.VAMTAM.isBelowMaxDeviceWidth(),
					alignedPopups       = [];

				var handleAlignWithParent = function( popupId, popupParent, clearPrevPos ) {
					var popupWrapper   = $( '#elementor-popup-modal-' + popupId ),
						popup          = $( popupWrapper ).find( '.dialog-widget-content' ),
						adminBarHeight = window.VAMTAM.adminBarHeight;

					if ( ! popup.length || ! popupParent ) {
						return;
					}

					var parentPos = popupParent.getBoundingClientRect();

					if ( clearPrevPos ) {
						$( popup ).css( {
							top: '',
							left: '',
						} );
					} else {
						$( popup ).css( {
							top: parentPos.bottom - adminBarHeight,
							left: parentPos.left,
						} );
						// After the popup is hidden we unset top/left.
						( function () { // IIFE for closure so popup, popupWrapper are available.
							// Visibity check.
							var visibilityCheck = setInterval( function() {
								if ( $( popupWrapper ).css( 'display' ) === 'none' ) {
									$( popup ).css( {
										top: '',
										left: '',
									} );
									clearInterval( visibilityCheck );
								}
							}, 500 );
						})();
					}
				};

				var repositionAlignedPopups = function ( clear ) {
					alignedPopups.forEach( function( popup ) {
						if ( clear ) {
							handleAlignWithParent( popup.id, popup.parent, true );
						} else {
							handleAlignWithParent( popup.id, popup.parent );
						}
					} );
				};

				var popupResizeHandler = function () {
					var isBelowMax = window.VAMTAM.isBelowMaxDeviceWidth();
					if ( prevIsBelowMax !== isBelowMax) {
						// We changed breakpoint (max/below-max).
						if ( isBelowMax ) {
							// Clear popup vals set from desktop.
							repositionAlignedPopups( true );
						} else {
							repositionAlignedPopups();
						}
						prevIsBelowMax = isBelowMax;
					} else if ( ! isBelowMax ) {
						repositionAlignedPopups();
					}
				};

				var popupScrollHandler = function () {
					requestAnimationFrame( function() {
						repositionAlignedPopups();
					} );
				};

				var storePopup = function ( popupId, popupParent ) {
					// If exists, update parent, otherwise store.
					// A popup can have multiple parents. We only care for the last parent that triggers it each time.
					var done;

					alignedPopups.forEach( function( popup ) {
						if ( popup.id === popupId ) {
							popup.parent = popupParent;
							done = true;
						}
					} );

					if ( ! done ) {
						alignedPopups.push( {
							id: popupId,
							parent: popupParent,
						} );
					}
				}

				function checkForStickyParent( popupParent ) {
					const parentEl = $( popupParent ).parents('.elementor-element.elementor-widget')[0];
					if ( ! parentEl ) {
						return;
					}

					let parentElSettings = $( parentEl ).attr( 'data-settings' );
					if ( ! parentElSettings ) {
						return;
					}

					try {
						parentElSettings = JSON.parse( parentElSettings );
					} catch (error) {
						return;
					}

					const hasStickyParent = parentElSettings.sticky;
					if ( hasStickyParent ) {
						window.removeEventListener( 'scroll', popupScrollHandler );
						window.addEventListener( 'scroll', popupScrollHandler, { passive: true } );
					}
				}

				var checkAlignWithParent = function( e ) {
					var actionLink = $( e.currentTarget ).attr( 'href' );
					if ( ! actionLink ) {
						return;
					}

					var settings = _self.utils.getSettingsFromActionLink( actionLink );
					if ( settings && settings.align_with_parent ) {

						storePopup( settings.id, e.currentTarget );

						if ( window.VAMTAM.isMaxDeviceWidth() ) {
							// Desktop
							handleAlignWithParent( settings.id, e.currentTarget );
						}

						window.removeEventListener( 'resize', popupResizeHandler );
						window.addEventListener( 'resize', popupResizeHandler, false );

						// This is for following the parent's scroll (for sticky parents).
						checkForStickyParent( e.currentTarget );
					}
				};

				elementorFrontend.elements.$document.on( 'click', 'a[href^="#elementor-action"]', checkAlignWithParent );
			},
			utils: {
				getSettingsFromActionLink: function( url ) {
					url = decodeURIComponent( url );

					if ( ! url ) {
						return;
					}

					var settingsMatch = url.match( /settings=(.+)/ );

					var settings = {};

					if ( settingsMatch ) {
						settings = JSON.parse( atob( settingsMatch[ 1 ] ) );
					}

					return settings;
				},
				getActionFromActionLink: function( url ) {
					url = decodeURIComponent( url );

					if ( ! url ) {
						return;
					}

					var actionMatch = url.match( /action=(.+?)&/ );

					if ( ! actionMatch ) {
						return;
					}

					var action = actionMatch[ 1 ];

					return action;
				}
			}
		},
		VamtamWidgetsHandler: {
			// Checks if there is an active mod (master toggle) for a widget.
			isWidgetModActive: ( widgetName ) => {
				if ( ! widgetName ) {
					return false;
				}

				const siteSettings = elementorFrontend.getKitSettings(),
					themePrefix    = 'vamtam_theme_',
					widgetModsList = window.VAMTAM_FRONT.widget_mods_list;

				// Do we have a master toggle for this widget?
				if ( ! widgetModsList[ widgetName ] ) {
					return false;
				}

				if ( siteSettings[ `${themePrefix}enable_all_widget_mods` ] === 'yes' ) {
					// All theme widget mods enabled by user pref.
					return true;
				} else if ( siteSettings[ `${themePrefix}disable_all_widget_mods` ] === 'yes' ) {
						// All theme widget mods disabled by user pref.
						return false;
				} else {
					// User pref for current widget.
					return siteSettings[ `${themePrefix}${widgetName}` ] === 'yes';
				}
			},
		},
		// Handles Popup-related stuff.
		VamtamPopupsHandler: {
			init: function () {
				this.disablePopupAutoFocus();
				this.enablePageTransitionsForPopupLinks();
			},
			disablePopupAutoFocus: function () {
				// Update to elementor pro 3.11.2 results in accidentally focused links within popups.
				elementorFrontend.elements.$window.on( 'elementor/popup/show elementor/popup/hide', ( event ) => {
					setTimeout( () => { // For popups with entry animations.
						if ( document.activeElement.nodeName !== 'input' ) {
							document.activeElement.blur();
						}
					}, 100 );
				} );
			},
			enablePageTransitionsForPopupLinks() {
				if ( ! elementorFrontend.config.experimentalFeatures['page-transitions'] ) {
					return;
				}

				let $pageTransitionEl = jQuery('e-page-transition');

				if ( $pageTransitionEl.length ) {
					$pageTransitionEl = $pageTransitionEl[0];
				} else {
					return;
				}

				const triggers = $pageTransitionEl.getAttribute('triggers');

				if ( triggers ) {
					return; // User has selected custom triggers for the page transition.
				}

     			const selector = '.elementor-popup-modal a:not( [data-elementor-open-lightbox="yes"] )',
					onLinkClickHandler = $pageTransitionEl.onLinkClick;

				if ( ! onLinkClickHandler ) {
					return;
				}

				// Re-route click event to PageTransitions onClick handler.
				jQuery( document ).on( 'click', selector, onLinkClickHandler.bind( $pageTransitionEl ) );
			}
		},
		VamtamImprovedCSSLoadingHandler: {
			init: () => {
				const widgetData = { ...VAMTAM_FRONT.widgets_assets_data }; // Added from server.


				if ( ! widgetData ) {
					return; // Nothing to do.
				} else {
					// So the paths are not displayed.
					$( '#vamtam-all-js-after' ).remove(); // Could be problematic if we need to add more inline stuff (wp_add_inline_script) to "vamtam-all" script.
					delete( VAMTAM_FRONT.widgets_assets_data );
				}

				let out = '';
				Object.keys(widgetData).forEach( widget => {
					const content = widgetData[ widget ][ 'content' ],
						widgetSelector = `.elementor-widget-${widget} > .elementor-widget-container`;

					if ( content ) {
						if ( $( `${widgetSelector} > style, ${widgetSelector} > link`).length ) {
							return; // Inline style has been added for widget.
						}

						// Add to fallback.
						out = out + '\r\n' + content;
					}
				});

				// Add fallback inline CSS to DOM.
				if ( out ) {
					$( 'body' ).append( `<div id="vamtam-css-opt-fallback">${out}</div>` );
				}
			}
		},
	}

	window.VAMTAM_FRONT.elementor.urlActions = VAMTAM_ELEMENTOR.VamtamActionLinksHandler.utils;
	window.VAMTAM_FRONT.elementor.widgets = {
		isWidgetModActive: VAMTAM_ELEMENTOR.VamtamWidgetsHandler.isWidgetModActive
	}

	$( document ).ready( function() {
		VAMTAM_ELEMENTOR.domLoaded();
	} );

	$( window ).on( 'load', function () {
		VAMTAM_ELEMENTOR.pageLoaded();
	} );

	// JS Handler applied to all elements.
	class VamtamElementBase extends elementorModules.frontend.handlers.Base {

		onInit(...args) {
			super.onInit( ...args );
			this.checkAddBaseThemeStylesClass();
		}

		checkAddBaseThemeStylesClass() {
			const isEditor = $( 'body' ).hasClass( 'elementor-editor-active' );
			if ( ! isEditor ) {
				return;
			} else if ( this.isWidgetModActive() ) {
				this.$element.addClass('vamtam-has-theme-widget-styles');
			}
		}

		// Checks if there is an active mod (master toggle) for a widget.
		isWidgetModActive() {
			let widgetName = this.getElementType();
			if ( widgetName === 'widget' ) {
				widgetName = this.getWidgetType();
			}
			return VAMTAM_ELEMENTOR.VamtamWidgetsHandler.isWidgetModActive( widgetName );
		}

	}

	jQuery( window ).on( 'elementor/frontend/init', () => {
		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( VamtamElementBase, {
				$element,
			} );
		};

		elementorFrontend.hooks.addAction( 'frontend/element_ready/global', addHandler );
	});
})( jQuery );
