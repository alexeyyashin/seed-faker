<?php

namespace AlexeyYashin\SeedFaker;

class SeedFaker
{
    protected int $randomized = 0;
    protected string $seed;
    protected array $words = [
        // Lorem ipsum...
        'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit',

        // and the rest of the vocabulary
        'a', 'ac', 'accumsan', 'ad', 'aenean', 'aliquam', 'aliquet', 'ante',
        'aptent', 'arcu', 'at', 'auctor', 'augue', 'bibendum', 'blandit',
        'class', 'commodo', 'condimentum', 'congue', 'consequat', 'conubia',
        'convallis', 'cras', 'cubilia', 'curabitur', 'curae', 'cursus',
        'dapibus', 'diam', 'dictum', 'dictumst', 'dignissim', 'dis', 'donec',
        'dui', 'duis', 'efficitur', 'egestas', 'eget', 'eleifend', 'elementum',
        'enim', 'erat', 'eros', 'est', 'et', 'etiam', 'eu', 'euismod', 'ex',
        'facilisi', 'facilisis', 'fames', 'faucibus', 'felis', 'fermentum',
        'feugiat', 'finibus', 'fringilla', 'fusce', 'gravida', 'habitant',
        'habitasse', 'hac', 'hendrerit', 'himenaeos', 'iaculis', 'id',
        'imperdiet', 'in', 'inceptos', 'integer', 'interdum', 'justo',
        'lacinia', 'lacus', 'laoreet', 'lectus', 'leo', 'libero', 'ligula',
        'litora', 'lobortis', 'luctus', 'maecenas', 'magna', 'magnis',
        'malesuada', 'massa', 'mattis', 'mauris', 'maximus', 'metus', 'mi',
        'molestie', 'mollis', 'montes', 'morbi', 'mus', 'nam', 'nascetur',
        'natoque', 'nec', 'neque', 'netus', 'nibh', 'nisi', 'nisl', 'non',
        'nostra', 'nulla', 'nullam', 'nunc', 'odio', 'orci', 'ornare',
        'parturient', 'pellentesque', 'penatibus', 'per', 'pharetra',
        'phasellus', 'placerat', 'platea', 'porta', 'porttitor', 'posuere',
        'potenti', 'praesent', 'pretium', 'primis', 'proin', 'pulvinar',
        'purus', 'quam', 'quis', 'quisque', 'rhoncus', 'ridiculus', 'risus',
        'rutrum', 'sagittis', 'sapien', 'scelerisque', 'sed', 'sem', 'semper',
        'senectus', 'sociosqu', 'sodales', 'sollicitudin', 'suscipit',
        'suspendisse', 'taciti', 'tellus', 'tempor', 'tempus', 'tincidunt',
        'torquent', 'tortor', 'tristique', 'turpis', 'ullamcorper', 'ultrices',
        'ultricies', 'urna', 'ut', 'varius', 'vehicula', 'vel', 'velit',
        'venenatis', 'vestibulum', 'vitae', 'vivamus', 'viverra', 'volutpat',
        'vulputate',
    ];

    public function __construct(string $seed = null)
    {
        $this->seed = $seed ?? uniqid('seed_', true);
    }

    // Scalar values

    public function integer(int $from = 0, int $to = 9999): int
    {
        $result = $from - 1;
        while ($result < $from) {
            $hashFrom = $this->randomized++ . '.' . $this->seed;
            $hash = md5($hashFrom);
            $generated = hexdec(substr($hash, 0, 10));
            $result = $generated % ($to + 1);
        }

        return $result;
    }

    public function float(float $from = 0.0, float $to = 10000.0, int $dec = 2): float
    {
        $multiplier = 10 ** $dec;
        return ((float)$this->integer($from * $multiplier, $to * $multiplier)) / $multiplier;
    }

    public function boolean(): bool
    {
        return $this->integer(0, 1) === 1;
    }

    // Complex data

    public function uuid(): string
    {
        $hash = md5($this->randomized++ . '.' . $this->seed);
        $chunks = str_split($hash, 4);

        return sprintf('%s%s-%s-%s-%s-%s%s%s', ...$chunks);
    }

    public function word(): string
    {
        return $this->oneOf(...$this->words);
    }

    public function sentence(): string
    {
        $result = [];
        $cnt = $this->integer(4, 24);
        for ($i = 0; $i < $cnt; $i++) {
            $item = $this->word() . ($i < $cnt - 1 ? $this->chance([
                    ',' => 10,
                    '' => 100,
                ]) : '');
            $result[] = $item;
        }

        $result[0] = ucfirst($result[0]);

        return implode(' ', $result) . $this->chance([
                '?!' => 25,
                '?' => 50,
                '!' => 51,
                '.' => 1000,
            ]);
    }

    public function paragraph(int $sentences = 3): string
    {
        $result = [];
        for ($i = 0; $i < $sentences; $i++) {
            $result[] = $this->sentence();
        }

        return implode(' ', $result);
    }

    public function text(int $paragraphs = 3, int $sentencesMin = 3, int $sentencesMax = 5, bool $html = false): string
    {
        $result = [];
        for ($i = 0; $i < $paragraphs; $i++) {
            $result[] = sprintf(
                '%s%s%s',
                $html ? '<p>' : '',
                $this->paragraph($this->integer($sentencesMin, $sentencesMax)),
                $html ? '</p>' : ''
            );
        }

        return implode("\n", $result);
    }

    /**
     * @param $width
     * @param $height
     * @param $forms
     * @param $maincolor
     * @return array
     */
    public function image($width = 500, $height = 500, $forms = 500, $maincolor = null): array
    {
        // Check if the GD library is installed
        if (!extension_loaded('gd')) {
            throw new \RuntimeException(
                'GD library is not installed. Please install it to use the image generation feature.'
            );
        }

        // Create a temporary file in the system's temp directory
        $tmpname = tempnam(sys_get_temp_dir(), 'seed_faker_img_');
        if ($tmpname === false) {
            throw new \RuntimeException('Failed to create temporary file');
        }

        // Create a blank image with the specified dimensions
        $image = imagecreatetruecolor($width, $height);

        if ($maincolor) {
            [$r, $g, $b] = $maincolor;
        } else {
            $r = $this->integer(0, 255);
            $g = $this->integer(0, 255);
            $b = $this->integer(0, 255);
        }

        // Generate a random background color
        $backgroundColor = imagecolorallocate($image, $r, $g, $b);

        // Generate a random foreground color
        if ($r + $g + $b > 765 / 2 || ($g + $b > 255 && $b < 180)) {
            $fgColor = array_map(fn() => $this->integer(0, 50), array_fill(0, 3, null));
        } else {
            $fgColor = array_map(fn() => $this->integer(205, 255), array_fill(0, 3, null));
        }
        $foregroundColor = imagecolorallocate($image, ...$fgColor);

        // Fill the image with the background color
        imagefill($image, 0, 0, $backgroundColor);

        // Add forms
        for ($i = 0; $i < $forms; $i++) {
            $rsize = $this->integer(($height + $width) / 100, ($height + $width) / 5);
            $rposX = $this->integer(0, $height + $width - $rsize);
            $rposY = $this->integer(0, $height + $width - $rsize);
            imagefilledrectangle(
                $image,
                $rposX,
                $rposY,
                $rposX + $rsize,
                $rposY + $rsize,
                $i % 2 ? $foregroundColor : $backgroundColor
            );
        }

        // Save the image to the temporary file
        if (!imagepng($image, $tmpname)) {
            unlink($tmpname);
            throw new \RuntimeException('Failed to save image to temporary file');
        }

        $result = [
            'name' => 'placeholder.png',
            'type' => 'image/png',
            'tmp_name' => $tmpname,
            'error' => 0,
            'size' => filesize($tmpname),
        ];

        // Clean up
        imagedestroy($image);

        return $result;
    }

    // Randomizers

    /**
     * @param mixed ...$items
     * @return mixed
     */
    public function oneOf(mixed ...$items): mixed
    {
        return $items[$this->integer(0, count($items) - 1)];
    }

    public function someOf(mixed ...$items): array
    {
        $result = [];
        for ($i = 0; $i < $this->integer(1, count($items)); $i++) {
            $item = $this->oneOf(...$items);
            $result[] = $item;

            unset($items[array_search($item, $items, true)]);
        }

        return $result;
    }

    /**
     * @param array $values
     * @return mixed
     */
    public function chance(array $values): mixed
    {
        // Transform the input array to match the expected format for chanceMixed
        $formattedValues = [];
        foreach ($values as $key => $chance) {
            $formattedValues[] = [$chance, $key]; // Create a sub-array with chance and key
        }

        // Call chanceMixed with the formatted values
        return $this->chanceMixed($formattedValues);
    }

    public function chanceMixed(array $values): mixed
    {
        $total = array_sum(array_column($values, 0));
        $rand = $this->integer(1, $total);
        $checked = 0;

        foreach ($values as $chance) {
            $checked += $chance[0];
            if ($rand <= $checked) {
                return $chance[1];
            }
        }

        return null; // Fallback in case of an error
    }
}
