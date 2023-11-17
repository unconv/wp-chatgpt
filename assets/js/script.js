(function( $ ) {
    const wpgpt_message_history = [];

    $(document).ready( function() {
        $(".wpgpt-toggle").click( function() {
            $(".wpgpt-chatbox").toggle();
        } );

        $(".wpgpt-send").click( wpgpt_send_message );
        $(".wpgpt-chat-input").keydown( function( e ) {
            if( e.which === 13 && ! e.shiftKey ) {
                wpgpt_send_message();
                e.preventDefault();
            }
        } );
    } );

    function wpgpt_send_message() {
        let message = $(".wpgpt-chat-input").val();
        wpgpt_add_message( message, "user" );
        $(".wpgpt-chat-input").val("");

        let payload = {
            "message": message,
            "message_history": wpgpt_message_history
        };

        $.ajax({
            url: '/wp-json/wpgpt/v1/send-message',
            method: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify( payload ),
            success: function( response ) {
                wpgpt_add_message( response.message, "assistant" );
                wpgpt_message_history.push( {
                    "role": "user",
                    "content": message,
                } );
                wpgpt_message_history.push( {
                    "role": "assistant",
                    "content": response.message.replace( "\n", "<br>" ),
                } );
            },
            error: function() {
                wpgpt_add_message( "Sorry, there was an error", "assistant" );
            }
          });
    }

    function wpgpt_add_message( message, role ) {
        let message_box = `
        <div class="wpgpt-chat-message ${role}">
            ${message}
        </div>`

        $(".wpgpt-chat-messages").append( message_box );

        $(".wpgpt-chat-messages").animate({
            scrollTop: $(".wpgpt-chat-messages")[0].scrollHeight
        });
    }
})(jQuery);
