# Phalcon-ModelGen
Generate models using an extended set of model annotations.

## Introduction
The Phalcom Framework ORM system is powerful, but it requires a lot of boilerplate to achieve more advanced functions. Furthermore, it does not work with high-level objects such as DateTime instances, or JSON objects.

Using ModelGen, we can generate the boilerplate code based on a minimal model specification. See the [Examples](examples/README.md) folder for a few model examples.

Models will be stored as PHP files on disk. No magic methods are used, so expect a small performance advantage over traditional models. Models do not depend on any runtime.

## Requirements
* PHP 7.2 or later.
* Phalcon Framework 4.0 or later.

## Installation
Install this dependency using `composer require basilfx/phalcon-modelgen`.

### Mode of operation.
This library will intercept file includes, and compile ModelGen annotated files before they are used. This is accomplished by using stream wrappers.

Within you loader configuration, you have to declare the folder where your models reside.

```php
<?php

use Phalcon\Loader;

$loader = new Loader();

$loader->registerNamespaces([
    // ...
    "My\\Models\\Base" => "modelgen://" . APP_DIR . "models/Base/",
    // ...
]);
```

Whenever you refer to a model in the `My\Models` namespace, the compiled version is used.

Next, it is important to register the stream wrapper.

```
<?php

use BasilFX\ModelGen\Compiler as ModelGenCompiler;

$modelGen = new ModelGenCompiler();

$modelGen->setCacheDirectory($config->cacheDir . "modelgen/");
$modelGen->registerAsStream();
```

### Performance
By default, ModelGen will re-compile all the files on every request. This is slow, but useful for development. You can configure this behaviour.

```php
<?php
/// ...
$modelGen->setCompileAlways($config->debug & $config->advanced->debug->modelGen);
// ...
```

Note that you have to clear your cache folder if your models have changed.

## License
See the `LICENSE` file (MIT license).
