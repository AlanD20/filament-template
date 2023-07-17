<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckLanguageKeys extends Command
{
    public const DEFAULT_LOCALE = 'en';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:check-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check language keys for all available JSON locale';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $files = [
            'ckb.json',
            'ar.json',
            'ar/attr.php',
            'ckb/attr.php',
        ];

        $this->getLocaleInArray($files[2]);
        $defaultJson = $this->getLocaleInArray(static::DEFAULT_LOCALE . '.json');
        $defaultAttrPHP = $this->getLocaleInArray(static::DEFAULT_LOCALE . '/attr.php');
        $failure = false;

        collect($files)
            ->mapWithKeys(function ($name) use ($defaultJson, $defaultAttrPHP) {
                $locale = $this->getLocaleInArray($name);

                return [
                    $name => str($name)->endsWith('.json') ?
                    $this->compareWithDefault($locale, $defaultJson, $name)
                    : $this->compareWithDefault($locale, $defaultAttrPHP, $name),
                ];
            })
            ->each(function ($errors, $key) use (&$failure) {
                $total = count($errors);

                if ($total === 0) {
                    return $this->info("There is no error in {$key} locale file.");
                }

                $failure = true;
                $this->newLine(2);

                foreach ($errors as $error) {
                    $this->error($error);
                }

                $this->warn("Total of {$total} errors in {$key} locale file.");
            });

        return $failure ? Command::FAILURE : Command::SUCCESS;
    }

    private function getLocaleInArray(string $locale): array
    {
        $path = \lang_path($locale);

        if (str($locale)->endsWith('.php')) {
            return include $path;
        }

        $contents = \file_get_contents($path);

        return \json_decode($contents, associative: true);
    }

    private function compareWithDefault(array $localeArray, array $defaultArray, string $locale): array
    {
        $default = static::DEFAULT_LOCALE . '.json';
        $errors = [];

        $localeCount = count($localeArray);
        $defaultCount = count($defaultArray);

        if ($localeCount !== $defaultCount) {
            array_push($errors, "- Missing keys!\n\t* Total `{$locale}` is {$localeCount}\n\t* Total `{$default}` is {$defaultCount}\n");
        }

        foreach ($localeArray as $key => $_) {
            if (! \array_key_exists($key, $defaultArray)) {
                array_push($errors, "- `{$key}` does not exist in `{$default}`.\n");
            }
        }

        foreach ($defaultArray as $key => $_) {
            if (! \array_key_exists($key, $localeArray)) {
                array_push($errors, "- `{$key}` does not exist in `{$locale}`.\n");
            }
        }

        return $errors;
    }
}
