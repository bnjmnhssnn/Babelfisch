Babelfisch
==========
Language manager for internationalization of websites
* Organize your language files in a tree structure, e.g. folders on a filesystem or parent-child hierarchy in a database
* Supports nested language files with mustache-like syntax
* Dynamic injection of additional data

----------
#### 1. Get a Babelfisch instance
```php
use \bnjmnhssnn\Babelfisch;
use \bnjmnhssnn\Babelfisch\StorageAdapter\FilesystemAdapter;

$bf = new Babelfisch(
    new FilesystemAdapter(__DIR__ . '/languages'),
    'DE', 'EN', 'NL' // Specify a preferred language and optional fallback languages
);
```
Example structure of your languages folder:
```
languages/
├── buttons/
│   ├── buy_DE.txt
│   ├── buy_EN.txt
│   └── buy_NL.txt
└── greetings/
    ├── welcome_DE.txt
    ├── welcome_EN.txt
    └── welcome_NL.txt
```
