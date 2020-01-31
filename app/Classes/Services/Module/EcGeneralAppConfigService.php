<?php

namespace App\Classes\Services\Module;

class EcGeneralAppConfigService
{

    public function hdContent()
    {
        $ec_api = getOriginEnv('EC_APP_BASE_URL');
        $cdn_base_url = getOriginEnv('APP_CDN_BASE_URL');
        $tool_base_url = config('common.public_ice_dist_url');
        return <<<EOF
<meta name="viewport" content="width=device-width,user-scalable=no,minimum-scale=1,maximum-scale=1,shrink-to-fit=no,">
<style>
    html,
    body {
      overflow-y: auto;
      height: 100%;
      width: 100%;
      font-family: "Microsoft YaHei";
    }
    a {
      text-decoration: none;
    }

    #ice-container {
      margin: 0 auto;
      width: 100%;
      height: 100%;
      min-height: 100%;
      overflow-y: auto;
    }

    .fadeIn {
      -webkit-animation-name: fadeIn;
      animation-name: fadeIn;
      -webkit-animation-duration: 1.5s;
      animation-duration: 1.5s;
      -webkit-animation-fill-mode: both;
      animation-fill-mode: both
    }

    @keyframes fadeIn {
      from {
        opacity: 0
      }

      to {
        opacity: 1
      }
    }
  </style>
<script>
window.fetch_path = '${ec_api}api/1.0/web/1.0';
window.app_options = '{"appId":"3f8f1497-7eef-447b-8909-de67ca89cba6","appVersion":"1.0.0","service":"請替換項目名與APPID"}';</script>
<script src="${cdn_base_url}sdk/hk01/v2.5.4/jssdk.js"></script>
<script type="text/javascript" src="${tool_base_url}build/js/vconsole.min.js"></script>
<script type="text/javascript" src="${tool_base_url}build/js/initialize.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJW4jsPlNKgv6jFm3B5Edp5ywgdqLWdmc&callback=initMap" async defer></script>
EOF;
    }

    public function ftContent()
    {
        return <<<EOF
       <script>
  //使用rem单位做自适应，js部分。单位直接px除100，换成rem即可。
  (function (doc, win) {
    var docEl = doc.documentElement,
      resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
      recalc = function () {
        var clientWidth = docEl.clientWidth;
        if (!clientWidth) return;
        docEl.style.fontSize = 100 * (clientWidth / 375) + 'px';
      };
    if (!doc.addEventListener) return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
    // 計算 --vh
    doc.documentElement.style.setProperty('--vh', win.innerHeight * 0.01 + 'px');
    win.addEventListener(resizeEvt, () => {
      doc.documentElement.style.setProperty('--vh', win.innerHeight * 0.01 + 'px');
    });
  })(document, window);
/**
 * 资源加载错误处理
 * */
window.addEventListener('error', (el) => {
  if (el.target.tagName === 'IMG') {
    el.target.src = "https://ec-image.hktester.com/a6dglI9FYZLMw-TmsXT9CEl-V5g=/filters:quality(100)/amod_v1/7812b5d6e3900eeb60b9f79e56da8d6b.png";
  }
  return true;
}, true);
</script>
EOF;
    }

}
