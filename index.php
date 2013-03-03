<?php

/**
 * This sample app is provided to kickstart your experience using Facebook's
 * resources for developers.  This sample app provides examples of several
 * key concepts, including authentication, the Graph API, and FQL (Facebook
 * Query Language). Please visit the docs at 'developers.facebook.com/docs'
 * to learn more about the resources available to you
 */

//2012-01-13 banz-ghb start keep session in IE
if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {
	header('p3p: CP="ALL DSP COR PSAa PSDa OUR NOR ONL UNI COM NAV"');
}
//2012-01-13 banz-ghb end   keep session in IE

// Provides access to app specific values such as your app id and app secret.
// Defined in 'AppInfo.php'
require_once('AppInfo.php');

// Enforce https on production
if (substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
  header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');


/*****************************************************************************
 *
 * The content below provides examples of how to fetch Facebook data using the
 * Graph API and FQL.  It uses the helper functions defined in 'utils.php' to
 * do so.  You should change this section so that it prepares all of the
 * information that you want to display to the user.
 *
 ****************************************************************************/

require_once('sdk/src/facebook.php');

$facebook = new Facebook(array(
  'appId'  => AppInfo::appID(),
  'secret' => AppInfo::appSecret(),
  'sharedSession' => true,
  'trustForwarded' => true,
));

// Fetch the basic info of the app that they are using
$app_info = $facebook->api('/'. AppInfo::appID());

$app_name = idx($app_info, 'name', '');

?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

    <title><?php echo he($app_name); ?></title>
    <link rel="stylesheet" href="stylesheets/screen.css" media="Screen" type="text/css" />
    <link rel="stylesheet" href="stylesheets/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" type="text/css" />

    <!--[if IEMobile]>
    <link rel="stylesheet" href="mobile.css" media="screen" type="text/css"  />
    <![endif]-->

    <!-- These are Open Graph tags.  They add meta data to your  -->
    <!-- site that facebook uses when your content is shared     -->
    <!-- over facebook.  You should fill these tags in with      -->
    <!-- your data.  To learn more about Open Graph, visit       -->
    <!-- 'https://developers.facebook.com/docs/opengraph/'       -->
    <meta property="og:title" content="<?php echo he($app_name); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo AppInfo::getUrl(); ?>" />
    <meta property="og:image" content="<?php echo AppInfo::getUrl('/logo.png'); ?>" />
    <meta property="og:site_name" content="<?php echo he($app_name); ?>" />
    <meta property="og:description" content="lislog" />
    <meta property="fb:app_id" content="<?php echo AppInfo::appID(); ?>" />

    <script type="text/javascript" src="/javascript/jquery-1.7.1.min.js"></script>

    <script type="text/javascript">
      ///// Utility
      function logResponse(response) {
        if (console && console.log) {
          console.log('The response was', response);
        }
      }

      $(function(){ //define function 1 start
        // Set up so we handle click on the buttons

        $('#sendRequest').click(function() {
          FB.ui(
            {
              method  : 'apprequests',
              message : $(this).attr('data-message')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });

        var radio_programs = ["baka", "bakusho", "fumou", "megane", "banana", "elekata"];

      //for each (var var_radio_program in radio_programs){ //loop 1 start
        for (var i = 0; i < radio_programs.length; i++){
          var_radio_program_button_name = '#publishAction_'+radio_programs[i];
          //var_radio_program_button_url  = 'https://lislog.herokuapp.com/radio/jp/co/tbs/'+radio_programs[i]+'.html';
          $(this).find
          $(var_radio_program_button_name).click(function() { //bind function 10 start
            //$(this).find("a").attr("href")
            //FB.api('/me/lislogapp:tune_in','POST',{radio_program:var_radio_program_button_url},//FB.api 1
            var var_radio_program_button_url =
              'https://lislog.herokuapp.com/radio/jp/co/tbs/'+$(this).attr("id").replace("publishAction_","")+'.html';
            FB.api('/me/lislogapp:tune_in','POST',{radio_program:var_radio_program_button_url},//FB.api 1
              function (response) {
                $("#most-recent-activity").show();// 2013-03-02 banz-ghb hide most-recent-activity when logged out
                if (response != null) { //if start
                  logResponse(response);
                  FB.api('/me/lislogapp:tune_in','GET',{limit:4}, //FB.api 2
                    function (response2) {
                      updateMostRecentActivity(response2.data);
                      addRowToBottom(response2.data);
                    }
                  ); //FB.api 2
                  $("#samples").show();// 2013-03-02 banz-ghb hide samples when logged out
                } //if end
              }
            ); //FB.api 1
          });  //bind function 10 end
        } //loop 1 end
        // end
      }); //define function 1 end

    /////////////////////////
      //View functions
      function updateMostRecentActivity(array_activities) {
        for(i = 0; i < 1; i++) {
          $('#most-recent-activity-title').text(array_activities[i].data.radio_program.title);
          //Chrome can't parse iso format
          //hint (use jquery wrapper)
          //http://docs.jquery.com/UI/Datepicker/parseDate
          //new Date().toLocaleString();
          //$('#most-recent-activity-publish_time').text(new Date().slice(0,-5).toLocaleString(array_activities[i].publish_time));
          //$('#most-recent-activity-publish_time').text(new Date().toLocaleString(array_activities[i].publish_time));
          $('#most-recent-activity-publish_time').text(array_activities[i].publish_time);
        }
      }
      //http://d.hatena.ne.jp/okahiro_p/20120525/1337918243
      function addRowToBottom(array_activities) {
        $('#recent-activities li').remove();

        //array_activities[i].publish_time
        for(i = 0; i < array_activities.length; i++) {
          var li = $('<li/>').text(array_activities[i].publish_time); //.appendTo(tr);
          $('#recent-activities').append(li);
        }
      }

      function getAppUsingFriends() {
        FB.api({ //FB.api
          method : 'fql.query',
          query  : 'SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1'
        },function(response111){
          $('#app-using-friends li').remove();

          for(i = 0; i < response111.length; i++) {
            logResponse(response111[i]);//alert(response111[i].name);
            var li = $('<li/>').text(response111[i].name);
            $('#app-using-friends').append(li);
            var a = $('<a/>').text(response111[i].name);
            a.attr("href", "https://www.facebook.com/"+response111[i].uid);//a is added attributes
            a.attr("target", "_top");//a is added attributes
            li.append(a);
            var img = $('<img/>');
            a.append(img);
            img.attr("src", "https://graph.facebook.com/"+response111[i].uid+"/picture?type=square");//img is added attributes
            img.attr("alt", response111[0].name);//img is added attributes
          }
        }); //FB.api
      }
      </script>

    <!--[if IE]>
      <script type="text/javascript">
        var tags = ['header', 'section'];
        while(tags.length)
          document.createElement(tags.pop());
      </script>
    <![endif]-->
  </head>
  <body>
    <div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo AppInfo::appID(); ?>', // App ID
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });

        // Listen to the auth.login which will be called when the user logs in
        // using the Login button
        FB.Event.subscribe('auth.login', function(response) {
          // We want to reload the page now so PHP can read the cookie that the
          // Javascript SDK sat. But we don't want to use
          // window.location.reload() because if this is in a canvas there was a
          // post made to this page and a reload will trigger a message to the
          // user asking if they want to send data again.
          window.location = window.location;
        });

        // 2013-02-24 banz-ghb start add event subscribe event function
        function function_eventStateChangeOnLislog(response3){ //start response3
          if (response3.status == "connected") {
            logResponse(response3);//alert("login");
            $("#fb-auth").hide();
            $("#picture").show();    // 2013-02-24 banz-ghb switch lislog-main
            $("#lislog-main").show();// 2013-02-24 banz-ghb switch lislog-main
            FB.api('/me/lislogapp:tune_in','GET',{limit:4}, //FB.api 31
              function (response31) {
                updateMostRecentActivity(response31.data);
                addRowToBottom(response31.data);
                getAppUsingFriends();// 2013-03-02 banz-ghb add getAppUsingFriends
              }
            ); //FB.api 31
            // 2013-02-24 banz-ghb start update profile picture
            $('#picture').attr("style", "background-image: url(https://graph.facebook.com/"+response3.authResponse.userID+"/picture?type=normal)");
            // 2013-02-24 banz-ghb end   update profile picture
          } else {
            logResponse(response3);//alert("not login");

            $('#fb-auth').show();
            $("#picture").hide();    // 2013-02-24 banz-ghb switch lislog-main
            $("#lislog-main").hide();// 2013-02-24 banz-ghb switch lislog-main
          } //if end
          $("#most-recent-activity").hide();// 2013-03-02 banz-ghb hide most-recent-activity when logged out
          $("#samples").hide();// 2013-03-02 banz-ghb hide samples when logged out
} //end response3

        FB.getLoginStatus(function_eventStateChangeOnLislog);
        FB.Event.subscribe('auth.statusChange', function_eventStateChangeOnLislog);
        // 2013-02-24 banz-ghb end   add event subscribe event function

        FB.Canvas.setAutoGrow();
      };

      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>

    <header class="clearfix">
      <p id="picture"></p>

      <div id="lislog-main"><!-- 2013-02-24 banz-ghb switch lislog-main -->
        <h1>Welcome to <strong><?php echo he($app_name); ?></strong></h1>

        <div id="share-app">
          <p>Press button when you tune in:</p><br>
          <ul>
            <!-- start -->
            <li>
              <a href="#" class="facebook-button" id="publishAction_baka" data-url="<?php echo AppInfo::getUrl(); ?>">
                <span class="plus">バカ</span>
              </a>
            </li>
                      <li>
              <a href="#" class="facebook-button" id="publishAction_bakusho" data-url="<?php echo AppInfo::getUrl(); ?>">
                <span class="plus">爆笑</span>
              </a>
            </li>
            <li>
              <a href="#" class="facebook-button" id="publishAction_fumou" data-url="<?php echo AppInfo::getUrl(); ?>">
                <span class="plus">不毛な議論</span>
              </a>
            </li>
            <li>
            <a href="#" class="facebook-button" id="publishAction_megane" data-url="<?php echo AppInfo::getUrl(); ?>">
                <span class="plus">メガネ</span>
              </a>
            </li>
            <li>
              <a href="#" class="facebook-button" id="publishAction_banana" data-url="<?php echo AppInfo::getUrl(); ?>">
                <span class="plus">バナナムーンゴールド</span>
              </a>
            </li>
            <li>
            <a href="#" class="facebook-button" id="publishAction_elekata" data-url="<?php echo AppInfo::getUrl(); ?>">
                <span class="plus">エレ片のコント太郎</span>
              </a>
            </li>
            <!-- end   -->
          </ul>
        </div>

      </div>
      <!-- Refer to https://developers.facebook.com/docs/reference/plugins/login/ -->
      <!-- 2013-03-03 banz-ghb start no extended permission when logging in  data-scope="user_likes,user_photos,publish_actions" -->
      <div id="fb-auth" class="fb-login-button" data-show-faces="true"></div>
      <!--div id="fb-auth" class="fb-login-button" data-scope="user_likes,user_photos,publish_actions"--><!--/div-->
      <!-- 2013-03-03 banz-ghb end   no extended permission when logging in-->
    </header>

    <!-- 2013-02-11 banz-ghb start -->
    <section id="most-recent-activity" class="clearfix">
      <div>
        <h1>Most Recent Activity:</h1>
        <p id="most-recent-activity-title">Empty</p><p>Your ranking: Heavy Listener (or Listner)</p>
        <p id="most-recent-activity-publish_time">Empty</p>
        </div>
    </section>
    <!-- 2013-02-11 banz-ghb end -->
    <section id="get-started">
      <p>Guide</p>
      <a href="https://lislog.heroku.com/guide.html" target="_blank" class="button">Learn How to use lislog</a>
    </section>

    <section id="samples" class="clearfix">
      <h1>Social Graph</h1>

      <div class="list">
        <h3>Recent activities</h3>
        <ul id="recent-activities" class="things">
          <li>
            <a>
              <span>empty</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="list">
        <h3>Friends using this app</h3>
        <ul id="app-using-friends" class="friends">
          <!-- app_using_friends --><!-- 2013-03-02 banz-ghb disable to show app_using_friends -->
        </ul>
      </div>

      <div class="list">
        <h3>Send Request</h3>
        <ul class="things">
          <li>
            <a href="#" class="facebook-button apprequests" id="sendRequest" data-message="Test this awesome app">
              <span class="apprequests">Send Requests</span>
            </a>
          </li>
        </ul>
      </div>
    </section>

    <section id="guides" class="clearfix">
      <h1>Check Your Facebook Timeline</h1>
      <ul>
        <li>
          <!-- response.username -->
          <!-- https://www.facebook.com/akinori.kohno.5/allactivity?privacy_source=activity_log&log_filter=app_554694347877002 -->
          <!-- https://www.facebook.com/${RESPONSE.USERNAME}/allactivity?privacy_source=activity_log&log_filter=app_${APPID} -->
          <!-- a href="https://www.heroku.com/?utm_source=facebook&utm_medium=app&utm_campaign=fb_integration" target="_top" class="icon apps-on-facebook"--><!-- Timeline --><!-- /a -->
          <!-- for PC     https://www.facebook.com/me/app_lislogapp -->
          <!-- for Mobile https://www.facebook.com/me -->
          <a href="https://www.facebook.com/me" target="_top" class="icon apps-on-facebook">Timeline</a>
          <p>View the activity logs of lislog in your facebook timeline.</p>
        </li>
      </ul>
    </section>
  </body>
</html>
