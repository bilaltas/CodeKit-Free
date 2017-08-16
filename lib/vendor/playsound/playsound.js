jQuery(document).ready(function($){

    // **********************************************************************//
    // ! FOR PLAYING SOUND AFTER SAVE
    // **********************************************************************//

	 	window.cstm_cds_audioElement = document.createElement('audio');
        //cstm_cds_audioElement.setAttribute('src', '../wp-content/plugins/custom-codes/admin/developer/vendor/playsound/Glass.mp3');

        if ( $('.updated.custom-codescustomcssjs').length ) cstm_cds_audioElement.play();;

        //cstm_cds_audioElement.load()

        /*cstm_cds_audioElement.addEventListener("load", function() {
            cstm_cds_audioElement.play();
        }, true);

        /*$('.play').click(function() {
            cstm_cds_audioElement.play();
        });

        $('.pause').click(function() {
            cstm_cds_audioElement.pause();
        });*/





}); // document ready