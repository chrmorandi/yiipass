// Copy to clipboard functionality
var client = new ZeroClipboard(document.getElementsByClassName('copy_button'));

client.on( "ready", function( readyEvent ) {

    client.on( 'copy', function(event) {
        /**
         * Reset all button texts to their initial state. So the
         * user can see, which button was clicked and which not.
         */
        var all_password_buttons = document.getElementsByClassName('copy_password');

        for (var i=0, max=all_password_buttons.length; i < max; i++) {
            all_password_buttons[i].innerHTML = 'Copy Password';
        }

        var all_username_buttons = document.getElementsByClassName('copy_username');

        for (var i=0, max=all_username_buttons.length; i < max; i++) {
            all_username_buttons[i].innerHTML = 'Copy Username';
        }
    } );

    client.on( "aftercopy", function( event ) {
        // `this` === `client`
        // `event.target` === the element that was clicked
        // hide clicked element
        //event.target.style.display = "none";
        event.target.innerHTML = 'Copied';
        //console.log(event.target.innerHTML);
    } );
} );
