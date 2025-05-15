# seed-faker

`seed-faker` is a PHP library designed to generate random data for testing and development purposes. It provides a variety of methods to create random integers, floats, booleans, UUIDs, words, sentences, paragraphs, and even images. This library is particularly useful for populating databases with sample data or for testing applications that require random input.

## Features

- Generate random values:
    - integer
    - float
    - boolean
- Generate UUIDs.
- Retrieve random words from a predefined vocabulary.
- Construct random sentences and paragraphs with customizable sentence lengths.
- Generate placeholder images with random colors and shapes.
- Support for weighted random selection through chance-based methods.

## Installation

You can install the package via Composer:

```bash
composer require alexeyyashin/seed-faker
```

## Usage

Here's a quick example of how to use the `SeedFaker` class:

```php
require_once 'vendor/autoload.php';

use AlexeyYashin\SeedFaker\SeedFaker;

$faker = new SeedFaker();

// Generate a random integer
$randomInteger = $faker->integer(1, 100);
echo "Random Integer: $randomInteger\n";

// Generate a random sentence
$randomSentence = $faker->sentence();
echo "Random Sentence: $randomSentence\n";
```

For more detailed usage instructions, please refer to the `example/demo.php`
