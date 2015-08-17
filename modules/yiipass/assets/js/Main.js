menu_click_dispatcher = new MenuClickDispatcher();
menu_click_dispatcher.dispatch();

// Copy to clipboard functionality
var client = new ZeroClipboard(document.getElementsByClassName('copy_button'));

client.on( "ready", function( readyEvent ) {
    client.on( "aftercopy", function( event ) {
        // `this` === `client`
        // `event.target` === the element that was clicked
        event.target.style.display = "none";
    } );
} );