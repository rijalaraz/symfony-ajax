/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import $ from 'jquery';

/*
const formVideo = document.querySelector('form');

formVideo.addEventListener('submit', function(e) {
    e.preventDefault();

    fetch(this.action, {
        body: new FormData(e.target),
        method: 'POST'
    })
    .then(response => response.json())
    .then(json => {
        console.log(json);
    });
})
*/



$(document).ready(function(){


    $('form[name="video"]').on('submit',function(e){

        e.preventDefault();

        const data = new FormData(e.target);

        $.ajax({
            url:"/",
            data: data,
            type:"POST",
            contentType:false,
            processData:false,
            cache:false,
            dataType:"json", // Change this according to your response from the server.
            error:function(err){
                console.error(err);
            },
            success:function(response){
                // Pour effacer les doublons de messages d'erreur
                $('.invalid-feedback').each(function(index, el) {
                    $(el).remove();
                });
                $('.is-invalid').each(function(index, el) {
                    $(el).removeClass('is-invalid');
                });

                // Pour afficher les messages d'erreur
                switch (response.code) {
                    case 'VIDEO_ADDED_SUCCESSFULLY':
                        $('#videos_list').append(response.html);
                        break;

                    case 'VIDEO_INVALID_FORM':
                        for (const key in response.errors) {
                            if (Object.prototype.hasOwnProperty.call(response.errors, key)) {
                                const errorMessage = response.errors[key];

                                $(`#video_${key}`).addClass('is-invalid');

                                let newDiv = $('<div>');
                                newDiv.addClass('invalid-feedback d-block');
                                newDiv.text(errorMessage);

                                $(`#video_${key}`).after(newDiv);
                            }
                        }
                        break;

                    default:
                        break;
                }
            },
            complete:function(){
                console.log("Request finished.");
            }
        });

    });

});