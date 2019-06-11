////////////////////////////////////////
// File fureinouen.js.
jQuery(function() {

	// Google Maps
	if( jQuery( '#map-canvas').length ){
		google.maps.event.addDomListener(window, 'load',  ita_google_maps);
	}

	jQuery( window ).load(function() {

		// home grid
		jQuery( "#blog ul li" ).tile( 3 );
		jQuery( ".tile .hentry" ).tile( 3 );

		// Browser supports matchMedia
		if ( window.matchMedia ) {
			// MediaQueryList
			var mq = window.matchMedia( "( min-width: 930px )" );

			// MediaQueryListListener
			var fureinouenHeightCheck = function ( mq ) {
				if ( mq.matches ) {
					// tile for home
					jQuery( "#blog ul li" ).tile( 3 );
					jQuery( ".tile .hentry" ).tile( 3 );
				}
				else {
					// cansel
					jQuery( '#blog ul li' ).css( 'height', 'auto' );
					jQuery( ".tile .hentry" ).css( 'height', 'auto' );
				}
			};

			// Add listener HeightChec
			mq.addListener( fureinouenHeightCheck );
			fureinouenHeightCheck( mq );
		}
		else {
			// Browser doesn't support matchMedia
			jQuery( "#blog ul li" ).tile( 3 );
			jQuery( ".tile .hentry" ).tile( 3 );
		}

		// Header Slider
		jQuery( '.slider[data-interval]' ).fureinouen_Slider();

	});

	// Navigation for mobile
	jQuery( "#small-menu" ).click( function(){
		jQuery( "#menu-primary-items" ).slideToggle();
		jQuery( this ).toggleClass( "current" );
	});

	// My mapp scroll enable
	var map = jQuery('#gmap iframe');
	map.css('pointer-events', 'none');
	jQuery('#gmap').click(function() {
		map.css('pointer-events', 'auto');
	});
	map.mouseout(function() {
		map.css('pointer-events', 'none');
	})

// Windows Scroll
	var totop = jQuery( '#back-top' );
	totop.hide();
	jQuery( window ).scroll(function () {
		// back to pagetop
		var scrollTop = parseInt( jQuery( this ).scrollTop() );
		if ( scrollTop > 800 ) totop.fadeIn(); else totop.fadeOut();

		// mini header with scroll
		var header_clip = jQuery( '#header' ).css( 'clip' );
		if( -1 == header_clip.indexOf( 'rect' ) ) {
			if ( scrollTop > 200 ) {
				jQuery('.wrapper #header').addClass('mini');
			}
			else {
				jQuery('.wrapper #header').removeClass('mini');
			}
		}
	});

	// back to pagetop
	totop.click( function () {
		jQuery( 'body, html' ).animate( { scrollTop: 0 }, 500 ); return false;
	});
});

////////////////////////////////////////
// Header Slider
jQuery.fn.fureinouen_Slider = function(){
	return this.each(function(i, elem) {
		// change slide
		var fureinouen_interval = jQuery( '.slider' ).attr( 'data-interval' );
		setInterval( function(){

			index = jQuery( '.slideitem.active' ).index( '.slideitem' );
			index++;
			if( index >= jQuery( '.slideitem' ).length ){
				index = 0;
			}

			// fade in
			jQuery( '.slideitem:eq(' + index + ')' ).fadeIn( 1000, function (){
				// fade out
				jQuery( '.slideitem.active' ).fadeOut( 1000 );
				jQuery( '.slideitem.start').removeClass( 'start' );
				jQuery( '.slideitem.active').removeClass( 'active' );
				jQuery( '.slideitem:eq(' + index + ')').addClass( 'active' );
			} );
		}, fureinouen_interval );
	});
};


////////////////////////////////////////
// Google Maps for access
function ita_google_maps() {
		var zoom = 15;

		var latlng_1 = new google.maps.LatLng( 35.777789, 139.653109 );
		var latlng_3 = new google.maps.LatLng( 35.780096, 139.638470 );

		var mapOptions = {
			zoom: zoom,
			center: latlng_3,
			scrollwheel: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			scaleControl: true,
			scaleControlOptions: {
				position: google.maps.ControlPosition.BOTTOM_LEFT
			},
			mapTypeControlOptions: {
				mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'm_map']
			}
		}
		var map = new google.maps.Map( document.getElementById('map-canvas'), mapOptions );
	
		var map_icon_3 = jQuery( '#map_icon_path' ).val() + '/icon_stand_3.png' ;
		var marker_3 = new google.maps.Marker({
			position: latlng_3,
			map: map,
			icon: map_icon_3
		});
	
		new google.maps.InfoWindow({
			content: '3号農産物直売スタンド<br><a href="https://goo.gl/maps/MjZRW23ovpG2" style="display :block;padding-top: 5px; font-size: 0.9em;" target="_blank">地図を拡大表示</a>'
		}).open( marker_3.getMap(), marker_3 );

		var map_icon_1 = jQuery( '#map_icon_path' ).val() + '/icon_stand_1.png' ;
		var marker_1 = new google.maps.Marker({
			position: latlng_1,
			map: map,
			icon: map_icon_1,
			title: '畑 3'
		});
	
		new google.maps.InfoWindow({
			content: '1号農産物直売スタンド<br><a href="https://goo.gl/maps/QHkG8zNsXPo" style="display :block;padding-top: 5px; font-size: 0.9em;" target="_blank">地図を拡大表示</a>'
		}).open( marker_1.getMap(), marker_1 );
	
	}
	