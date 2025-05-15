<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlexeyYashin\SeedFaker\SeedFaker;

// Create an instance of SeedFaker with random seed
$faker = new SeedFaker();

// Or create an instance with a specific seed (ex. using item ID)
// Please note that the same seed and code will generate the same data every time
$faker = new SeedFaker('item_123');

// Generate a random integer
$randomInteger = $faker->integer();
printf('Random integer: %d' . "\n", $randomInteger);

// Generate a random float
$randomFloat = $faker->float();
printf('Random float: %.2f' . "\n", $randomFloat);

// Generate a random boolean
$randomBoolean = $faker->boolean();
printf('Random boolean: %s' . "\n", $randomBoolean ? 'true' : 'false');

// Generate a random UUID
$randomUUID = $faker->uuid();
printf('Random UUID: %s' . "\n", $randomUUID);

// Generate a random word
$randomWord = $faker->word();
printf('Random word: %s' . "\n", $randomWord);

// Generate a random sentence
$randomSentence = $faker->sentence();
printf('Random sentence: %s' . "\n", $randomSentence);

// Generate a random paragraph with 3 sentences
$randomParagraph = $faker->paragraph(3);
printf('Random paragraph: %s' . "\n", $randomParagraph);

// Generate a random image
// Returns $_FILES compatible array
try {
    $imageData = $faker->image(300, 300, 5);
    printf('Generated image: %s' . "\n", $imageData['tmp_name']);
    printf('Image size: %d bytes' . "\n", $imageData['size']);
} catch (\RuntimeException $e) {
    printf('Unable to generate image. %s' . "\n", $e->getMessage());
}

// Get a random value from list of values
$randomValue = $faker->oneOf('apple', 'banana', 'cherry', 'orange', 'pineapple');
printf('Random fruit: %s' . "\n", $randomValue);

// Get a random array of values from list of values
$randomFruitsArray = $faker->someOf('apple', 'banana', 'cherry', 'orange', 'pineapple');
printf('Some random fruits: %s' . "\n", implode(', ', $randomFruitsArray));

// Get a random value with chance
$randomFruit = $faker->chance([
    'apple' => 10,
    'banana' => 20,
    'cherry' => 30,
    'orange' => 40,
    'pineapple' => 50,
    'strawberry' => 60,
]);
printf('Random fruit with chance: %s' . "\n", $randomFruit);

// Get a non-string value with chance
$randomFruits = $faker->chanceMixed([
    [10, ['apple', 'banana']],
    [20, ['banana', 'cherry']],
    [30, ['cherry', 'orange']],
    [40, ['orange', 'apple']],
    [50, ['pineapple', 'strawberry']],
    [60, ['strawberry', 'apple']],
]);
printf('Two random fruits from array with chance: %s' . "\n", implode(', ', $randomFruits));
