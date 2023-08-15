<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckLocaleKeys extends Command
{
    public const DEFAULT_LOCALE = 'en';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locale:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare default locale keys with other locale keys';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $locales = ['ckb'];
        $failure = false;
        $defaultLocale = static::DEFAULT_LOCALE;
        $default = [];

        // Load all default files into memory
        foreach ($this->getFilesForLocale($defaultLocale) as $file) {
            if (str($file)->endsWith('.json')) {
                // since json files are named with their locale, we don't have to prepend locale
                $default['json'] = $this->readAsArray($file);
            } else {
                $default[basename($file)] = $this->readAsArray($file);
            }
        }

        foreach ($locales as $locale) {
            $files = $this->getFilesForLocale($locale);
            $errors = [
                'locale' => $locale,
            ];

            foreach ($files as $file) {
                $localeAsArray = $this->readAsArray($file);
                $fileNameAsKey = basename($file);

                if (str($file)->endsWith('.json')) {
                    $errors['json'] = $this->diff($localeAsArray, $default['json']);
                } else {
                    $errors[$fileNameAsKey] = $this->diff($localeAsArray, $default[$fileNameAsKey]);
                }
            }

            $failure = $this->printErrors($errors);
        }

        return $failure ? Command::FAILURE : Command::SUCCESS;
    }

    protected function printErrors(array $errors): bool
    {
        $failure = false;
        $locale = $errors['locale'];

        $this->info("- {$locale}:");

        foreach ($errors as $file => $error) {
            if (is_string($error) || count($error) === 0) {
                continue;
            }

            $failure = true;
            $total = count($error);

            $this->warn("\tTotal error in `{$file}`: {$total}");
            $this->newLine(1);

            foreach ($error as $err) {
                $this->error("\t{$err}");
            }
        }

        $this->newLine(1);

        return $failure;
    }

    protected function readAsArray(string $localeFile): array
    {
        $path = \lang_path($localeFile);
        $array = [];

        if (str($localeFile)->endsWith('.php')) {
            $array = include $path;
        } else {
            $contents = \file_get_contents($path);
            $array = \json_decode($contents, associative: true);
        }

        $array['file'] = $localeFile;

        return $array;
    }

    protected function diff(array $array1, array $array2): array
    {
        $errors = [];
        $array1Locale = $array1['file'];
        $array2Locale = $array2['file'];

        $array1Length = count($array1);
        $array2Length = count($array2);

        if ($array1Length !== $array2Length) {
            array_push($errors, "- Missing keys!\n\t\t* Total keys in `{$array1Locale}`: {$array1Length}\n\t\t* Total keys in `{$array2Locale}`: {$array2Length}\n");
        }

        foreach ($array1 as $key => $_) {
            if (! \array_key_exists($key, $array2)) {
                array_push($errors, "- `{$key}` does not exist in `{$array2Locale}` but exists in `{$array1Locale}`.\n");
            }
        }

        foreach ($array2 as $key => $_) {
            if (! \array_key_exists($key, $array1)) {
                array_push($errors, "- `{$key}` does not exist in `{$array1Locale}` but exists in `{$array2Locale}`.\n");
            }
        }

        return $errors;
    }

    protected function getFilesForLocale(string $locale): array
    {
        $path = \lang_path($locale);

        $files = array_diff(scandir($path), ['.', '..']);

        // Prepend locale name to directory-based locale
        $files = array_map(fn ($file) => "{$locale}/{$file}", $files);

        // json localization files are named by their locale
        if (file_exists("{$path}.json")) {
            array_push($files, "{$locale}.json");
        }

        return $files;
    }
}
