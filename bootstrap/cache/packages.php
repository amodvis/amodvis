<?php return array (
  'amodvis/amodvis_system' => 
  array (
    'providers' => 
    array (
      0 => 'ES\\Taobaoke\\TaobaokeServiceProvider',
      1 => 'ES\\MQ\\Providers\\MQServiceProvider',
    ),
    'aliases' => 
    array (
      'Taobaoke' => 'ES\\Taobaoke\\Facades\\Taobaoke',
    ),
  ),
);