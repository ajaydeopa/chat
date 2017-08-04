@extends('master')

@section('content')
    <p id="power">0</p>
    <input type="text" id="ip">
    <button onclick="call()">Click</button>
@stop

@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js" type="text/javascript"></script>
    <script>
        //var socket = io('http://localhost:3000');
        function call()
        {
            var socket = io('http://127.0.0.1:3000');
            ip = $('#ip').val();
            socket.on("test-channel:"+ip, function(message){
                // increase the power everytime we load test route
                $('#power').text(parseInt($('#power').text()) + parseInt(message.data.power));
            });
        }
    </script>
@stop