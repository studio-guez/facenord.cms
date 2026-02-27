<?php
use Kirby\Cms\Block;
use Kirby\Content\Field;

$smart = fn (Field $field, Block $block) => $field->smartypants()->value();

return [

  'debug' => true,
  'api' => [
    'basicAuth' => false,        // ❌ désactive l'auth
    'allowInsecure' => true      // ✅ accepte HTTP
  ],
  'kql' => [
    'auth' => false,
  ],          // ✅ KQL sans login
//    'intercept' => function ($type, $key, $value) {
//      return true;  // Autorise TOUT en mode dev
//    }
  'routes' => [
    [
      'pattern' => '/',
      'action'  => function () {
        go('/panel');
      }
    ],
  ],
  'blocksResolver' => [
    'resolvers' => [
      'text:titre' => $smart,
      'text:text' => $smart
    ]
  ]
];
