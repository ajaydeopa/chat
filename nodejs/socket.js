var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
try
{
	var Redis = require('ioredis');
	var redis = new Redis();
	redis.subscribe('test-channel', function(err, count) {
	});
	redis.on('message', function(channel, message) {
	    message = JSON.parse(message);
	    console.log('Message Recieved: ' + message.data.data.group);
	    io.emit(channel + ':'+ message.data.data.group, message.data);
	});
	http.listen(3000, function(){
	    console.log('Listening on Port 3000');
	});
}
catch(e)
{
	alert(e);
}