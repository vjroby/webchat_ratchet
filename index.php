<?php
require __DIR__ . '/vendor/autoload.php';
$nickname = uniqid('Guest_');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Ratchet test</title>
        <link rel="stylesheet" href="css/main.css"/>
        <script type="text/javascript" src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
    </head>
    <body>
    <div>
        <h2>Testing Ratchet</h2>
        <div class="error">

        </div>

<!--        <form action="#">-->
            <label for="nickname">Nickname:</label>
            <input type="text" id="nickname" value="<?php echo $nickname; ?>"/><br>
            <button id="connect">Connect</button><br>
            <div  id="chatbox" style=""></div>
            <br>
            <label for="chatmessage">Type:</label>
            <input type="text" id="chatmessage" />
            <button id="send">Send</button>
<!--        </form>-->
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('input#chatmessage').on('keypress', function(e){
                var code = e.keyCode || e.which;
                if(code == 13) { //Enter keycode
                    $('#send').trigger('click');
                }
            });

            var conn = false;

            $('#connect').on('click', function(e){
                console.log('test')
                e.preventDefault();
                var nickname = $('#nickname');

                if (nickname.val().length != 0){
                    $('div.error').text('');
                    nickname.prop('readonly', true);

                    try{
                        conn = new WebSocket('ws://192.168.0.5:8080');
                    }
                    catch (exception){
                        show_error(exception);
                    }

                    conn.onopen = function(e) {
                        $('div.error').text('Connection established!');
                    };

                    conn.onmessage = function(e) {
                        var data = JSON.parse(e.data);
                        var message = '<div>'+data.nickname+': '+data.message+'</div>';
                        $('#chatbox').prepend(message);

                        //console.log(e.data);
                    };
                    conn.onclose = function(e){
                        $('#connect').slideDown('slow');
                        $('#nickname').prop('readonly', false);
                        show_error('Connection closed, server down!');
                    };

                    $(this).slideUp('slow');
                }else{
                    show_error('Input a nickname to connect');
                }
            });

            $('#send').on('click', function(e){
                e.preventDefault();
                if (conn != false){
                    var message = $('#chatmessage').val();
                    if( message.length != 0){
                        var nickname = $('input#nickname').val();
                        var dataToSend = {
                            nickname: nickname,
                            message: message
                        };
                        dataToSend = JSON.stringify(dataToSend);
                        $.when(conn.send(dataToSend))
                            .then(function(){
                                var message = $('#chatmessage').val();
                                var data = '<div class="mine">'+nickname+': '+message+'</div>';
                                $('#chatbox').prepend(data);
                                $('input#chatmessage').val('');
                            });
                    }else{
                        show_error('Empty message!!!');
                    }
                }else{
                    show_error('Not connected to chat!');
                }
            })
        });

        function show_error(message){
            $('div.error').text(message);
        }

    </script>
    </body>
</html>