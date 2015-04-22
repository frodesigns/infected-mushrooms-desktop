var orightml;
$(window).load(function() {
  orightml = $("#slidercontainer").html();
  $('#slider').nivoSlider({
    controlNavThumbs:true,
	controlNavThumbsFromRel:true,
	captionOpacity: 0.9
  });
  $("#slider").append("<br style='clear: both;' />")
});
$(document).ready(function(){

	$("div.images img").click(function() {
		$("div.images img").removeClass("selectedImage");
		$(this).addClass("selectedImage");
		$("#imagepath").val($(this).attr("alt"));
	});
	
	$("#quickjump a, .image img").tipsy({
		opacity: 0.95
	});
	
	$(".itemhistory").tipsy({
		title: 'rel',
		opacity: 0.95
	});

	$(".profile").click(function() {
		var id = $(this).attr("rel");
		$("#message").html("<div id='close'>X</div>");
		$("#message").fadeIn("fast");
		$("#message").css("background", "#2A2C2B url(loading.gif) no-repeat 50% 50%");
			
		$.get("getprofile.php?id=" + id, function(data){
			dataToObj = eval("profile="+data);
			profile = dataToObj;			
			var profilestring = "";
			profilestring += "<span class='itemname'>" + profile.full_name + "'s Profile</span><br /><div style='color: white; font-weight: normal;'>";
			if (profile.real_name != "" && profile.country != "") {
				profilestring += "<strong>" + profile.real_name + " - " + profile.country + "</strong><br /><br />";
			}
			if (profile.lastactivity != "") {
				profilestring += "<strong>Last Activity:</strong> " + profile.lastactivity + " on " + profile.lastactivitytime + "<br /><br />";
			}
			profilestring += "<strong>MSN:</strong> " + profile.msn + "<br /><strong>Email:</strong> " + profile.user_email + "<br /><br /><strong><u>Characters:</u></strong><br />";
			if (profile.char1name != "") {
				profilestring += "<strong>" + profile.char1name + "</strong> - " + profile.char1lvl + " " + profile.char1type + "<br />";
			}
			if (profile.char2name != "") {
				profilestring += "<strong>" + profile.char2name + "</strong> - " + profile.char2lvl + " " + profile.char2type + "<br />";
			}
			if (profile.char3name != "") {
				profilestring += "<strong>" + profile.char3name + "</strong> - " + profile.char3lvl + " " + profile.char3type + "<br />";
			}
			if (profile.char4name != "") {
				profilestring += "<strong>" + profile.char4name + "</strong> - " + profile.char4lvl + " " + profile.char4type + "<br />";
			}
			
			profilestring += "</div>";
			
			$("#message").append(profilestring);
			$("#message").css("background", "#2A2C2B");
			
		});		
	});

	$("#monthselect").val( $("#month").val() );
	$("#yearselect").val( $("#year").val() );

  $('#itemlist form:nth-child(odd)').addClass('alternate');
  $('.threadpager li:nth-child(odd)').addClass('alternate');
  $('.forumpager li:nth-child(odd)').addClass('alternate');
  $("#myform").validate();
  $("#pform").validate();
  $("#logForm").validate();
  $.validator.addMethod("username", function(value, element) {
        return this.optional(element) || /^[a-z0-9\_]+$/i.test(value);
  }, "Username must contain only letters, numbers, or underscore.");

  $("#regForm").validate();
  $("#actForm").validate();		
		
	$("#quickjump a").click(function() {
		var itemid = "#" + $(this).attr('rel');
		$('html, body').animate({
			scrollTop: $(itemid).offset().top
		}, 500);
	});
  
  $("#itemlist img").click(function() {
	$('html, body').animate({
		scrollTop: $("#account").offset().top
	}, 500);		
  });
  
  $(".scrolltop").click(function() {
	$('html, body').animate({
		scrollTop: $("#account").offset().top
	}, 500);		
  });
  
  $(".scrollpost").click(function() {
	$('html, body').animate({
		scrollTop: $(".forms").offset().top
	}, 500);		
  });
  
  $(".application").click(function() {
	$("#title").val("Application: your name here");
	$(".tinymce").val("<p><span style='text-decoration: underline;'><strong>Your Name:</strong></span><br><br><br><span style='text-decoration: underline;'><strong>Your Character(s):</strong></span><br><br><br><span style='text-decoration: underline;'><strong>About Yourself:</strong></span><br><br><br><span style='text-decoration: underline;'><strong>Your Helbreath History:</strong></span><br><br><br><span style='text-decoration: underline;'><strong>Why do you think you would be a good fit for our guild?:&nbsp;</strong></span><br><br><br><span style='text-decoration: underline;'><strong>Any comments you want to add?:&nbsp;</strong></span><br><br></p>");
	$('html, body').animate({
		scrollTop: $(".forms").offset().top
	}, 500);		
  });
  
  $(".votechange").click(function() {
		if ($(this).val() == 1) {
			$(this).val("0")
		} else {
			$(this).val("1")
		}		
  });
  
	$("#close").live('click', function() {		
		$(this).parent().fadeOut("slow");
	});  
	
	$.extend({
		getValues: function(url) {
			var result = null;
			$.ajax({
				url: url,
				type: 'get',
				dataType: 'html',
				async: false,
				success: function(data) {
					result = data;
				}
			});
			return result;
		}
	});
  
	$(".foundsubmit").live('click', function() {
		var thisform = "#" + $(this).parent().parent().attr('id');	
		var currentusername = $("#currentusername").val();		
		$(thisform).ajaxForm({
			url: "foundsubmit.php",
			success: function(responseText) {	
				if (responseText == "Success!") {										
					$(thisform + " .found").html("Item reported as found!");
					$(thisform + " .found").addClass('flag');
					$(thisform + " .found").removeClass('found');
					$(thisform + " .foundsubmit").replaceWith("<input type='submit' class='button blue flagsubmit' name='flagsubmit' value='Flag It!' />");
				}
			}
		});
	});
	
	$(".sticky").live('click', function() {
		var thisform = "." + $(this).parent().attr('class');		
		$(thisform).ajaxForm({
			success: function(responseText) {	
				if (responseText == "Success!") {										
					$(thisform).replaceWith("<form class='unstickyform' name='unstickyform' action='unsticky.php' method='post'><input type='hidden' name='threadid' value='$threadid' /><input type='submit' class='unsticky button red' value='Unsticky' /></form>");
				}
			}
		});
	});
	
	$(".votelist").click(function() {
		var pollid = $(this).parent().parent().attr("id");
		var list = $.getValues("votelist.php?pollid=" + pollid);
		$("#message").html("<div id='close'>X</div>");
		$("#message").append(list);
		$("#message").fadeIn("fast");		
	});
	
	var pollcontent;
	$(".editpoll").click(function() {
		$(this).hide();
		var thispoll = "#" + $(this).parent().parent().attr("id");
		var pollid = $(this).parent().parent().attr("id");
		pollcontent = $(thispoll + " .polldescrip").html();
		$(thispoll + " .polldescrip").html("<form name='form' method='post' action='editpollsubmit.php'><input type='hidden' name='pollid' value='" + pollid + "' /><textarea class='tinymce' name='edit' class='editcontent' rows='10'>" + pollcontent + "</textarea><input type='submit' class='editpollsubmit button blue' value='Update' rel='" + pollid + "' /> <input type='button' class='cancelpollsubmit button blue' value='Cancel' rel='" + pollid + "' /></form>");
		if (!navigator.userAgent.match(/Android/i)) {		
			$('textarea.tinymce').tinymce({
				script_url : 'tiny_mce/tiny_mce.js',
				theme : "advanced",
				plugins : "emotions",
				theme_advanced_buttons1 : "bold,italic,underline,strikethrough,emotions,|,forecolor,fontsizeselect,|,blockquote,|,justifyleft,justifycenter,justifyright,|,indent,outdent,|,undo,redo,|,numlist,bullist,hr,|,link,unlink,image",
				theme_advanced_buttons2 : "cut,copy,paste,|,removeformat",
				theme_advanced_buttons3 : "",
				theme_advanced_buttons4 : "",
				theme_advanced_toolbar_align : "left",
				theme_advanced_toolbar_location : "top",
				content_css : "styles.css"
			});
		}
	});
	
	$(".cancelpollsubmit").live('click', function() {
		var pollid = $(this).attr('rel');
		$("#" + pollid + " .editpoll").show();
		$("#" + pollid + " .polldescrip").html(pollcontent);
	});
	
	$(".editpollsubmit").live('click', function() {
		var pollid = $(this).attr('rel');
		$(this).parent().ajaxForm({			
			success: function(responseText) {
				$("#" + pollid + " .editpoll").show();
				$("#" + pollid + " .polldescrip").html(responseText);
			}
		});
	});
	
	$(".deletepollbutton").click(function() {	
		var itemtoremove = $(this).parent().parent().parent().attr('id');		
		$(".deletepoll").ajaxForm({
			beforeSubmit: function() {		
				return confirm('Are you sure you want to delete this poll?');				
			},
			success: function(responseText) { 
				if (responseText == "Success!") {		
					$("#" + itemtoremove).slideUp("fast");
				}
			}
		});
	});
	
	$(".unsticky").live('click', function() {
		var thisform = "." + $(this).parent().attr('class');		
		$(thisform).ajaxForm({
			success: function(responseText) {	
				if (responseText == "Success!") {										
					$(thisform).replaceWith("<form class='stickyform' name='stickyform' action='sticky.php' method='post'><input type='hidden' name='threadid' value='$threadid' /><input type='submit' class='sticky button red' value='Sticky' /></form>");
				}
			}
		});
	});
	
	$(".flagsubmit").live('click', function() {
		var thisform = "#" + $(this).parent().parent().attr('id');
		var currentusername = $("#currentusername").val();
		$(thisform).ajaxForm({
			url: "flagsubmit.php",
			success: function(responseText) {
				if (responseText == "Success!") {		
					$(thisform + " .flag").html("Flagged as missing by <strong>" + currentusername + "</strong>!");
					$(thisform + " .flag").addClass('found');
					$(thisform + " .flag").removeClass('flag');	
					$(thisform + " .flagsubmit").replaceWith("<input type='submit' class='button blue foundsubmit' name='foundsubmit' value='Found It!' />");	
				}
			}
		});
	});

	$(".updatesubmit").click(function() {				
		var thisform = "#" + $(this).parent().parent().attr('id');	
		var currentusername = $("#currentusername").val();
		var selectedvalue = $(thisform + " .passedto").val();
		var selectedperson = $(thisform + " .passedto option[value='" + selectedvalue + "']").text();			
		var time = $.getValues("time.php");		
		var listcount = $(thisform + " .lineitem").size();
		if (listcount == 5) {
			var height = $(thisform + " .info").css('height');	
			$(thisform + " .info").css('height', height);
			$(thisform + " .info").css('overflow', 'hidden');
		}				
		$(thisform).ajaxForm({
			url: "updatesubmit.php",
			success: function(responseText) {
				if (responseText == "Tracker Updated!") {
					$(thisform + " .info .itemname").after("<span style='display: none;' class='lineitem'><strong>" + selectedperson + "</strong> had this on " + time + "<br /><span class='reporter'>Reported by <strong>" + currentusername + "</strong></span></span>");
					$(thisform + " .info .lineitem").first().show('normal', function() { 						
						listcount = listcount + 1
						if (listcount > 5) {						
							$(thisform + " .info .lineitem").last().slideUp("fast", function() { $(this).remove(); } );
						}
						$(thisform + " .passedto").val("");
						//$("#message").html("<div id='close'>X</div>");
						//$("#message").append(responseText);
						//$("#message").fadeIn("fast");
					});					
				} else {
					$("#message").html("<div id='close'>X</div>");
					$("#message").append(responseText);
					$("#message").fadeIn("fast");
				}
			}
		});

	});
	
	$(".loggedsubmit").click(function() {
		var thisform = "#" + $(this).parent().parent().attr('id');
		var currentusername = $("#currentusername").val();	
		var time = $.getValues("time.php");		
		var listcount = $(thisform + " .lineitem").size();
		if (listcount == 5) {
			var height = $(thisform + " .info").css('height');	
			$(thisform + " .info").css('height', height);
			$(thisform + " .info").css('overflow', 'hidden');
		}		
		$(thisform).ajaxForm({
			url: "loggedsubmit.php",
			success: function(responseText) {
				if (responseText == "Tracker Updated!") {
					$(thisform + " .info .itemname").after("<span style='display: none;' class='lineitem'><strong>" + currentusername + "</strong> logged out with this item<br /><span class='reporter'>" +time + "</span></span>");
					$(thisform + " .info .lineitem").first().show("normal", function() { 
						listcount = listcount + 1
						if (listcount > 5) {
							$(thisform + " .info .lineitem").last().slideUp("fast", function() { $(this).remove(); } );
						}		
						//$("#message").html("<div id='close'>X</div>");
						//$("#message").append(responseText);
						//$("#message").fadeIn("fast");
					});						
				} else {
					$("#message").html("<div id='close'>X</div>");
					$("#message").append(responseText);
					$("#message").fadeIn("fast");
				}
			}
		});	
		
	});
  
	$("#medusaform").ajaxForm({
        success: function(responseText) { 
            if (responseText == "<head></head><body>Screenshot added successfully!</body>") {
				var fileArray = $("#file").val().split('\\');
				var killname = $("#killname").val();
				var currentusername = $("#currentusername").val();
				var filename = fileArray[fileArray.length-1];
				var filenamefix = filename.replace(" ", "%20");
				$("#slidercontainer").html("");
				$("#slidercontainer").append(orightml);				
				$("#slider").prepend("<img src='http://www.frodesigns.com/im/medusa/" + filenamefix + "' alt='' title='" + currentusername + " killed " + killname + "<br /><small>Permalink: http://www.frodesigns.com/im/medusa/" + filename + "</small>' rel='http://www.frodesigns.com/im/medusa/thumbs/" + filename + "' />");
				orightml = $("#slidercontainer").html();
				// var currentusername = $("#currentusername").val();
				// var scoreitem = $("#" + currentusername).html();
				// if (scoreitem != "") {
					// var scoreboardArray = scoreitem.split('-');
					// var name = $.trim(scoreboardArray[0]);
					// var count = parseInt($.trim(scoreboardArray[1]));
					// count = count + 1;
					// $("#" + currentusername).html(name + " - " + count);
				// }
					// var deathitem = $("." + killname).html();
				// if (deathitem != "") {
					// var deathArray = deathitem.split('-');
					// var deathname = $.trim(deathArray[0]);
					// var deathcount = parseInt($.trim(deathArray[1]));
					// deathcount = deathcount + 1;
					// $("." + killname).html(deathname + " - " + deathcount);	
				// }
				$('#slider').nivoSlider({
					controlNavThumbs:true,
					controlNavThumbsFromRel:true,
					captionOpacity: 0.9
				});
				$("#message").html("<div id='close'>X</div>");
				$("#message").append(responseText);
				$("#message").fadeIn("fast");
				$("#medusaform").clearForm();
				$("#medusaform #file").replaceWith("<input type='file' name='image' id='file' />");
				
			} else {
				$("#message").html("<div id='close'>X</div>");
				$("#message").append(responseText);
				$("#message").fadeIn("fast");
			}
        } 
	});
	
	$("#newthread").ajaxForm({
        success: function(responseText) { 
            if (responseText == "<head></head><body>You can't pretend to be a guild member!  Please choose a different name.</body>" || responseText == "<head></head><body>Spam Detected!</body>" || responseText == "<head></head><body>You have to fill in all of the fields!</body>" ) {
				$("#message").html("<div id='close'>X</div>");
				$("#message").append(responseText);
				$("#message").fadeIn("fast");
			} else {
				window.location = "thread.php?threadid=" + responseText.replace(/<.*?>/g, '');
			}
		}
	});
	
	$("#newprivthread").ajaxForm({
        success: function(responseText) { 
            if (responseText == "<head></head><body>You have to fill in all of the fields!</body>" ) {
				$("#message").html("<div id='close'>X</div>");
				$("#message").append(responseText);
				$("#message").fadeIn("fast");
			} else {
				window.location = "thread.php?threadid=" + responseText.replace(/<.*?>/g, '');
			}
		}
	});
	
	$("#pollform").ajaxForm({
        success: function(responseText) { 
            if (responseText == "<head></head><body>You have to fill in all of the fields!</body>" ) {
				$("#message").html("<div id='close'>X</div>");
				$("#message").append(responseText);
				$("#message").fadeIn("fast");
			} else {
				location.reload(true);
			}
		}
	});
	$("#replybutton").click(function() {
		$("#ajaxloader").show();
		$(this).hide();
		$("#threadreply").ajaxForm({
			success: function(responseText) { 
				if (responseText == "<head></head><body>You can't pretend to be a guild member!  Please choose a different name.</body>" || responseText == "<head></head><body>Spam Detected!</body>" || responseText == "<head></head><body>You have to fill in all of the fields!</body>" ) {
					$("#message").html("<div id='close'>X</div>");
					$("#message").append(responseText);
					$("#message").fadeIn("fast");
					$("#replybutton").show();
					$("#ajaxloader").hide();
				} else {
					window.location = "thread.php?threadid=" + responseText.replace(/<.*?>/g, '') + "#last";
					location.reload(true);
				}
			}
		});
	});
	
	$("#newmessage").ajaxForm({
        success: function(responseText) { 
            if (responseText == "<head></head><body>Message Sent!</body>" ) {
				var title = $("#messagetitle").val();
				var sendername = $(".sendername").val();
				var reciever = $(".passedto option:selected").text();
				var content = $("textarea").val();
				var time = $.getValues("time.php");	
				$(".sentlist").prepend("<li class='message newsent' style='display: none;'><a class='messagesenttitle itemname boldauth'>" + title + "</a><br />Sent to <strong>" + reciever + "</strong> on <em>" + time + "</em><div class='messagecontent'>" + content + "</div></li>");
				$( ".messages" ).tabs( "option", "selected", 1 );
				$(".newsent").slideDown('normal');
				$(".passedto").val("");
				$("textarea").val("");
				$("#newmessage").clearForm();
			} else {		
				$("#message").html("<div id='close'>X</div>");
				$("#message").append(responseText);
				$("#message").fadeIn("fast");				
			}
		}
	});
	
	var content;
	
	$(".edit").click(function() {
		var postid = $(this).attr('rel');
		content = $("#" + postid + " .content").html();
		$("#" + postid + " .content").html("<form name='form' method='post' action='editsubmit.php'><input type='hidden' name='postid' value='" + postid + "' /><textarea class='tinymce' name='edit' class='editcontent' rows='15' cols='70'>" + content + "</textarea><input type='submit' class='editsubmit button blue' value='Update' rel='" + postid + "' /> <input type='button' class='cancelsubmit button blue' value='Cancel' rel='" + postid + "' /></form>");
		$(this).hide();
		$("#" + postid + " a.quotepost").hide();
		if (!navigator.userAgent.match(/Android/i)) {	
			$('textarea.tinymce').tinymce({
				script_url : 'tiny_mce/tiny_mce.js',
				theme : "advanced",
				plugins : "emotions",
				theme_advanced_buttons1 : "bold,italic,underline,strikethrough,emotions,|,forecolor,fontsizeselect,|,blockquote,|,justifyleft,justifycenter,justifyright,|,indent,outdent,|,undo,redo,|,numlist,bullist,hr,|,link,unlink,image,|,cut,copy,paste,|,removeformat",
				theme_advanced_buttons2 : "",
				theme_advanced_buttons3 : "",
				theme_advanced_buttons4 : "",
				theme_advanced_toolbar_align : "left",
				theme_advanced_toolbar_location : "top",
				content_css : "styles.css"
			});
		}
	});
	
	$(".threadpager .delete").click(function() {	
		var litoremove = $(this).parent().parent().parent().parent().attr('id');		
		$(".deletepost").ajaxForm({
			beforeSubmit: function() {		
				return confirm('Are you sure you want to delete this post?');				
			},
			success: function(responseText) { 
				if (responseText == "Success!") {	
					$("#" + litoremove).before("<div style='height: 1px; padding: 0; background: #D9CB9E;'></div>");
					$("#" + litoremove).slideUp("fast");					
				}
			}
		});
	});
	
	$(".deletethread").click(function() {		
		$(".deletethreadform").ajaxForm({
			beforeSubmit: function() {		
				return confirm('Are you sure you want to delete this thread?');				
			},
			success: function(responseText) { 
				if (responseText == "Success!") {		
					window.location = "forum.php";
				}
			}
		});
	});
	
	$(".deleteprivthread").click(function() {		
		$(".deletethreadform").ajaxForm({
			beforeSubmit: function() {		
				return confirm('Are you sure you want to delete this thread?');				
			},
			success: function(responseText) { 
				if (responseText == "Success!") {		
					window.location = "privforum.php";
				}
			}
		});
	});
	
	$(".voteyes").click(function() {		
		var thispoll = "#" + $(this).parent().parent().parent().parent().attr("id");
		$(thispoll + " .voteyes").hide();
		$(thispoll + " .voteno").hide();
		$(thispoll + " .votehidden").val("1");
		var width = $(this).attr("rel");		
		$(thispoll + " form").ajaxForm({
			success: function(responseText) { 
				if (responseText == "Success!") {		
					if (width == 100) {
						$(thispoll + " .ui-green .progressbar div").addClass("ui-corner-right");
					}
					$(thispoll + " .ui-green .progressbar").removeClass("zero");
					$(thispoll + " .ui-green .progressbar div").animate({width: width + "%"}, 500);		
					$(thispoll + " .ui-green .progresstext").html(width + "%");
					$(thispoll + " .buttons").html("Thank you for voting!");				
				}
			}
		});		
	});
	
	$(".voteno").click(function() {
		var thispoll = "#" + $(this).parent().parent().parent().parent().attr("id");
		$(thispoll + " .voteyes").hide();
		$(thispoll + " .voteno").hide();
		$(thispoll + " .votehidden").val("0");
		var width = $(this).attr("rel");	
		$(thispoll + " form").ajaxForm({
			success: function(responseText) { 
				if (responseText == "Success!") {		
					if (width == 100) {
						$(thispoll + " .ui-red .progressbar div").addClass("ui-corner-right");
					}
					$(thispoll + " .ui-red .progressbar").removeClass("zero");
					$(thispoll + " .ui-red .progressbar div").animate({width: width + "%"}, 500);		
					$(thispoll + " .ui-red .progresstext").html(width + "%");
					$(thispoll + " .buttons").html("Thank you for voting!");				
				}
			}
		});		
	});
	
	$(".cancelsubmit").live('click', function() {
		var postid = $(this).attr('rel');
		$("#" + postid + " .content").html(content);
		$("#" + postid + " a.edit").show();
		$("#" + postid + " a.quotepost").show();
	});
	
	$(".editsubmit").live('click', function() {
		var postid = $(this).attr('rel');
		$(this).parent().ajaxForm({			
			success: function(responseText) {
				$("#" + postid + " .content").html(responseText);
				$("#" + postid + " a.edit").show();
				$("#" + postid + " a.quotepost").show();
			}
		});
	});
	
	$(".quotepost").click(function() {
		var postid = $(this).attr('rel');
		content = $("#" + postid + " .content").html();
		$("#" + postid + " .content blockquote").remove();
		var strippedcontent = $("#" + postid + " .content").html();
		$("#" + postid + " .content").html(content);
		var name = $(this).attr('alt');
		if (!navigator.userAgent.match(/Android/i)) {	
			$(".forms textarea").append("<blockquote><u><strong>" + name + " wrote:</strong></u><br />" + strippedcontent + "</blockquote><p></p>");
		} else {
			$(".forms textarea").val("<blockquote><u><strong>" + name + " wrote:</strong></u><br />" + strippedcontent + "</blockquote><p></p>");
		}
		$('html, body').animate({
			scrollTop: $(".forms").offset().top
		}, 500);
	});
	
	$(".markallread").click(function() {
		var answer = confirm('Are you sure you want to mark all forum threads as read?');				
		
		if (answer){
			$.ajax({
				url: "markallread.php",
				success: function(data) {				
					window.location.reload();
				}
			});
		}
	});
	
	$(".messagedelete").click(function() {
		var itemtoremove = $(this).parents("li.message");
		var messageid = $(this).attr('rel');
		var answer = confirm('Are you sure you want to delete this message?');				
		
		if (answer){
			$.ajax({
				url: "deletemessage.php?messageid=" + messageid,
				success: function(data) {				
					$(itemtoremove).slideUp("fast");
				}
			});
		}
	});
	
	$(".messagereplynoquote").click(function() {
		var messageid = $(this).attr('rel');
		content = $("#" + messageid + " .themessage").html();
		var title = $("#" + messageid + " .messagetitle").html();
		if (title.substr(0, 3) != "RE:") {
			title = "RE: " + title;
		}
		var name = $(this).attr('alt');
		//$(".forms textarea").html("").append("<blockquote><u><strong>" + name + " wrote:</strong></u><br />" + content + "</blockquote><br />");
		$("#messagetitle").val(title);	
		$(".replytomessageid").val(messageid);		
		$(".passedto").find("option:contains('" + name + "')").each(function(){
			$(this).attr("selected","selected");
		});		
		$('html, body').animate({
			scrollTop: $(".forms").offset().top
		}, 500);
	});
	
	$(".messagereply").click(function() {
		var messageid = $(this).attr('rel');
		content = $("#" + messageid + " .themessage").html();
		var title = $("#" + messageid + " .messagetitle").html();
		if (title.substr(0, 3) != "RE:") {
			title = "RE: " + title;
		}
		var name = $(this).attr('alt');
		$(".forms textarea").html("").append("<blockquote><u><strong>" + name + " wrote:</strong></u><br />" + content + "</blockquote><br />");
		$("#messagetitle").val(title);	
		$(".replytomessageid").val(messageid);		
		$(".passedto").find("option:contains('" + name + "')").each(function(){
			$(this).attr("selected","selected");
		});		
		$('html, body').animate({
			scrollTop: $(".forms").offset().top
		}, 500);
	});	
	
	var autovalues = $.getValues("autocomplete.php");
	var availableTags = autovalues.split(",");
	$("#killname").autocomplete({ source: availableTags, minLength: 2 });	
	if (!navigator.userAgent.match(/Android/i)) {	
		$('textarea.tinymce').tinymce({
			script_url : 'tiny_mce/tiny_mce.js',
			plugins : "emotions",
			theme : "advanced",
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,emotions,|,forecolor,fontsizeselect,|,blockquote,|,justifyleft,justifycenter,justifyright,|,indent,outdent,|,undo,redo,|,numlist,bullist,hr,|,link,unlink,image,|,cut,copy,paste,|,removeformat",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_align : "left",
			theme_advanced_toolbar_location : "top",
			content_css : "styles.css"
		});
	}
	
	$(".threecolumn ul").quickPager({pageSize:10});
	$(".sentlist").quickPager({pageSize:10, pagerLocation: 'both'});
	$(".inboxlist").quickPager({pageSize:10, pagerLocation: 'both'});
	$(".threecolumn .simplePagerNav").before("Page: ");
	
	$(".messages").tabs();	
	
	$(".messagetitle").click(function() {
		var messageid = $(this).parent().parent().attr("id");		
		$(this).parent().ajaxForm({
			success: function(responseText) { 
				if (responseText == "Success!") {
					$("#" + messageid + " .messagetitle").removeClass("boldauth");
					if ($("#" + messageid + " .messagecontent").is(":visible")) {
						$("#" + messageid + " .messagecontent").slideUp("normal");
					} else {
						$("#" + messageid + " .messagecontent").slideDown("normal");
					}
				}
			}
		});
		$(this).parent().submit();
	});
	
	$("#scoreboard li").click(function() {
		$("#scoreboard li").removeClass("sitered");
		$(this).addClass("sitered");
		var name = $(this).attr("id");
		$(".nivo-controlNav img").removeClass("highlighted");
		$("." + name).addClass("highlighted");
	});
	
	// $(".itemhistory").click(function() {
		// var history = $(this).attr("rel");
		// if (history == "") {
			// history = "No history for this item."
		// }
		// var itemname = $(this).html();
		// $("#message").html("<div id='close'>X</div>");
		// $("#message").append("<span class='itemname' style='font-weight: normal;'>" + itemname + "</span><br /><span style='color: white; font-weight: normal;'>" + history + "</span>");
		// $("#message").fadeIn("fast");	
	// });
	
	$(".messagesenttitle").live('click', function() {		
		var messageid = $(this).parent().attr("id");	
		if ($(this).parent().find('.messagecontent').is(":visible")) {
			$(this).parent().find('.messagecontent').slideUp("normal");
		} else {
			$(this).parent().find('.messagecontent').slideDown("normal");
		}
	});

	$(".nav").find("a[href='"+window.location.pathname+"']").each(function(){
		$(this).addClass("current");
	});
	

	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();		

	var calendar = $('#calendar').fullCalendar({
		theme: true,
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek'
		},
		selectable: true,
		selectHelper: true,
		select: function(start, end, allDay) {
			$("#rangeDemoStart").AnyTime_noPicker()
			$("#rangeDemoFinish").AnyTime_noPicker()
			var startMonth = start.getMonth() + 1;
			if (startMonth < 10) {
				startMonth = "0" + startMonth;
			}
			var startDay = start.getDate();
			if (startDay < 10) {
				startDay = "0" + startDay;
			}
			var startHours = start.getHours().toString();
			if (startHours < 10) {
				startHours = "0" + startHours;
			}
			var startMinutes = start.getMinutes().toString();
			if (startMinutes == 0) {
				startMinutes = "00";
			}
			var startYear = start.getFullYear();			
			var startDate = startYear + "-" + startMonth + "-" + startDay + " " + startHours + ":" + startMinutes + ":00";

			var endMonth = end.getMonth() + 1;
			if (endMonth < 10) {
				endMonth = "0" + endMonth;
			}
			var endDay = end.getDate();
			if (endDay < 10) {
				endDay = "0" + endDay;
			}
			var endHours = end.getHours().toString();
		    if (endHours < 10) {
				endHours = "0" + endHours;
			}
			var endMinutes = end.getMinutes();
			if (endMinutes == 0) {
				endMinutes = "00";
			}
			var endYear = end.getFullYear();			
			var endDate = endYear + "-" + endMonth + "-" + endDay + " " + endHours + ":" + endMinutes + ":00" ;	
			
			$("#rangeDemoStart").val(startDate);
			$("#rangeDemoFinish").val(endDate);
			$("#eventform").fadeIn("fast");
			
			//--set up datetime range pickers
			var oneDay = 24*60*60*1000;
			var rangeDemoFormat = "%Y-%m-%d %H:%i:%s";
			var rangeDemoConv = new AnyTime.Converter({format:rangeDemoFormat});
			$("#rangeDemoToday").click( function(e) {
				$("#rangeDemoStart").val(rangeDemoConv.format(new Date())).change(); 
				$("#rangeDemoFinish").val(rangeDemoConv.format(new Date())).change();
			});
			$("#rangeDemoClear").click( function(e) {
				$("#rangeDemoStart").val("").change(); 
				$("#rangeDemoFinish").val("").change();
			});
			$("#rangeDemoStart").AnyTime_picker({format:rangeDemoFormat});
			var fromDay = rangeDemoConv.parse($("#rangeDemoStart").val());
			var dayLater = new Date(fromDay);
			dayLater.setHours(0,0,0,0);
			var ninetyDaysLater = new Date(fromDay+(90*oneDay));
			ninetyDaysLater.setHours(23,59,59,999);	
			$("#rangeDemoFinish").AnyTime_picker({ 
				earliest: dayLater,
				format: rangeDemoFormat,
				latest: ninetyDaysLater
			});			
			
			//--ajax insert into DB
			$("#addeventform").ajaxForm({
				success: function(responseText) { 
					var title = $("#eventname").val();	
					var starttime = $("#rangeDemoStart").val();
					var endtime = $("#rangeDemoFinish").val();						
					
					//--display on calendar after successful callback
					calendar.fullCalendar('renderEvent',
						{
							id: responseText,
							title: title,
							start: starttime,
							end: endtime
						},
						true // make the event "stick"
					)
					
					$("#addeventform").clearForm();
					$("#eventform").fadeOut("normal");
				}
			});			
			
			calendar.fullCalendar('unselect');
		},				
		eventClick: function(event, element) {
			var eventid = event.id;
			var title = event.title;
			
			//get attendees with ajax and display in message box with button to add your name to list
			var list = $.getValues("eventattendees.php?eventid=" + eventid);
			$("#message").html("<div id='close'>X</div>");
			$("#message").append(list);
			$("#message").fadeIn("fast");	
			
			$("#addeventform").ajaxForm({
				success: function(responseText) { 
					if (responseText == "Success!" ) {
						$("#joinevent").attr("disabled", "true");
						$(".attendeelist").append($("#currentname").val());
					}
				}
			});
			
		},
		allDaySlot: false,
		allDayDefault: false,
		events: "json-events.php",					
		loading: function(bool) {
			if (bool) $('#loading').show();
			else $('#loading').hide();
		}			
	});		

});