Babelfisch
==========
Language manager for internationalization of websites
* Organize your language files in a tree structure, e.g. folders on a filesystem or parent-child hierarchy in a database
* Supports nested language files with mustache-like syntax
* Dynamic injection of additional data

----------
#### 1. Example structure of your languages folder
```
languages/
├── buttons/
│   ├── buy_EN.txt
│   ├── buy_DE.txt
│   └── buy_NL.txt
└── greetings/
    ├── welcome_EN.txt
    ├── welcome_DE.txt
    └── welcome_NL.txt
```
#### 2. Get a Babelfisch instance
```php
use \bnjmnhssnn\Babelfisch;
use \bnjmnhssnn\Babelfisch\StorageAdapter\FilesystemAdapter;

$bf = new Babelfisch(
    new FilesystemAdapter(__DIR__ . '/languages'),
    'EN', 'DE', 'NL' // Specify a preferred language and optional fallback languages
);
```
#### 3. Output the English (EN) text
```php
$buy_button = '<button>' . $bf->output('buttons:buy') . '</button>';
```
#### 4. Inject data
Content of *languages/greetings/welcome_EN*:
```
Hello {{name}}, welcome to my awesome website!
```
```php
echo $bf->output('greetings:welcome', ['name' => 'Ted']); // Hello Ted, welcome to my awesome website!
```



