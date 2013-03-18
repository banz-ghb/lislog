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
//2012-03-16 banz-ghb start get locales
//ja,en-US;q=0.8,en;q=0.6
$locales = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
$client_language = 'en';
$client_locale   = 'en_US';
foreach ($locales as $locale){
	if (preg_match('/^ja/i',$locale)) {
		$client_language = 'ja';
		$client_locale   = 'ja_JP';
		break;
	}
}
//2012-03-16 banz-ghb end   get locales

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
<!--2012-03-16 banz-ghb start get locales-->
<html xmlns:fb="http://ogp.me/ns/fb#" lang="<?php echo he($client_language)?>">
<!--2012-03-16 banz-ghb end   get locales-->
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

    <script type="text/javascript"><!--
      // 2013-03-18 declare global var
      navigationheightvalue = 40; //need to syncronize #navigation height
      //////////////////////////////////////////////////////////////
      // Utility
      //////////////////////////////////////////////////////////////
      // TODO: control debug mode by environment variant
      function logResponse(response) {
        if (console && console.log) {
          console.log('The response was', response);
        }
      }

      //////////////////////////////////////////////////////////////
      // Build html elements start
      //////////////////////////////////////////////////////////////
      $(function(){ //define function start1 start
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

        $('#fb-auth').click(function() {
          FB.login(function(response) {
              // handle the response
           }, {scope: 'user_likes,user_photos,publish_actions'});
           /*
           FB.ui({method:    'permissions.request',
                  client_id: <?php echo AppInfo::appID(); ?>,
                  display:   'touch',
                  perms:     'user_likes,user_photos,publish_actions'}
            ,function (response){
              logResponse(response);
           });
           */
         });

        var radio_programs_id = ["baka", "bakusho", "fumou", "megane", "banana", "elekata"];
        var radio_programs_title = [ "伊集院光　深夜の馬鹿力"
                                    ,"爆笑問題カーボーイ"
                                    ,"山里亮太の不毛な議論"
                                    ,"おぎやはぎのメガネびいき"
                                    ,"バナナマンのバナナムーンGOLD"
                                    ,"エレ片のコント太郎"
                                   ];

        // 2013-03-03 banz-ghb start #7 generate lislog buttons dynamically
        $('#radioprogram-list li').remove();
        // 2013-03-03 banz-ghb end   #7 generate lislog buttons dynamically

        for (var i = 0; i < radio_programs_id.length; i++){ //loop 1 start
          addPublishActionButtonOnLiElement(radio_programs_id[i], radio_programs_title[i]);
        } //loop 1 end

        //lislog-main           ->                   menu-lislog-main
        //*most-recent-activity ->id-shared-activity menu-most-recent-activity
        //*samples              ->about-us
        //*get-started          ->          menu-get-started
        $("#menu-get-started").click(function(){ //menu function 1 start
        //function testclick(){
          //alert(test);
          //http://stackoverflow.com/questions/7193425/how-do-you-animate-fb-canvas-scrollto?answertab=active#tab-top
          $('html,body').animate(
            {scrollTop: $("#get-started").offset().top - navigationheightvalue},
            {duration: 1000, step: function(top_offset){
              FB.Canvas.scrollTo(0, top_offset);
            }
          });
          return false;
          //alert("debug2: "+top_offset);
        }); //menu function 1 end
        // 2013-03-17 banz-ghb start add other navigations
        $("#menu-lislog-main").click(function(){ //menu function 1-2 start
          $('html,body').animate(
            {scrollTop: 0},
            {duration: 1000, step: function(top_offset){
              FB.Canvas.scrollTo(0, top_offset );
            }
          });
          return false;
        }); //menu function 1-2 end
        //2013-03-17 banz-ghb remove most-recent-activity
        $("#menu-shared-activity").click(function(){ //menu function 1-3 start
          $('html,body').animate(
            {scrollTop: $("#id-shared-activity").offset().top - navigationheightvalue},
            {duration: 1000, step: function(top_offset){
              FB.Canvas.scrollTo(0, top_offset);
            }
          });
          return false;
        }); //menu function 1-3 end
        // 2013-03-17 banz-ghb end   add other navigations

      }); //define function start1-2 end
      //////////////////////////////////////////////////////////////
      // Build html elements end
      //////////////////////////////////////////////////////////////

      //////////////////////////////////////////////////////////////
      //View functions
      //////////////////////////////////////////////////////////////
      // 2013-03-17 banz-ghb start remove most-recent-activity
      /*
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
      */
      // 2013-03-17 banz-ghb end   remove most-recent-activity

      //http://d.hatena.ne.jp/okahiro_p/20120525/1337918243
      function addRowToBottom(array_activities) {
        $('#recent-activities li').remove();

        //array_activities[i].publish_time
        for(i = 0; i < array_activities.length; i++) {
          var li = $('<li/>').text(array_activities[i].publish_time); //.appendTo(tr);
          $('#recent-activities').append(li);
        }
      }

      // 2013-03-16 banz-ghb start delete candidate instead of facepile
      /*
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
      */
      // 2013-03-16 banz-ghb end   delete candidate instead of facepile

      function addPublishActionButtonOnLiElement(var_radio_program_id, var_radio_program_title) {
          // 2013-03-17 banz-ghb start add facepile
          var var_radio_program_button_name = 'publishAction_'+var_radio_program_id; //radio_programs_id[i]
          //clickバインドないと合わせること
          var var_radio_program_button_url_1 =
              'https://lislog.herokuapp.com/radio/jp/co/tbs/'+var_radio_program_id+'.html';
          // 2013-03-17 banz-ghb end   add facepile
          //configure a li element
          var li = $('<li/>');
          $('#radioprogram-list').append(li);
          //configure an a element
          var a = $('<a/>');
          li.append(a);
          a.attr("href", "#");
          a.attr("class", "facebook-button");
          a.attr("id", var_radio_program_button_name);
          a.attr("data-url", "<?php echo AppInfo::getUrl(); ?>");
          a.click(function() { //bind function 10 start
            var var_radio_program_button_url =
              'https://lislog.herokuapp.com/radio/jp/co/tbs/'+$(this).attr("id").replace("publishAction_","")+'.html';
            //2013-03-18 banz-ghb start change listen action
              FB.api('/me/lislogapp:tune_in','POST',{radio_program:var_radio_program_button_url},//FB.api 1
            //FB.api('me/music.listens','POST',{radio_station:var_radio_program_button_url},//FB.api 1
            //2013-03-18 banz-ghb end   change listen action
              function (response) {
                //2013-03-17 banz-ghb remove most-recent-activity
                $("#id-shared-activity-div").show();// 2013-03-02 banz-ghb hide most-recent-activity when logged out
                // 2013-03-18 banz-ghb start realtime update
                var container = document.getElementById('id-shared-activity');
                FB.XFBML.parse(container);
                // 2013-03-18 banz-ghb end   realtime update
                if (response != null) { //if start
                  logResponse(response);
                  //2013-03-16 banz-ghb start delete candidate
                  /*
                  //FB.api('/me/lislogapp:tune_in','GET',{limit:4}, //FB.api 2
                    FB.api('/me/music.listens',    'GET',{limit:4}, //FB.api 2
                      function (response2) {
                        logResponse(response2);
                        updateMostRecentActivity(response2.data);
                        addRowToBottom(response2.data);
                    }); //FB.api 2
                  */
                  //2013-03-16 banz-ghb end   delete candidate
                  $("#samples").show();// 2013-03-02 banz-ghb hide samples when logged out

                  //2013-03-18 banz-ghb start start scroll
                  //http://stackoverflow.com/questions/7193425/how-do-you-animate-fb-canvas-scrollto?answertab=active#tab-top
                  $('html,body').animate(
                    //2013-03-17 banz-ghb remove most-recent-activity
                    {scrollTop: $("#id-shared-activity").offset().top - navigationheightvalue},
                    {duration: 500, step: function(top_offset){
                      FB.Canvas.scrollTo(0, top_offset);
                    }
                  });
                  //update shared activity by xfbml
                  //2013-03-18 banz-ghb end   start scroll
                } //if end
                return false;
              }
            ); //FB.api 1
          });  //bind function 10 end
          //configure a span element
          var span = $('<span/>').text(var_radio_program_title); //radio_programs_title[i]
          a.append(span);
          span.attr("class", "plus");
          // TODO implement facepile
          /*
          li.append(div);
          div ->attr  "class"         "fb-facepile"
              ->attr  "data-href"     "https://lislog.herokuapp.com/radio/jp/co/tbs/baka.html"
                                      ->var_radio_program_button_url
              ->attr  "data-action"   "music.listens"
              ->attr  "data-max-rows" "1"

          <div class="fb-facepile"
            data-href="https://lislog.herokuapp.com/radio/jp/co/tbs/baka.html"
            data-action="music.listens"
            data-max-rows="1">
           </div>
          */
          // 2013-03-17 banz-ghb start add facepile
          var div = $('<div/>');
          li.append(div);
          div.attr("class",         "fb-facepile");
          div.attr("data-href",     var_radio_program_button_url_1);
          div.attr("data-action",   "lislogapp:tune_in");//2013-03-18
        //div.attr("data-action",   "music.listens");
          div.attr("data-max-rows", "1");
          div.attr("data-width",    "270"); //adjust layout
          // 2013-03-17 banz-ghb end   add facepile

      }
      //-->
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
    <!-- 2013-03-09 banz-ghb start header -->
    <div id="navigation"><!-- style="height:50px;border:1px solid blue;" -->
      <div id="navigation_top" class="clearfix">
        <ul>
          <li><a id="menu-lislog-main" class="first"  > トップ</a></li>
          <li><a id="menu-get-started"    > | ガイド</a></li>
          <li><a id="menu-shared-activity"> | アクティビティログ</a></li>
          </ul>
      </div>
    </div>
    <!-- 2013-03-09 banz-ghb end   header -->
  <header class="clearfix">
    <p id="picture"></p>

      <div id="lislog-main"><!-- 2013-02-24 banz-ghb switch lislog-main -->
        <h1><fb:intl desc="Welcome message">
          <fb:intl-token name="welcome-message">
          Welcome to
          </fb:intl-token>
          </fb:intl> <strong><?php echo he($app_name); ?></strong></h1>

        <div id="share-app" class="clearfix">

        </div>

        <!-- 2013-03-17 banz-ghb move radioprogram-list -->
      </div>

    <!-- 2012-03-06 banz-ghb start change layout -->
    <div class="clearfix">
      <p>Press button when you tune in:</p><br>
      <ul id="radioprogram-list">
        <!-- start temporarilly removed -->
        <!-- end   temporarilly removed -->
      </ul>
    </div>
    <!-- 2012-03-06 banz-ghb end   change layout -->

      <!-- 2013-03-03 banz-ghb start no extended permission when logging in  data-scope="user_likes,user_photos,publish_actions" -->
      <!-- Refer to https://developers.facebook.com/docs/reference/plugins/login/ -->
      <div id="fb-login" class="fb-login-button" data-scope="user_likes,user_photos,publish_actions" data-show-faces="true"></div>
      <div id="fb-auth" >アプリを承認する</div>
      <!--div id="fb-auth" class="fb-login-button" data-scope="user_likes,user_photos,publish_actions"--><!--/div-->
      <!-- 2013-03-03 banz-ghb end   no extended permission when logging in-->
    </header>

    <!-- 2013-03-17 banz-ghb remove most-recent-activity -->

    <!-- 2013-03-03 banz-ghb start change location of get-started -->
    <section id="get-started">
      <p>Guide</p>
      <a href="https://lislog.heroku.com/guide.html" target="_blank" class="button">Learn How to use lislog</a>
    </section>
    <!-- 2013-03-03 banz-ghb end   change location of get-started -->

    <!-- 2013-03-17 banz-ghb start move samples -->
    <section id="id-shared-activity" class="clearfix">
      <!-- adjust layout -- data-width="300" data-height="300" -->
      <!-- 2013-03-18 banz-ghb start realtime update -->
      <!-- FB.XFBML.parse(); -->
      <fb:shared-activity id="id-shared-activity-div"></fb:shared-activity>
      <!--div id="id-shared-activity-div" class="fb-shared-activity" --><!--/div-->
      <!-- 2013-03-18 banz-ghb end   realtime update -->
    </section>
    <!-- 2013-03-17 banz-ghb end   move samples -->

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

    <!-- 2013-03-17 banz-ghb start move samples -->
    <section id="samples" class="clearfix"><a id="samples-a"></a>
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

      <!-- 2013-03-17 banz-ghb disable to show app_using_friends -->

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
    <!-- 2013-03-17 banz-ghb end   move samples -->

    <!-- initialize facebook javascript sdk -->
    <div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo AppInfo::appID(); ?>', // App ID
          // 2012-03-16 banz-ghb start get locales
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel_<?php he($client_locale);?>.html', // Channel File
          // 2012-03-16 banz-ghb start get locales
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });

        // Listen to the auth.login which will be called when the user logs in
        // using the Login button

        // 2013-03-14 banz-ghb start disable code for php
        //FB.Event.subscribe('auth.login', function_eventStateChangeOnLislog);
        FB.Event.subscribe('auth.login', function(response) {
          // We want to reload the page now so PHP can read the cookie that the
          // Javascript SDK sat. But we don't want to use
          // window.location.reload() because if this is in a canvas there was a
          // post made to this page and a reload will trigger a message to the
          // user asking if they want to send data again.
          // 2013-03-14 banz-ghb start disable code for php
            window.location = window.location; //pending
          // 2013-03-14 banz-ghb end   disable code for php
        });
        // 2013-03-14 banz-ghb start disable code for php

        // 2013-02-24 banz-ghb start add event subscribe event function
        function function_eventStateChangeOnLislog(response3){ //start response3
          if (response3.status == "connected") {
            logResponse(response3);//alert("login");
            $("#fb-login").hide();
            $("#fb-auth").hide();
            $("#picture").show();    // 2013-02-24 banz-ghb switch lislog-main
            $("#lislog-main").show();// 2013-02-24 banz-ghb switch lislog-main
            //2013-03-18 banz-ghb start change listen action
              FB.api('/me/lislogapp:tune_in','GET',{limit:4}, //FB.api 31
            //FB.api('/me/music.listens',    'GET',{limit:4}, //FB.api 31
            //2013-03-18 banz-ghb end   change listen action
              function (response31) {
            	//2013-03-17 banz-ghb remove most-recent-activity
                //updateMostRecentActivity(response31.data);
                addRowToBottom(response31.data);
                // 2013-03-16 banz-ghb start delete candidate instead of facepile
                //getAppUsingFriends();// 2013-03-17 banz-ghb disable to show app_using_friends
                // 2013-03-16 banz-ghb end   delete candidate instead of facepile
              }
            ); //FB.api 31
            // 2013-02-24 banz-ghb start update profile picture
            $('#picture').attr("style", "background-image: url(https://graph.facebook.com/"+response3.authResponse.userID+"/picture?type=normal)");
            // 2013-02-24 banz-ghb end   update profile picture
          } else if (response3.status == "not_authorized") {
            $("#fb-login").hide();
            $("#fb-auth").show();
            $("#picture").show();    // 2013-02-24 banz-ghb switch lislog-main
            $("#lislog-main").show();// 2013-02-24 banz-ghb switch lislog-main
          } else {
            logResponse(response3);//alert("not login");

            $('#fb-login').show();
            $("#fb-auth").hide();
            $("#picture").hide();    // 2013-02-24 banz-ghb switch lislog-main
            $("#lislog-main").hide();// 2013-02-24 banz-ghb switch lislog-main
            alert('login callback 1');
            // 2013-03-13 banz-ghb start add FB.login
            //FB.login(function(response){
            //  alert('login callback');
            //}
            // 2013-03-13 banz-ghb end   add FB.login
          } //if end
          ////2013-03-17 banz-ghb remove most-recent-activity
          //$("#most-recent-activity").hide();// 2013-03-02 banz-ghb hide most-recent-activity when logged out
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
        //2012-03-16 banz-ghb start get locales
        js.src = "//connect.facebook.net/<?php echo he($client_locale);?>/all.js";
        //2012-03-16 banz-ghb end   get locales
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
  </body>
</html>
