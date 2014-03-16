if (jQuery("#fb-root").length == 0)
{
    var joomFbSdk = '<div id="fb-root"></div>';
    jQuery("body").append(joomFbSdk);
}

window.fbAsyncInit = function() {
    FB.init({
        appId      : fbAppId,
        status     : true,
        xfbml      : true
    });

  jQuery('.facebook-login').click(function(event) {
    event.preventDefault();

    FB.login(function(response) {
        if (response.authResponse) 
        {
            var accessToken = response.authResponse.accessToken;
            FB.api('/me', function(response) {
                window.location.href=document.URL + "?accessToken=" + accessToken + "&type=facebook";
            });
        } 
        else
        {
            //User cancelled login or did not fully authorize.
        }
    }, {
        scope: 'email, user_birthday, user_about_me, user_likes, publish_actions'
    });
  });
};

(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/all.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));