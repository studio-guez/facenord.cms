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
		'defaultResolvers' => [
			'files' => fn (\Kirby\Cms\File $file) => [
				'url' => $file->url(),
				'width' => $file->width(),
				'height' => $file->height(),
				'srcset' => $file->srcset(),
				'alt' => $file->alt()->value(),
				'focus' =>$file->focus()->value()
			]
		],
		'resolvers' => [
			'text:titre' => $smart,
			'text:text' => $smart,
			'profiles:profiles' => function (Field $field, Block $block) {
					$structure = $field->toStructure();

					return $structure->map(function ($item) {
						$image = $item->image_cover()->toFile();
						return [
							'name' => $item->name()->value(),
							'function' => $item->function()->value(),
							'roles' => $item->roles()->value(),
							'image_cover' => $image ? [
								'url' => $image->url(),
								'width' => $image->width(),
								'height' => $image->height(),
								'srcset' => $image->srcset(),
								'alt' => $image->alt()->value(),
								'focus' => $image->focus()->value()
							] : null
					];
				})->values();
			}
		],
		'files' => [
			'gallery' => ['images'],
			'image' => ['image']
		],
	],
];
