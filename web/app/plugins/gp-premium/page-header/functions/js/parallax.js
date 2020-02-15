function generate_parallax_element( selector, context ) {
    context = context || document;
    var elements = context.querySelectorAll( selector );
    return Array.prototype.slice.call( elements );
}

window.addEventListener( "scroll", function() {
    var scrolledHeight= window.pageYOffset;
    generate_parallax_element( ".parallax-enabled" ).forEach( function( el, index, array ) {
        var limit = el.offsetTop + el.offsetHeight;
        if( scrolledHeight > el.offsetTop && scrolledHeight <= limit ) {
            el.style.backgroundPositionY = ( scrolledHeight - el.offsetTop ) / el.getAttribute( 'data-parallax-speed' ) + "px";
        } else {
            el.style.backgroundPositionY = "0";
        }
    });
});