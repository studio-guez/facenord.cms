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
	'panel' => [
        'menu' => [
            'site' => [
                'current' => function(string $current): bool {
                    $links = ['pages/images'];
                    $path  = Kirby\Cms\App::instance()->path();

                    return $current === 'site' && A::every($links, fn($link) => Str::contains($path, $link) === false);
                }
            ],
            'images' => [
                'icon' => 'image',
                'label' => 'Images',
                'link' => 'pages/images',
                'current' => function(string $current): bool {
                    $path = Kirby\CMS\App::instance()->path();
                    return Str::contains($path, 'pages/images');
                }
            ],
            '-',
            'users',
            'system'
        ]
    ],
	 'thumbs' => [
        'srcsets' => [
            'default' => [
                '1080w'  => ['width' => 1080],
                '1400w'  => ['width' => 1400],
                '1920w'  => ['width' => 1920],
                '2560w' => ['width' => 2560],
                '3840w' => ['width' => 3840]
            ]
        ]
    ],
	'blocksResolver' => [
		'defaultResolvers' => [
			'files' => fn (\Kirby\Cms\File $file) => [
				'url' => $file->resize(3840)->url(),
				'width' => $file->resize(3840)->width(),
				'height' => $file->resize(3840)->height(),
				'srcset' => $file->srcset(),
				'alt' => $file->alt()->value(),
				'focus' =>$file->focus()->value()
			]
		],
		'resolvers' => [
			'text:titre' => $smart,
			'text:text' => $smart,
			'article_heading:titre' => $smart,
			'cards:titre' => $smart,
			'gallery:titre' => $smart,
			'pages_list:titre' => $smart,
			'podcast:titre' => $smart,
			'video:titre' => $smart, 
			'profiles:titre' => $smart,
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
		'pages' => [
			'pages_list' => ['link']
		],
		'files' => [
			'gallery' => ['images'],
			'image' => ['image']
		],
	],
];
