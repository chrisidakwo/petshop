<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Preset
    |--------------------------------------------------------------------------
    |
    | This option controls the default preset that will be used by PHP Insights
    | to make your code reliable, simple, and clean. However, you can always
    | adjust the `Metrics` and `Insights` below in this configuration file.
    |
    | Supported: "default", "laravel", "symfony", "magento2", "drupal"
    |
    */

    'preset' => 'laravel',

    /*
    |--------------------------------------------------------------------------
    | IDE
    |--------------------------------------------------------------------------
    |
    | This options allow to add hyperlinks in your terminal to quickly open
    | files in your favorite IDE while browsing your PhpInsights report.
    |
    | Supported: "textmate", "macvim", "emacs", "sublime", "phpstorm",
    | "atom", "vscode".
    |
    | If you have another IDE that is not in this list but which provide an
    | url-handler, you could fill this config with a pattern like this:
    |
    | myide://open?url=file://%f&line=%l
    |
    */

    'ide' => "phpstorm",

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may adjust all the various `Insights` that will be used by PHP
    | Insights. You can either add, remove or configure `Insights`. Keep in
    | mind, that all added `Insights` must belong to a specific `Metric`.
    |
    */

    'exclude' => [
        '_ide_helper.php',
        'public/*',
        'database/*',
    ],

    'add' => [
        \NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes::class => [
            \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenFinalClasses::class,
        ],
    ],

    'remove' => [
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff::class,
        \SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff::class,
    ],

    'config' => [
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 160
        ],
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenPrivateMethods::class => [
            'title' => 'The usage of private methods is not idiomatic in Laravel.',
        ],
        \SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class => [
            'exclude' => [
                'app/Exceptions/Handler.php',
                'app/Providers/JwtAuthServiceProvider.php',
                'app/Http/Resources/BaseResourceCollection.php',
                'app/Http/Resources/File/FileResource.php',
                'app/Http/Resources/User/UserResource.php',
                'app/Http/Resources/User/UserResourceCollection.php',
                'app/Http/Controllers/Admin/AdminController.php',
                'app/Http/Resources/Product/ProductResource',
                'app/Http/Resources/Product/ProductResourceCollection',
                'app/Http/Resources/Promotion/PromotionResource',
                'app/Http/Resources/Promotion/PromotionResourceCollection',
                'app/Http/Resources/Payment/PaymentResource.php',
                'app/Http/Resources/Payment/PaymentResourceCollection.php',
                'app/Http/Resources/Order/OrderResource.php',
                'app/Http/Resources/Order/OrderResourceCollection.php',
                'app/Http/Resources/OrderStatus/OrderStatusResource.php',
                'app/Http/Resources/OrderStatus/OrderStatusResourceCollection.php',
                'app/Http/Resources/Brand/BrandResource.php',
                'app/Http/Resources/Brand/BrandResourceCollection.php',
                'app/Http/Resources/Category/CategoryResource.php',
                'app/Http/Resources/Category/CategoryResourceCollection.php',
                'app/Http/Resources/Blog/PostResource.php',
                'app/Http/Resources/Blog/PostResourceCollection.php',
            ],
        ],
        \NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class => [
            'exclude' => [
                'app/Auth/Jwt.php',
                'app/Auth/JwtGuard.php',
                'app/Auth/Passwords/PasswordBrokerManager.php',
            ]
        ],
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenFinalClasses::class => [
            'exclude' => [
                'app/Exceptions/InvalidBearerToken.php',
            ]
        ],
        \SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class =>[
            'exclude' => [
                'app/Http/Resources/User/UserResource.php',
                'app/Http/Resources/Order/OrderResource.php',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    |
    | Here you may define a level you want to reach per `Insights` category.
    | When a score is lower than the minimum level defined, then an error
    | code will be returned. This is optional and individually defined.
    |
    */

    'requirements' => [
//        'min-quality' => 0,
//        'min-complexity' => 0,
//        'min-architecture' => 0,
//        'min-style' => 0,
//        'disable-security-check' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Threads
    |--------------------------------------------------------------------------
    |
    | Here you may adjust how many threads (core) PHPInsights can use to perform
    | the analysis. This is optional, don't provide it and the tool will guess
    | the max core number available. It accepts null value or integer > 0.
    |
    */

    'threads' => null,

];
