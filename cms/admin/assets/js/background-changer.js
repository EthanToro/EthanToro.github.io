$(document).ready(function() {
    var body = $('body'),
        bgNum = Math.floor((Math.random()*28) + 1);

    body.css(
    	{
    	'background': "url('assets/images/backgrounds/" + bgNum + ".jpg') fixed center center no-repeat",
    	'background-size': "cover"
    });
});