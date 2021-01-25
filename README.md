Babelfisch
==========
Language manager for internationalization of websites
* Organize your language files in a tree structure, e.g. folders on a filesystem or parent-child hierarchy in a database
* Supports nested language files with mustache-like syntax
* Dynamic injection of additional data

![PHP Composer](https://github.com/bnjmnhssnn/Babelfisch/workflows/PHP%20Composer/badge.svg)
----------
#### Example structure of your languages folder
Language files must be suffixed with the language identifiers used in the next step
```
languages/
├── button/
│   ├── buy_EN.txt
│   ├── buy_DE.txt
│   └── buy_NL.txt
├── greeting/│
│    ├── welcome_EN.txt
│    ├── welcome_DE.txt
│    └── welcome_NL.txt
└── home
     ├── main_paragraph_EN.txt
     ├── main_paragraph_DE.txt
     └── main_paragraph_NL.txt
```
No matter if you use filesystem storage or a database, you are free to organize your language files however you like. If you choose a hierarchical, tree-like structure, you can access each entry by a colon-separated identifier (See **Basic Usage**)

#### Get a Babelfisch instance
```php
use \bnjmnhssnn\Babelfisch;
use \bnjmnhssnn\Babelfisch\StorageAdapter\FilesystemAdapter;

$bf = new Babelfisch(
    new FilesystemAdapter(__DIR__ . '/languages'),
    'EN', 'DE', 'NL' // Pass the preferred language first, and optional fallback languages
);
```
When there is no language file present, Babelfish will try to load the fallback files in the specified order. 

#### Basic Usage
```php
$buy_button = '<button>' . $bf->output('button:buy') . '</button>';
```

#### Inject dynamic data
Content of *languages/greetings/welcome_EN*
```
Hello {{name}}, welcome to my awesome website!
```
```php
echo $bf->output('greeting:welcome', ['name' => 'Ted']); // Hello Ted, welcome to my awesome website!
```

#### Nested language files & dynamic data
Content of *languages/home/main_paragraph_EN*
```
{{greeting:welcome}}
Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna.
{{button:buy}}
```
```php
echo $bf->output('home:main_paragraph', ['name' => 'Ted']);
```

#### Use cacheing
In the case of large and deeply nested language files, it could be more performant to use a cache module.
```php
use \bnjmnhssnn\Babelfisch;
use \bnjmnhssnn\Babelfisch\StorageAdapter\FilesystemAdapter;
use \bnjmnhssnn\Babelfisch\Cache\FilesystemCache;

$bf = new Babelfisch(new FilesystemAdapter(__DIR__ . '/languages'), 'EN', 'DE', 'NL');
$bf->setCache(new FilesystemCache(__DIR__ . '/cache'));
```
When you pass dynamic data to the output method, the cache module will generate **one cache file for each dynamic data set**, which is not desired in many cases. Though, you have to activate the cache explicitely with a third boolean argument, or use the convenience method `outputWithCache` instead.
```php
echo $bf->output('very_large_paragraph', [], true);
// ... or with the convenient method "outputWithCache"
echo $bf->outputWithCache('very_large_paragraph');
```



