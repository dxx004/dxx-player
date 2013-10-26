<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title>DXX Facebook Music Player</title>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="js/swfobject.js"></script> 
	<script type="text/javascript" src="js/func.js?v=17"></script>
	<link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico" />
	
	<!-- styles needed by jScrollPane - include in your own sites -->
	<link type="text/css" href="css/jquery.jscrollpane.css" rel="stylesheet" media="all" /> 
	<script type="text/javascript" src="js/jquery.mousewheel.js"></script>
	<!-- the jScrollPane script -->
	<script type="text/javascript" src="js/jquery.jscrollpane.min.js"></script>
	<link href='//fonts.googleapis.com/css?family=Strait' rel='stylesheet' type='text/css'>
	<!-- scripts specific to this demo site --> 
	<style type="text/css" media="screen, tv, projection">
		@import "css/style.css?v=15";
	</style>
	<meta property="og:title" content="DXX Facebook Music Player" />
	<meta property="og:description" content="Watch your favorite videos from your or your friends wall... and it's FREE! " />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="https://www.s-mail.cz/dxx_player/" />
	<meta property="og:image" content="https://www.s-mail.cz/dxx_player/images/Button-Play.png" />
	<meta property="fb:admins" content="1483947465" />
</head>
<body>
<div id="like_overlay" style="display: none;">
</div>
<div id="top_bar">
	<div class="wrap">
		<!-- iframe src="" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:21px;" allowTransparency="true" id="fb_like"></iframe -->
		<img src="" id="fb_image" alt="" style="display: none;"/>
		<div id="fb_name">
			<a id="fb_name_a"></a>
			<? if (isset($_REQUEST['fb_user_id'])) {
			echo '
			<span id="fb_change_to_default" style="display: none;">(use player as <a href="#"></a>)</span>';
			}
			?>
		</div>
	</div>
</div>
<div style="text-align: center; margin-top: 20px; display: none;" id="login_wrap"><button id="login">Login with Facebook</button></div>
<div class="wrap">
	<div class="loading" style="display: none;">&nbsp;</div>
	<h1>DXX Facebook Music Player <span id="copyright">&copy; <?php echo date('Y')?> - by <a href="https://www.facebook.com/kilo.hoven" target="_blank">Kilo</a></span></h1>
	<div class="fb-like" id="self_like" data-href="https://www.facebook.com/dxxmusicplayer" data-send="true" data-layout="button_count" data-width="250" data-show-faces="false" data-action="recommend"></div>
	<div id="bulk" style="display:none;">&nbsp;</div>
	<hr class="clear"/>
	<input type="text" id="video_search" placeholder="Video search" class="rel_logged"/>
	<div id="videos" style="display: none;"></div>
	<div id="loading_videos" style="display: none;">LOADING...<br/>please wait</div>
	<div id="video_player" class="rel_logged">
		<div id="ytapiplayer">
			<div id="paypal_donate">
				<h3>If you wanna buy me a beer...</h3>
				<br/>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHPwYJKoZIhvcNAQcEoIIHMDCCBywCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYCFZOQ52NJq1IR4EUZ+PLVTSXBezXgft7OhiufSe2CgB/pG11hDlMtiJkfrnwFjDnWO+1i8bJiLwyPiHjigrDsiC4iWvzgFsTDJTUW9OfjjpdBTiixjCVB9YbY1ZYVLZzNfALcWBSSV3uHkZeQg3ZJe7b1lJBOB01N/KuLoqWeSIzELMAkGBSsOAwIaBQAwgbwGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIc8dkBTDIJ1OAgZhmMErWMiT2DrPhYgi4ke7cpLarQNjjpa7Y7cK9OnqLJ7KFHFG6LlDZzQT77vEWJpFn+5C5KfCbWLPd7GuxQEoVrS3Yw5iNOlOrY+BxDx9Z+WNacg2QoN4iQDGA5VgjjdiBoBdcGVQDdCN5s7seq8lRY2gg367es6Xa8vOJ/ZYhRSL7plKZDGVz5aW1cKDpgfTBoIUZBxqRu6CCA4cwggODMIIC7KADAgECAgEAMA0GCSqGSIb3DQEBBQUAMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbTAeFw0wNDAyMTMxMDEzMTVaFw0zNTAyMTMxMDEzMTVaMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAwUdO3fxEzEtcnI7ZKZL412XvZPugoni7i7D7prCe0AtaHTc97CYgm7NsAtJyxNLixmhLV8pyIEaiHXWAh8fPKW+R017+EmXrr9EaquPmsVvTywAAE1PMNOKqo2kl4Gxiz9zZqIajOm1fZGWcGS0f5JQ2kBqNbvbg2/Za+GJ/qwUCAwEAAaOB7jCB6zAdBgNVHQ4EFgQUlp98u8ZvF71ZP1LXChvsENZklGswgbsGA1UdIwSBszCBsIAUlp98u8ZvF71ZP1LXChvsENZklGuhgZSkgZEwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tggEAMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADgYEAgV86VpqAWuXvX6Oro4qJ1tYVIT5DgWpE692Ag422H7yRIr/9j/iKG4Thia/Oflx4TdL+IFJBAyPK9v6zZNZtBgPBynXb048hsP16l2vi0k5Q2JKiPDsEfBhGI+HnxLXEaUWAcVfCsQFvd2A1sxRr67ip5y2wwBelUecP3AjJ+YcxggGaMIIBlgIBATCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwCQYFKw4DAhoFAKBdMBgGCSqGSIb3DQEJAzELBgkqhkiG9w0BBwEwHAYJKoZIhvcNAQkFMQ8XDTEzMDczMDA5MDE1N1owIwYJKoZIhvcNAQkEMRYEFAn4ch1GBKNfVJudS+aHmrmPJeJdMA0GCSqGSIb3DQEBAQUABIGAG4xV6GfHLB6zSLMKTtj7A+b5Dtk1BXYKW5I4j5S20vPVpqsenVKx8WJoulFiFMR+tZk/TdZLCct0+5GVlOphS+4ad7vsVU9cTMXbeZYHvYr7eZKZEL958jGTdxN3HTY6rgbFJGolnRMlZeLe5iKzK3MrTmd6yYbE5s+PB26+4Vw=-----END PKCS7-----
">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
				<br/>
				<h3>Thx :)</h3>
			</div>
			<span style="font-size: 49px;">Select video to play</span>
		</div>
		<div class="control_buttons" style="display: none;">
			<button class="sprite_button prev" title="previous">&nbsp;</button>
			<button class="sprite_button next" title="next">&nbsp;</button>
			<button class="sprite_button shuffle" title="shuffle">&nbsp;</button>
			<input type="checkbox" id="do_random"/><label for="do_random">&nbsp;Play Random</label>
		</div>
	</div>
	<hr class="clear"/>
	<div id="friends_list" class="rel_logged">
		<div id="fl_search">
			<input type="text" id="fl_search_input" value="" placeholder="Friend Search"/>
			<button id="invite_friends">Invite Friends</button>
		</div>
		<div id="friends_list2"></div>
	</div>
	
</div>
<div id="smp-root"></div>
<div id="fb-root"></div>
<script>
	var FB_USER_ID = <?=(isset($_REQUEST['fb_user_id'])?json_encode($_REQUEST['fb_user_id']):0)?>;
	var FB_USERNAME = "";
	var LIMIT = 10000;
	var RANDOM = false;
	var LAST_TIMESTAMP = -1;
	var SELF_ID = 0;
	var SELF_NAME = "";
	var FRIENDS = [];
	var FRIENDS_NAMES = [];
	var ACCESS_TOKEN = "";

    window.smpAsyncInit = function() {
        StarSMP.init({
            fbAppId:     '311789798881087',
            fbInstance:  FB,
            fbAutoOptin: true,
            loyaltyId:   '127517',
            apiServer:   '//api.starsmp.com/api/',
            clientId:    'music_player',
            channelUrl:  'https://www.s-mail.cz/dxx_player/channel.html.php'
        }, function(data) {
            StarSMP.track("view", document.location.href);
        });
    };
	
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '311789798881087', // App ID
	  channelUrl : 'https://www.s-mail.cz/dxx_player/channel.html.php', // Channel File for x-domain communication
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });
	FB.Canvas.setAutoGrow();
	
	// load SMP when Facebook is initialized
        var e = document.createElement("script"); e.async = true;
        e.src = document.location.protocol + "//api.starsmp.com/connect/all.js";
        document.getElementById("smp-root").appendChild(e);	

    // Additional initialization code here
	FB.getLoginStatus(function(response) {
		if (response.status === 'connected') {
			// the user is logged in and has authenticated your
			// app, and response.authResponse supplies
			// the user's ID, a valid access token, a signed
			// request, and the time the access token 
			// and signed request each expire
			// var uid = response.authResponse.userID;
			// var accessToken = response.authResponse.accessToken;
			$('#login_wrap').hide();
			$('.rel_logged').show();
			ACCESS_TOKEN = response.authResponse.accessToken;
			FB.api('/me', function (response) {
				SELF_ID = response.id;
				SELF_NAME = response.name;
				_gaq.push([
					'_setCustomVar',				
					1, 
					'Facebook ID', 
					SELF_ID, 
					1
				]);			
				
				if (!FB_USER_ID) {
					init(response);
				} else {
					FB.api('/'+FB_USER_ID, function (response) {
						init(response);
					});
				}
			});
			/* var user = (!FB_USER_ID?'me':FB_USER_ID);
			FB.api('/'+user, function (response) {
				init(response);
			}); */
			/*FB.api('/me/posts', function(response) {
				fillVideos(response);
			});*/
		} else if (response.status === 'not_authorized') {
			// the user is logged in to Facebook, 
			// but has not authenticated your app
			$('#login_wrap').show();
		} else {
			$('#login_wrap').show();
			// the user isn't logged in to Facebook.
		}
	});	
  };

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));
</script>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-313091-80']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
