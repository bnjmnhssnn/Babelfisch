Babelfisch
==========
Language manager for internationalization of websites
* Organize your language files in a tree structure, e.g. folders on a filesystem or parent-child hierarchy in a database
* Supports nested language files with mustache-like syntax
* Dynamic injection of additional data

----------
#### 1. Example structure of your languages folder
Language files must be suffixed with the language identifiers used in the next step
```
languages/
├── buttons/
│   ├── buy_EN.txt
│   ├── buy_DE.txt
│   └── buy_NL.txt
├── greetings/│
│    ├── welcome_EN.txt
│    ├── welcome_DE.txt
│    └── welcome_NL.txt
└── home
     ├── main_paragraph_EN.txt
     ├── main_paragraph_DE.txt
     └── main_paragraph_NL.txt
```

#### 2. Get a Babelfisch instance
```php
use \bnjmnhssnn\Babelfisch;
use \bnjmnhssnn\Babelfisch\StorageAdapter\FilesystemAdapter;

$bf = new Babelfisch(
    new FilesystemAdapter(__DIR__ . '/languages'),
    'EN', 'DE', 'NL' // Pass the preferred language first, and optional fallback languages
);
```
When there is no language file present, Babelfish will try to load the fallback files in the specified order. 

#### 3. Output the English (EN) text
```php
$buy_button = '<button>' . $bf->output('buttons:buy') . '</button>';
```

#### 4. Inject dynamic data
Content of *languages/greetings/welcome_EN*
```
Hello {{name}}, welcome to my awesome website!
```
```php
echo $bf->output('greetings:welcome', ['name' => 'Ted']); // Hello Ted, welcome to my awesome website!
```



