function sendData(callback, target) {
    var option;
    if (callback === "loadPostResult") {
        option = {
            beforeSend: function() {
                showuidfeedback(target);
            },
            success: function(response, statusText, xhr) {
                loadPostResult(response, statusText, target);
            },
            data: {
                param: "postResult",
                g: target.term,
                more: target.more,
                start: target.start,
                limit: target.limit
            }
        };

    } else if (callback === "loadCommunityResult") {
        option = {
            beforeSend: function() {
                showuidfeedback(target);
            },
            success: function(response, statusText, xhr) {
                loadCommunityResult(response, statusText, target);
            },
            data: {
                param: "communityResult",
                g: target.term,
                more: target.more,
                start: target.start,
                limit: target.limit
            }
        };
    } else if (callback === "loadPeopleResult") {
        option = {
            beforeSend: function() {
                showuidfeedback(target);
            },
            success: function(response, statusText, xhr) {
                loadPeopleResult(response, statusText, target);
            },
            data: {
                param: "peopleResult",
                g: target.term,
                more: target.more,
                start: target.start,
                limit: target.limit
            }
        };

    }
    $.ajax(option);
}
function showuidfeedback(target) {
    if (target.loadImage)
        $(target.target).html("<center><img src='images/loading.gif' style='border:none' /></center>");
    return true;
}
function manageError(jqXHR, textStatus, errorThrown) {
    var msg = "";
    if (textStatus === "timeout") {
        msg = "Network timeout.";
    } else if (textStatus === "parsererror") {
        msg = "Opps! something critical just happened...Our team will fix this soon ";
    } /*else if (jqXHR.readyState === 0) {
     msg = "No internet connection.";
     }*/
    if (msg !== "")
        humane.log(msg, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-error'});
    option = {
        param: "logError",
        jqXHR: jqXHR,
        textStatus: textStatus,
        errorThrown: errorThrown
    };
    if (textStatus !== "timeout" && textStatus !== "" && jqXHR.readyState !== 0)
        sendData("logError", option);
}
function loadPostResult(response, statusText, target) {
    var htmlstr = "", toggleId = "";
    if (!response.error) {
        $.each(response, function(i, response) {
            htmlstr += '<div class="index-search-result"><h3>' +
                    '<a href="user/'+response.username+'"><img class="post-profile-pic" src="' + (response.photo.nophoto ? response.photo.alt : response.photo.thumbnail45) + '">' + response.firstname.concat(' ', response.lastname) + '</a>' +
                    '<div class="float-right">' +
//                    '<span class="post-time"><span class="icon-16-share"></span>24</span>' +
                    '<span class="post-time"><span class="icon-16-clock"></span><span class="timeago" title="' + response.time + '">' + response.time + '</span></span>' +
                    '&nbsp;<span class="post-time"><span class="icon-16-comment"></span>' + response.numComnt + '</span> ' +
                    '</div>' +
                    '<div class="clear"></div><hr></h3><p class="post-content">' + (response.post.length > 200 ? nl2br(linkify(response.post.substring(0, 200))) + '<span style="display:none" id="continuereading-' + response.id + '">' + nl2br(linkify(response.post.substring(200))) + '</span> <a id="continue-' + response.id + '">continue reading...</a>' : nl2br(linkify(response.post))) + '</p>';
            if (response.post_photo) {
                $.each(response.post_photo, function(k, photo) {
                    htmlstr += '<a class="fancybox" rel="gallery' + response.id + '"  href="' + photo.original + '" rel="group"><img src="' + photo.thumbnail + '"></a>';
                });
            }
            htmlstr += '</div>';
            if (response.post.length > 200) {
                if (toggleId !== "") {
                    toggleId += ",";
                }
                toggleId += "#continue-" + response.id;
            }
        });
        htmlstr += '<!--<div class="index-search-result">--><p><a href="index-search-results.php?g=' + target.term + '&s=post">see more Post results on "' + target.term + '"</a></p><!--</div>-->';
        $(target.target).html(htmlstr);
        $(".timeago").timeago();
        $(".fancybox").fancybox({
            openEffect: 'none',
            closeEffect: 'none'

        });
        if (toggleId !== "")
            $(toggleId).click(function() {
                showOption(this);
            });
    } else {
        htmlstr += '<div class="index-search-result"><p class="post-content">No result</p></div>';
        $(target.target).html(htmlstr);
    }
}
function loadCommunityResult(response, statusText, target) {
    var htmlstr = "", toggleId = "";
    if (!response.error) {
        $.each(response, function(i, response) {
            if (response.unique_name) {
                htmlstr += '<div class="index-search-result">' +
                        '<h3><a href="' + response.unique_name + '"><img class="post-profile-pic" src="' + response.thumbnail150 + '">' + response.name +(response.verified === "1" ? ' <img src="images/gossout-verified.png" class="verified-community" style="border:none;margin:0px;" title="Verified Community">' : "")+ '</a></h3>' +
                        '<hr><p>' + (response.description.length > 250 ? (linkify(response.description.substring(0, 250))) + "..." : response.description) + '</p><hr>' +
                        '<a><span class="icon-16-dot"></span>' + response.post_count + ' ' + (response.post_count > 1 ? "Posts" : "Post") + '</a>' +
                        '<a><span class="icon-16-dot"></span>' + response.mem_count + ' ' + (response.mem_count > 1 ? "Members" : "Member") + '</a>' +
                        '</div>';
                if (response.description.length > 200) {
                    toggleId += "#continue-" + response.id;
                }
            }
        });
        htmlstr += '<!--<div class="index-search-result">--><p><a href="index-search-results.php?g=' + target.term + '&s=com">see more Community results on "' + target.term + '"</a></p><!--</div>-->';

        $(target.target).html(htmlstr);
        $(".timeago").timeago();
        $(".fancybox").fancybox({
            openEffect: 'none',
            closeEffect: 'none'

        });
        if (toggleId !== "")
            $(toggleId).click(function() {
                showOption(this);
            });
    } else {
        htmlstr += '<div class="index-search-result"><p class="post-content">No result</p></div>';
        $(target.target).html(htmlstr);
    }
}
function loadPeopleResult(response, statusText, target) {
    var htmlstr = "", toggleId = "";
    if (!response.error) {
        $.each(response, function(i, response) {
            htmlstr += '<div class="index-search-result">' +
                    '<h3><a href="user/'+response.username+'">' + response.firstname.concat(' ', response.lastname) + '</a></h3>' +
                    '<img class="float-left" src="' + (response.photo.nophoto ? response.photo.alt : response.photo.thumbnail150) + '">' +
                    '<p> <span class="icon-16-location"></span>' + (response.location === "" ? "Location not set" : response.location) + '</p>' +
                    '<p> <span class="icon-16-calendar"></span>Joined on ' + response.dateJoined + '</p>' +
                    '<p> <span class="icon-16-female"></span>' + (response.gender === "M" ? "Male" : "Female") + '</p>' +
                    '<div class="clear"></div></div>';
//            if (response.description.length > 200) {
//                toggleId += "#continue-" + response.id;
//            }
        });
        htmlstr += '<!--<div class="index-search-result">--><p><a href="index-search-results.php?g=' + target.term + '&s=people">see more People results on "' + target.term + '"</a></p><!--</div>-->';
        $(target.target).html(htmlstr);
        $(".timeago").timeago();
        $(".fancybox").fancybox({
            openEffect: 'none',
            closeEffect: 'none'

        });
        if (toggleId !== "")
            $(toggleId).click(function() {
                showOption(this);
            });
    } else {
        htmlstr += '<div class="index-search-result"><p class="post-content">No result</p></div>';
        $(target.target).html(htmlstr);
    }
}
function showOption(obj) {
    var option;
    if ((obj.id).indexOf("continue-") >= 0) {
        var postIdPos = (obj.id).lastIndexOf("-") + 1;
        var postId = ((obj.id).substring(postIdPos));
        if ($("#" + obj.id).html() === "continue reading...") {
            $("#" + obj.id).html("show less");
        } else {
            $("#" + obj.id).html("continue reading...");
        }
        $("#continuereading-" + postId).toggle();
    }
}
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0)
            return c.substring(nameEQ.length, c.length);
    }
    return 0;
}
function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
function linkify(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText;
}