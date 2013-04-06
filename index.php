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
    <!-- 2013-03-30 anz-ghb start add activity indicator -->
    <!--script type="text/javascript" src="/javascript/jquery.activity-indicator-1.0.0.min.js"--><!--/script-->
    <script type="text/javascript" src="/javascript/jsgt_indicator003.js" charset="utf-8"></script>
    <!-- 2013-03-30 anz-ghb start add activity indicator -->

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
               method  : 'apprequests'
              ,message : $(this).attr('data-message')
            },
            function (response) {
              // If response is null the user canceled the dialog
              if (response != null) {
                logResponse(response);
              }
            }
          );
        });

        $('#fb-login').click(function() {
          FB.login(function(response) {
              // handle the response
          }, {scope: 'user_likes,user_photos,publish_actions'});
        });

        //Under normal circumstances you should attach this FB.login() call to a Javascript onClick event as the call results in a popup window being opened, which will be blocked by most browsers.
        //https://developers.facebook.com/docs/howtos/login/getting-started/#step4
        //function login() {
        //  FB.login(function(response) {
        //    if (response.authResponse) {
        //      // connected
        //    } else {
        //      // cancelled
        //    }
        //  });
        //}

        $('#fb-auth').click(function() {

          FB.login(function(response) {
              // handle the response
           }, {scope: 'user_likes,user_photos,publish_actions'});

//          2500
//          An active access token must be used to query information about the current user.
//          FB.ui({method:    'permissions.request',
//                  client_id: <?php echo AppInfo::appID(); ?>,
//                  display:   'touch',
//                  perms:     'user_likes,user_photos,publish_actions'}
//                ,function (response){
//                   logResponse(response);
//                   if (response && response.perms) {
//                     //function_eventStateChangeOnLislog(response);
//                     //top.location.href = 'https://lislog.herokuapp.com';
//                       $("#fb-login").hide();
//                       $("#fb-auth").hide();
//                       $("#picture").show();    // 2013-02-24 banz-ghb switch lislog-main
//                       $("#lislog-main").show();// 2013-02-24 banz-ghb switch lislog-main
//                     //$("#id-shared-activity").show();
//                       $('#picture').attr("style", "background-image: url(https://graph.facebook.com/"+response.selected_profiles+"/picture?type=normal)");
//                   } else {
//                       $("#fb-login").hide();
//                       $("#fb-auth").show();
//                       $("#picture").show();    // 2013-02-24 banz-ghb switch lislog-main
//                       $("#lislog-main").show();// 2013-02-24 banz-ghb switch lislog-main
//                     //$("#id-shared-activity").hide();
//                   }
//                }
 //         );

         });

        var radio_programs_id = ["baka", "bakusho", "fumou", "megane", "banana", "elekata"];
        var radio_programs_title = [ "Ijuin Shinya no baka chikara"
                                     ,"Bakusho mondai Cowboy"
                                     ,"Yamazato Fumou na giron"
                                     ,"Ogi Yahagi Megane biiki"
                                     ,"Bananaman Banana moon GOLD"
                                     ,"Elekata Konto Taro"
                                    ];
         var radio_programs_detail = [ "TBS radio Mon 25am start"
                                      ,"TBS radio Tue 25am start"
                                      ,"TBS radio Wed 25am start"
                                      ,"TBS radio Thu 25am start"
                                      ,"TBS radio Fri 25am start"
                                      ,"TBS radio Sat 25am start"
                                     ];
        /*
        var radio_programs_title = [ "伊集院光　深夜の馬鹿力"
                                    ,"爆笑問題カーボーイ"
                                    ,"山里亮太の不毛な議論"
                                    ,"おぎやはぎのメガネびいき"
                                    ,"バナナマンのバナナムーンGOLD"
                                    ,"エレ片のコント太郎"
                                   ];
        var radio_programs_detail = [ "TBSラジオ 月曜25時～"
                                     ,"TBSラジオ 火曜25時～"
                                     ,"TBSラジオ 水曜25時～"
                                     ,"TBSラジオ 木曜25時～"
                                     ,"TBSラジオ 金曜25時～"
                                     ,"TBSラジオ 土曜25時～"
                                    ];
        */

        $('#radioprogram-list li').remove();

        for (var i = 0; i < radio_programs_id.length; i++){ //loop 1 start
          addPublishActionButtonOnLiElement(radio_programs_id[i]
                                           ,radio_programs_title[i]
                                           ,radio_programs_detail[i]
                                           );
          } //loop 1 end

        //lislog-main           ->                   menu-lislog-main
        //*most-recent-activity ->id-shared-activity menu-most-recent-activity
        //*samples              ->about-us
        //*get-started          ->          menu-get-started
        $("#menu-get-started").click(function(){ //menu function 1 start
          //http://stackoverflow.com/questions/7193425/how-do-you-animate-fb-canvas-scrollto?answertab=active#tab-top
          $('html,body').animate(
            {scrollTop: $("#get-started").offset().top - navigationheightvalue},
            {duration: 1000, step: function(top_offset){
              FB.Canvas.scrollTo(0, top_offset);
            }
          });
          return false;
        }); //menu function 1 end
        $("#menu-lislog-main").click(function(){ //menu function 1-2 start
          $('html,body').animate(
            {scrollTop: 0},
            {duration: 1000, step: function(top_offset){
              FB.Canvas.scrollTo(0, top_offset );
            }
          });
          return false;
        }); //menu function 1-2 end
        $("#menu-shared-activity").click(function(){ //menu function 1-3 start
          $('html,body').animate(
            {scrollTop: $("#id-shared-activity").offset().top - navigationheightvalue},
            {duration: 1000, step: function(top_offset){
              FB.Canvas.scrollTo(0, top_offset);
            }
          });
          return false;
        }); //menu function 1-3 end
        $("#menu-samples").click(function(){ //menu function 1-4 start
            $('html,body').animate(
              {scrollTop: $("#samples").offset().top - navigationheightvalue},
              {duration: 1000, step: function(top_offset){
                FB.Canvas.scrollTo(0, top_offset);
              }
            });
            return false;
        }); //menu function 1-4 end

      }); //define function start1-2 end
      //////////////////////////////////////////////////////////////
      // Build html elements end
      //////////////////////////////////////////////////////////////

      //////////////////////////////////////////////////////////////
      //View functions
      //////////////////////////////////////////////////////////////
      function addPublishActionButtonOnLiElement(var_radio_program_id
                                                ,var_radio_program_title
                                                ,var_radio_programs_detail) {//start addPublishActionButtonOnLiElement
          // 2013-03-17 banz-ghb start add facepile
          var var_radio_program_button_name = 'publishAction_'+var_radio_program_id; //radio_programs_id[i]
          //clickバインドないと合わせること
          var var_radio_program_button_url_1 =
              'https://lislog.herokuapp.com/radio/jp/co/tbs/'+var_radio_program_id+'.html';
          // 2013-03-17 banz-ghb end   add facepile
          //configure a li element
          var li = $('<li/>');
          $('#radioprogram-list').append(li);

          var p_title = $('<p/>');
          li.append(p_title);
          p_title.text(var_radio_program_title);
          p_title.attr("class", "radioprogram-title-style");// change font color and size
          var p_detail = $('<p/>');
          li.append(p_detail);
          p_detail.text(var_radio_programs_detail);

          var div = $('<div/>');
          li.append(div);
          div.attr("class",         "fb-facepile");
          div.attr("data-href",     var_radio_program_button_url_1);
          div.attr("data-action",   "lislogapp:tune_in");//2013-03-18
        //div.attr("data-action",   "music.listens");
          div.attr("data-max-rows", "1");
          div.attr("data-width",    "270"); //adjust layout

          //configure an a element
          var br1 = $('<br>');
          li.append(br1);
          // 2013-03-23 start

          /*var div2 = $('<div/>');
          li.append(div2);
          div2.text('test');
          div2.attr("margin", "0 auto");
          */
          /*
          var li2 = $('<li/>');
          li2.attr("class", "facebook-button-li2");
          $('#radioprogram-list').append(li2);
          */
          // 2013-03-23 end
          var a = $('<a/>');
          li.append(a);//li.append(a);// 2013-03-23
          a.attr("href", "#");
          a.attr("class", "facebook-button");
          a.attr("id", var_radio_program_button_name);
          a.click(function() { //bind function 10 start
            $('#'+$(this).attr("id").replace("publishAction_","")+'-div-date').text("Last time you tuned in: "/*"最後に聴いた日: "*/);
            $('#'+var_radio_program_id+'-div-date').addClass("indicator-start");
            $('#'+var_radio_program_id+'-div-date').removeClass("indicator-stop");
            $('#'+$(this).attr("id").replace("publishAction_","")+'-div-date').append('<img src="images/indicator.white.gif">')
            var var_radio_program_button_url =
              'https://lislog.herokuapp.com/radio/jp/co/tbs/'+$(this).attr("id").replace("publishAction_","")+'.html';
              FB.api('/me/lislogapp:tune_in','POST',{radio_program:var_radio_program_button_url},//FB.api 1
            //FB.api('me/music.listens','POST',{radio_station:var_radio_program_button_url},//FB.api 1
              function (response) {
                FB.api('/'+response.id,'GET'//FB.api 1-1
                 ,function (response1_1) {
                    //logResponse(response1_1.data.radio_program.url);
                    //http://c-brains.jp/blog/wsg/13/02/14-163725.php

                    var str = response1_1.data.radio_program.url;
                    str1 = str.replace(/\.html/,"");
                    str2 = str1.replace(/^.*\//,"");

                    var targetId = '#'+str2+'-date';
                    // 2013-03-27 banz-ghb start show local time
                    var var_utc_time   = response1_1.publish_time.replace(/\D/g," ");
                    var ary_utc_time   = var_utc_time.split(" ");
                    var var_utc_date = new Date(ary_utc_time[0]
                                               ,ary_utc_time[1]
                                               ,ary_utc_time[2]
                                               ,ary_utc_time[3]
                                               ,ary_utc_time[4]
                                               ,ary_utc_time[5]);
                    var var_utc_time_tmp = var_utc_date.getTime();
                    var var_offset = new Date().getTimezoneOffset();
                    var var_local_time = new Date();
                    /*var var_local_time = */var_local_time.setTime(var_utc_time_tmp - (var_offset * 1000 * 60));
                    //var var_local_time_format = var_local_time;
                    //var ary_local_time   = var_local_time_format.replace(/\D/g," ").split(" ");

                    //2013-03-31 banz-ghb start add activity indicator
                    $('#'+var_radio_program_id+'-div-date').addClass("indicator-stop");
                    $('#'+var_radio_program_id+'-div-date').removeClass("indicator-start");
                    $('#'+str2+'-div-date'+' img').remove();
                    $('#'+str2+'-div-date'/*targetId*/).text("Last time you tuned in: "//'最後に聴いた日: '
                                    +var_local_time.getFullYear() +'/'
                                    +var_local_time.getMonth()+'/'
                                    +var_local_time.getDate()+' '
                                    +var_local_time.getHours()+':'
                                    +var_local_time.getMinutes()
                                    );
                    //alert(response1_1.publish_time)
                    //// "publish_time": "2013-03-20T06:57:44+0000" response1_1.publish_time
                    //$(targetId).text(response1_1.publish_time)/*'test'*/;
                    // 2013-03-27 banz-ghb end   show local time
                });//FB.api 1-1
                var container = document.getElementById('id-shared-activity');
                FB.XFBML.parse(container);
                return false;
              }
            ); //FB.api 1
            return false; /* stop scroll code*/
          });  //bind function 10 end
          //configure a span element
          var span = $('<span/>').text("Tune in"/*リスなう*/); //radio_programs_title[i]
          a.append(span);
          span.attr("class", "plus");

          var br2 = $('<br>');
          li.append(br2);

          var div_date = $('<div/>');
          li.append(div_date);

          div_date.attr("id", var_radio_program_id+'-div-date');
          $('#'+var_radio_program_id+'-div-date').addClass("indicator-init");

          var p_date = $('<p/>');
          p_date.attr("id", var_radio_program_id+'-date');
          $('#'+var_radio_program_id+'-div-date').text("");

      } // end addPublishActionButtonOnLiElement
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
    <nav id="navigation" class="clearfix"><!-- style="height:50px;border:1px solid blue;" -->
          <div class="navigation-first" ><p id="menu-lislog-main"  >Top</p></div><!-- トップ -->
          <div class="navigation-div" ><p id="menu-get-started"    >Guide</p></div><!-- ガイド -->
          <div class="navigation-div"><p id="menu-shared-activity">Activity</p></div><!-- ログ -->
          <!-- menu-samples -->
    </nav>
    <!-- 2013-03-09 banz-ghb end   header -->
    <header class="clearfix">
      <p id="picture"></p>

        <div id="lislog-main">
          <h1><strong>Welcome to <?php echo he($app_name); ?><!--へようこそ--></strong></h1>
        </div>

        <!-- 2013-03-26 banz-ghb start move -->

      <!-- 2012-03-21 banz-ghb start move radioprogram-list -->
      <div class="clearfix">
        <p><!--お気に入りラジオを聴いたらリスなうボタンを押して下さい:-->Press button when you tune in</p><br>
      </div>
    </header>

    <!-- 2012-03-06 banz-ghb start move radioprogram-list -->
    <section id="section-radioprogram-list" class="clearfix">

      <!-- 2013-03-26 banz-ghb start move -->
      <!-- Refer to https://developers.facebook.com/docs/reference/plugins/login/ -->
      <!--div id="fb-login" class="fb-login-button" data-scope="user_likes,user_photos,publish_actions" data-show-faces="true"--><!--/div-->
      <div ><a id="fb-login" href="#" class="button">Login with Facebook<!--Facebookでログイン--></a></div>
      <div ><a id="fb-auth"  href="#" class="button">Authorize lislog<!--   アプリを承認する--></a   ></div>
      <!--div id="fb-auth" class="fb-login-button" data-scope="user_likes,user_photos,publish_actions"--><!--/div-->
      <!-- 2013-03-26 banz-ghb end   move -->
      <ul id="radioprogram-list">
        <!-- start temporarilly removed -->
        <!-- end   temporarilly removed -->
      </ul>
    </section>
    <!-- 2012-03-06 banz-ghb end   move radioprogram-list -->

    <section id="get-started">
      <p>Guide<!-- ガイド --></p>
      <a href="https://lislog.heroku.com/guide.html" target="_blank" class="button">Learn How to use lislog<!--リスログの使い方--></a>
    </section>

    <section id="samples" class="clearfix">
      <h1>About us<!--リスログについて--></h1>
      <div class="list">
        <!--h3--><!--リスログについて--><!--/h3-->
        <ul class="things">
          <li>
            <a href="#" id="id-qa">
              <span class="apprequests">Q&amp;A</span>
            </a>
          </li>
          <li><a href="#" id="id-privacy-policy"><span class="apprequests"  >Privacy policy<!--プライバシーポリシー--></span></a></li>
          <li><a href="#" id="id-terms-of-service"><span class="apprequests">Terms of service<!--利用規約--></span       ></a></li>
          <li><a href="#" id="id-user-support"><span class="apprequests"    >User support<!--お問い合せ--></span      ></a></li>
        </ul>
      </div>
      <div class="list">
        <!--h3--><!--Send Request--><!--/h3-->
        <ul class="things">
          <li>
            <a href="#" class="button" id="sendRequest" data-message="Invite your friends"><!--"招待したい友達を選択して「送信」を押してください."-->Invite friends<!--友達を招待-->
            </a>
          </li>
        </ul>
      </div>
      <!-- 2013-03-20 banz-ghb deploy like button -->
      <!--div class="fb-like-box" data-href="https://www.facebook.com/lislog" data-width="292" data-show-faces="true" data-stream="false" data-header="false"--><!--/div-->
      <!--div class="fb-like" class="fb-like-overflow" data-send="false" data-layout="button_count" data-width="225" data-show-faces="false"--><!--/div-->
    </section>

    <section id="guides" class="clearfix">
      <!--h1--><!--タイムライン--><!--Check Your Facebook Timeline--><!--/h1-->
      <ul>
        <li>
          <!-- response.username -->
          <!-- https://www.facebook.com/akinori.kohno.5/allactivity?privacy_source=activity_log&log_filter=app_554694347877002 -->
          <!-- https://www.facebook.com/${RESPONSE.USERNAME}/allactivity?privacy_source=activity_log&log_filter=app_${APPID} -->
          <!-- a href="https://www.heroku.com/?utm_source=facebook&utm_medium=app&utm_campaign=fb_integration" target="_top" class="icon apps-on-facebook"--><!-- Timeline --><!-- /a -->
          <!-- for PC     https://www.facebook.com/me/app_lislogapp -->
          <!-- for Mobile https://www.facebook.com/me -->
          <a href="https://www.facebook.com/me" target="_top" class="icon apps-on-facebook">Facebook Timeline<!--タイムライン--></a>
          <!--p--><!--View the activity logs of lislog in your facebook timeline.--><!--/p-->
        </li>
      </ul>
    </section>

    <section id="id-shared-activity" class="clearfix">
      <!-- adjust layout -- data-width="300" data-height="300" -->
      <!-- FB.XFBML.parse(); -->
      <fb:shared-activity id="id-shared-activity-div"></fb:shared-activity>
      <!--div id="id-shared-activity-div" class="fb-shared-activity" --><!--/div-->
    </section>

    <!-- initialize facebook javascript sdk -->
    <div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo AppInfo::appID(); ?>', // App ID
          channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel_<?php he($client_locale);?>.html', // Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true // parse XFBML
        });

        //03-24 start
        var container = document.getElementById('id-i18n-div');
        FB.XFBML.parse(container);
        //03-24 end

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

        function function_eventStateChangeOnLislog(response3){ //start response3
          if (response3.status == "connected") {
            logResponse(response3);//alert("login");
            $("#fb-login").hide();
            $("#fb-auth").hide();
            $("#picture").show();    // 2013-02-24 banz-ghb switch lislog-main
            $("#lislog-main").show();// 2013-02-24 banz-ghb switch lislog-main
            //$("#id-shared-activity").show(); // 2013-03-20 auth
            //FB.api('/me/lislogapp:tune_in','GET',{limit:4}, //FB.api 31
            //FB.api('/me/music.listens',    'GET',{limit:4}, //FB.api 31
            //function (response31) {
                //updateMostRecentActivity(response31.data);
                //addRowToBottom(response31.data); 2013-03-20 banz-ghb disable recent activityies
                //getAppUsingFriends();// 2013-03-17 banz-ghb disable to show app_using_friends
            //}
            //); //FB.api 31
            $('#picture').attr("style", "background-image: url(https://graph.facebook.com/"+response3.authResponse.userID+"/picture?type=normal)");
            //03-24 start
            var container = document.getElementById('id-i18n-div');
            FB.XFBML.parse(container);
            //03-24 end
          } else if (response3.status == "not_authorized") {
            $("#fb-login").hide();
            $("#fb-auth").show();
            $("#picture").show();    // 2013-02-24 banz-ghb switch lislog-main
            $("#lislog-main").show();// 2013-02-24 banz-ghb switch lislog-main
          //$("#id-shared-activity").hide(); // 2013-03-20 auth
          } else {
            logResponse(response3);//alert("not login");

            $('#fb-login').show();
            $("#fb-auth").hide();
            $("#picture").hide();    // 2013-02-24 banz-ghb switch lislog-main
            $("#lislog-main").hide();// 2013-02-24 banz-ghb switch lislog-main
          //$("#id-shared-activity").hide(); // 2013-03-20 auth
          //alert('login callback 1');
            //FB.login(function(response){
            //  alert('login callback');
            //}
          } //if end
          //$("#most-recent-activity").hide();// 2013-03-02 banz-ghb hide most-recent-activity when logged out
          //$("#samples").hide();// 2013-03-20 banz-ghb show samples
        } //end response3

        FB.getLoginStatus(function_eventStateChangeOnLislog);
        FB.Event.subscribe('auth.statusChange', function_eventStateChangeOnLislog);

        FB.Canvas.setAutoGrow();
      };

      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/<?php echo he($client_locale);?>/all.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
  </body>
</html>
