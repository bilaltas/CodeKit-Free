jQuery(document).ready(function($){

    // **********************************************************************//
    // ! FOR PLAYING SOUND AFTER SAVE
    // **********************************************************************//

	 	window.cc_audioElement = document.createElement('audio');
        //cc_audioElement.setAttribute('src', '../wp-content/plugins/custom-codes/admin/developer/includes/playsound/Glass.mp3');

        if ( $('.updated.custom-codescustomcssjs').length ) cc_audioElement.play();;

        //cc_audioElement.load()

        /*cc_audioElement.addEventListener("load", function() {
            cc_audioElement.play();
        }, true);

        /*$('.play').click(function() {
            cc_audioElement.play();
        });

        $('.pause').click(function() {
            cc_audioElement.pause();
        });*/





}); // document ready