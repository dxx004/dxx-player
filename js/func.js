var videos = [];
var def_videos = [];
var inc = 0;



function init(response) {
	FB_USER_ID = response.id;
	FB_USERNAME = response.name;
	document.title += ' - '+FB_USERNAME;
	
	$('#videos').show();
	$('#show_hide_friends').show();
	
	var search_ev = function (e) {
		if (e.keyCode == 13) {
			searchFriends($(this).val());
		}
	}

	getFriends( function () {
		if ( (SELF_ID==FB_USER_ID) || (inArray(FB_USER_ID, FRIENDS))) {
			loadVideos(fillVideos);
			writeUser();
			$('#fl_search_input')
				//.blur( function () { if ($(this).val() == '') $(this).val('Search'); })
				//.focus( function () { $(this).val(''); })
				.keyup( search_ev );
            checkLikeAndShow();
		} else {
			alert('You\'re not friend with this guy!');
		}
	});
}

function searchFriends(val) {
	var str = val.toLowerCase();
	var $eles = $('#friends_list2').find('a');
	$.each ( $eles, function (i, ele) {
		var name = $(this).attr('title').toLowerCase();
		if (name.indexOf(str) == -1) {
			$(this).hide();
		} else {
			$(this).show();
		}
	});
	panes[0].reinitialise();
}

function searchVideos(val) {
    var str = val.toLowerCase();
    var $eles = $("#videos").find('a');
    $("#videos p").show();
    //videos = def_videos;
    videos = def_videos.slice(0);
    redoLinks();

    var vvvv = [];
    $eles.each ( function () {
        var name = $(this).text().toLowerCase();
        var id = $(this).attr('id').split('_');
        id = id[1];
        if (name.indexOf(str) == -1) {
            $(this).parent('p').hide();
            $(this).attr('id', 'remvideo_'+id);
        } else {
            vvvv.push(videos[id]);
            $(this).parent('p').show();
        }
    });
    videos = vvvv.splice(0);
    redoLinks();
    panes[1].reinitialise();
    return false;
}

function showAllVideos() {
    $('#video_search').val('');
    //videos = def_videos;
    videos = def_videos.slice(0);
    $("#videos").find('a').each ( function () {
        $(this).parent('p').show();
    });
    redoLinks();
    panes[1].reinitialise();
}

var panes = new Array();
	$(document).ready ( function () {
		panes.push($('#friends_list2').jScrollPane().data('jsp'));
        $('.rel_logged').hide();
		$('#login').click( function (e) {
			FB.login(function(response) {
				if (response.authResponse) {
					$('#login_wrap').hide();
                    $('.rel_logged').show();
					ACCESS_TOKEN = response.authResponse.accessToken;
					FB.api('/me', function (response) {
						SELF_ID = response.id;
						
						if (!FB_USER_ID) {
							init(response);
						} else {
							FB.api('/'+FB_USER_ID, function (response) {
								init(response);
							});
						}
					});
				} else {
					// console.log('User cancelled login or did not fully authorize.');
				}
			}, {scope: 'read_stream,user_likes'});			
		});
		
		$('button.next').unbind('click').click ( function (e) {
			e.preventDefault();
			var off = RANDOM?Math.floor(Math.random()*videos.length):1;
			StarSMP.track("select.next", document.location.href);
			playPrevNext(off);
		});
		
		$('button.prev').unbind('click').click ( function (e) {
			e.preventDefault();
			var off = RANDOM?Math.floor(Math.random()*videos.length):-1;
			StarSMP.track("select.prev", document.location.href);
			playPrevNext(off);
			return false;
		});
		
		$('button.shuffle').unbind('click').click ( function (e) {
			e.preventDefault();
			var rand = Math.floor(Math.random()*videos.length);
			StarSMP.track("select.shuffle", document.location.href);
			playPrevNext(rand);
			return false;
		});
		
		$('#do_random').unbind('change').change( function (e) {
			RANDOM = $(this).is(':checked');
		});
		
		$('#show_hide_friends').unbind('click').click ( function (e) {
			if ($('#friends_list:visible').length>0) {
				$('#friends_list').fadeOut();
				$(this).html('Show Friends');
			} else {
				$('#friends_list').fadeIn();
				$(this).html('Hide Friends');
			}
		});
		
		$('#invite_friends').unbind('click').click ( function (e) {
			StarSMP.track("invite.click", document.location.href);
			FB.ui({method: 'apprequests',
			  message: 'Watch your favorite videos from your or your friends wall.'
			});
		});
		
	});
	
	
	function loadVideos(callback) {
		showLoading();

		$.getJSON(
			"ajax.php?action=load_videos&fb_user_id="+FB_USER_ID+'&self='+SELF_ID,
			function(data) {
				if (data) {
					if (data.last_timestamp) 
						LAST_TIMESTAMP = parseInt(data.last_timestamp);
					var old_data_count = data.data.length;
					FB.api('/'+FB_USER_ID+'/posts?limit=25&access_token='+ACCESS_TOKEN, function(response) {
						response.data = combineData(data.data, response.data);
						callback(response, (response.data.length-old_data_count));
					});

/* 					if (data.data.length == 0) {
						FB.api('/'+FB_USER_ID+'/posts?limit=25', function(response) {
							callback(response);
						});
					} else {
						callback(data, false);
					}
 */				} else
					alert('response is empty');
			}
		);

        $('#video_search').keyup( function (e) {
            var val = $(this).val();
            if (val) {
                if (e.keyCode == 13) {
                    searchVideos(val);
                }
            } else {
                showAllVideos();
            }
        });
	}
	
	function combineData(data_big, data_small) {
		var res = [];
		for (var i in data_small) {
			var val = data_small[i];
			if ( ((val.type == 'video') || (val.type == 'swf') || (val.type == 'link')) && (val.source) && ((val.source.indexOf('youtube.com')!=-1) || (val.source.indexOf('youtu.be')!=-1))) {
				var video_id = getVideoId(val.link);
				if (!dataExists(video_id, data_big)) {
					res.push(val);
				}
			}
		}
		
		return res.concat(data_big);
	}

	function dataExists(video_id, in_array) {
		for (var i in in_array) {
			if (in_array[i].video_id == video_id)
				return true;
		}
		return false;
	}
	
	function fillVideos(response, update) {
		if (typeof(update) == "undefined") {
			update = true;
		}
		var to_write = [];
		$.each (response.data, function (i, val) {
			if (((val.type == 'swf') || (val.type == 'video') || (val.type == 'link')) && (val.link) && ((val.link.indexOf('youtube.com')!=-1)  || (val.link.indexOf('youtu.be')!=-1))) {
				videos.push({video_id: getVideoId(val.link), link: val.link, name: val.name, created_time: val.created_time});
				to_write.push({video_id: getVideoId(val.link), link: val.link, name: val.name, created_time: val.created_time});
			}
		});
		var ok = false;
		writeVideos(to_write, update);
		if (videos.length < LIMIT && response.paging && response.paging.next) {
			var until = response.paging.next.split('&until=');
			until = parseInt(until[1]);
			if (until > LAST_TIMESTAMP) {
				setTimeout( function () {
					FB.api(response.paging.next.replace('https://graph.facebook.com', ''), function(response) {
						fillVideos(response);
					});
				}, 1000);
			} else {
				ok = true;
			}
		} else
			ok = true;
		
		if (ok) {
            def_videos = videos.slice(0);
            hideLoading();
			panes.push($('#videos').jScrollPane().data('jsp'));
			$('.delete_ico').unbind('click').click ( function (e) {
				e.preventDefault();
				
				StarSMP.track("delete.item", document.location.href);
				var id = $(this).attr('id').split('_');
				id = id[1];
				deleteVideo(id);
			});
		}
	}
	
	function getVideoId(link) {
		if ((link.indexOf('youtu.be')>-1) && (link.indexOf('v=')<=0)) {
			var youtube_id = link.split('youtu.be/')[1];
		} else if (link.indexOf('attribution_link')>-1) {
			var youtube_id = link.split('watch%3Fv%3D')[1];
            youtube_id = youtube_id.split('%26feature')[0];
		} else {
			var youtube_id = link.split('v=')[1];
			if (typeof youtube_id != 'undefined') {
				var ampersandPosition = youtube_id.indexOf('&');
				if(ampersandPosition != -1) {
				  youtube_id = youtube_id.substring(0, ampersandPosition);
				}
			}
		}
		
		
		return youtube_id;
		
	}

	var link_ev = function (e) {
		e.preventDefault();
		
		var id = $(this).attr('id').split('_');
		id = parseInt(id[1]);
		inc = id;
		var link = $(this).attr('href');
		$('#videos p.playing').removeClass('playing');
		$(this).parent('p').addClass('playing');
		
		StarSMP.track("select.direct", document.location.href);
		selectVideoLink(id, false);
		
		// $('#video_player').html('').append(iframe);
		play(videos[inc].video_id);
		return false;
	}
	
	var shareLink = '//www.facebook.com/plugins/like.php?href=[[LINK]]&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=dark&amp;&amp;height=24&amp;appId=311789798881087';
	
	function selectVideoLink(id, scroll) {
		if (typeof(scroll) == "undefined") {
			scroll = true;
		}
		$('#videos p.playing').removeClass('playing');
		$('#video_'+id+':visible').parent('p').addClass('playing');
		$('.control_buttons').show();
		//var sl = shareLink.replace('&amp;', '&');
		//sl = sl.replace('[[LINK]]', videos[id].link);
		//$('#fb_like').attr('src', sl);
		var p = $('#video_'+id+':visible').parent('p').position();
		
		if (scroll)
			panes[1].scrollTo(p.left, p.top);
	}
	
	var writeInc = 0;
	function writeVideos(to_write, update) {
		var link = p = del = null;
		var videos_ids = [];
		var videos_names = [];
		var videos_times = [];
		var $el = $('#videos');
		// var pane = panes[1].getContentPane();
		for (i in to_write) {
			p = $('<p></p>');
			link  = $('<a></a>')
				.unbind('click').click (link_ev)
				.attr('href', to_write[i].link)
				.attr('video_id', to_write[i].video_id)
				.attr('id', 'video_'+writeInc)
				.html(to_write[i].name);
			del = $('<span>&nbsp;</span>')
				.attr('id', 'delvideo_'+writeInc)
				.addClass('delete_ico');
			writeInc++;
			
			p.append(del);
			p.append(link);
			$el.append(p);
			// pane.append(p);
			videos_ids.push(to_write[i].video_id);
			videos_names.push(to_write[i].name);
			videos_times.push(to_write[i].created_time);
		}
		// panes[1].reinitialise();

		if (update && videos_ids.length>0) {
			if (update === true)
				update = videos_ids.length;
			$.post(
				'ajax.php?action=add_videos',
				{
					fb_user_id: FB_USER_ID,
					self: SELF_ID,
					videos: videos_ids.splice(0, update).join(','),
					videos_name: videos_names.splice(0, update).join('|~|'),
					videos_times: videos_times.splice(0, update).join('|~|')
				}
			);
		}
	}
	
	function getVideos() {
		// $('#videos').html('');
	}
	
	var ytplayer = null;
	function play(id) {
		if (ytplayer) {
			ytplayer.loadVideoById(id);
		} else {
			var params = { allowScriptAccess: "always", allowfullscreen: "true" };
			var atts = { id: "myytplayer" };
			var youtube_id = id;
			swfobject.embedSWF("//www.youtube.com/v/"+youtube_id+"?enablejsapi=1&playerapiid=ytplayer&version=3","ytapiplayer", "425", "356", "8", null, null, params, atts);
		}
	}
	
	function playNext() {
		inc += 1;
		if (inc == videos.length)
			inc = 0;
		var next = videos[inc];
		if (ytplayer) {
			selectVideoLink(inc);
			ytplayer.loadVideoById(next.video_id);
		}		
	}
	
	function playPrevNext(offset) {
		inc += offset;
		if (inc < 0)
			inc = videos.length + inc;
		if (inc >= videos.length)
			inc = inc-videos.length;
		play(videos[inc].video_id);
		selectVideoLink(inc);
	}
	
	function onYouTubePlayerReady(playerId) {
	  ytplayer = document.getElementById("myytplayer");
	  ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
	  ytplayer.setPlaybackQuality('large');
	  ytplayer.playVideo();
	}	

	function onytplayerStateChange(newState) {
	    // console.log("Player's new state: " + newState);
		if (newState == 0) {
			if (RANDOM) {
            var rand = Math.floor(Math.random()*videos.length);
			playPrevNext(rand);
            } else {
                playNext();
            }
		}
		if (newState == -1) {
			// deleteVideo();
		}
	}	
	
	function getFriends(callback) {
		FB.api('/me/friends', function (response) {
			var $el = $('#friends_list2');
			var pane = panes[0].getContentPane();
			//$('#fl_search_input').val('Search');
			for (var i in response.data) {
				FRIENDS.push(response.data[i].id);

            }
            $.post(
                'ajax.php?action=get_friends',
                {
                    friends: FRIENDS.join(','),
                    fb_user_id: SELF_ID
                },
                function (json) {
                    if (json['result']== 'ok') {
                        for (var i in json['visible_friends']) {
                            var a = $('<a></a>')
                                .html('<img src="https://graph.facebook.com/'+response.data[i].id+'/picture" alt="'+response.data[i].name+'"/>')
                                .attr('href', '?fb_user_id='+response.data[i].id)
                                .attr('title', response.data[i].name);
                            pane.append(a);
                        }
                    }
                    panes[0].reinitialise();
                    if (typeof(callback) != "undefined") {
                        callback();
                    }
                }
            );
		});
	}

	function writeUser() {
		$('#fb_image').attr('src', 'https://graph.facebook.com/'+FB_USER_ID+'/picture');
		$('#fb_image').show();
		$('#fb_name_a')
			.html(FB_USERNAME)
			.attr('href', 'https://www.facebook.com/profile.php?id='+FB_USER_ID)
			.attr('target', '_blank');
		if (SELF_ID != FB_USER_ID) {
			$('#fb_change_to_default').find('a')
				.html(SELF_NAME)
				.attr('href', '?fb_user_id='+SELF_ID);
			$('#fb_change_to_default').show();
		}
	}
	
	function deleteVideo(id) {
		$.post(
			'ajax.php?action=del_video',
			{
				fb_user_id: SELF_ID,
				video_id: videos[id].video_id
			},
			function (data) {
				if (data == 'ok') {
					$('#video_'+id+':visible').parent('p').remove();
					videos.splice(id, 1);
					redoLinks();
				} else {
					console.log(data);
				}
			}
		);
	}
	
	function redoLinks() {
		if (videos.length == $('#videos p:visible').length) {
			$.each ( $('#videos p:visible'), function (i, video){
				$(this).find('span.delete_ico').attr('id', 'delvideo_'+i);
				$(this).find('a').attr('id', 'video_'+i);
			});
		} else {
			console.log('redoLinks error');
		}
	}
	
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

function checkLikeAndShow() {
    FB.api('/me/likes/246302842129650', function(response) {
        if (typeof response.data[0] != 'undefined') {
            //user already like the page
            $('#like_overlay').fadeOut();
        } else {
            // if like is clicked
            FB.Event.subscribe('edge.create',
                function(response) {
                    $('#like_overlay').fadeOut();
                }
            );
            // show like
            var $el = $('#self_like').remove();
            $('#bulk').show();;
            $('#like_overlay').append($el);
            $('#like_overlay').fadeIn();
        }
    });
}

function showLoading() {
    $('.loading').show();
    $('#loading_videos').show();
    $('#video_search').attr('disabled', 'disabled');
}

function hideLoading() {
    $('.loading').hide();
    $('#loading_videos').hide();
    $('#video_search').removeAttr('disabled');
}