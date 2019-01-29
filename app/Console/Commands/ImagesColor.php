<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManager;
use marijnvdwerf\palette\Palette;

use App\Image;

class ImagesColor extends AbstractCommand
{

    protected $signature = 'images:color';

    protected $description = 'Determine dominant color for each image';

    public function handle()
    {
        $manager = new ImageManager(['driver' => 'imagick']);

        $images = Image::select('id')->whereNull('color');

        foreach ($images->cursor() as $image)
        {
            $file = storage_path() . '/app/images/' . $image->id . '.jpg';

            if (!file_exists($file))
            {
                $this->warn("{$image->id} - File not found");
                continue;
            }

            $contents = file_get_contents($file);

            try {
                $palette = Palette::generate($manager->make($contents));
            } catch (\Exception $e) {
                // TODO: Resolve [ErrorException]  max(): Array must contain at least one element
                // See vendor/marijnvdwerf/material-palette/src/Palette.php:81
                // https://github.com/marijnvdwerf/material-palette-php/issues/6
                $this->warn("{$image->id} - Monotone image skipped");
                continue;
            }

            // TODO: Reorder these for better results?
            $swatches = collect([
                'vibrant' => $palette->getVibrantSwatch(),
                'muted' => $palette->getMutedSwatch(),
                'vibrant_light' => $palette->getLightVibrantSwatch(),
                'muted_light' => $palette->getLightMutedSwatch(),
                'vibrant_dark' => $palette->getDarkVibrantSwatch(),
                'muted_dark' => $palette->getDarkMutedSwatch(),
            ]);

            // Select the first swatch that (1) isn't empty, and (2) isn't derived
            $swatch = $swatches->first(function($swatch) {
                return !is_null($swatch) && $swatch->getPopulation() > 0;
            });

            // This might happen if the image is black-and-white?
            if (!$swatch)
            {
                $this->warn("{$image->id} - No swatches generated");
                continue;
            }

            // Convert to HSL - better for searching w/ Elasticsearch:
            // https://dpb587.me/blog/2014/04/24/color-searching-with-elasticsearch.html
            $color = $swatch->getColor()->asHSLColor();

            // For calculating percentage of pixel population
            $size = getimagesize($file);

            // @TODO Consider using HSV instead
            $out = [
                'population' => $swatch->getPopulation(),
                'percentage' => $swatch->getPopulation() / ($size[0] * $size[1]) * 100,
                'h' => floor($this->normalize($color->getHue() * 360, 0, 360)),
                's' => floor($this->normalize($color->getSaturation() * 100, 0, 100)),
                'l' => floor($this->normalize($color->getLightness() * 100, 0, 100)),
            ];

            // Save the generated color to database
            $image->color = $out;
            $image->save();

            $this->info($image->id . ' - ' . json_encode($out));
        }
    }

    /**
     * Normalizes any number to an arbitrary range by assuming the range
     * wraps around when going below min or above max.
     *
     * @link https://stackoverflow.com/questions/1628386/normalise-orientation-between-0-and-360
     */
    private function normalize($value, $min, $max)
    {
        $range = $max - $min;
        $offset = $value - $min;

        // + start to reset back to start of original range
        return ($offset - (floor($offset / $range) * $range)) + $min;
    }

}
