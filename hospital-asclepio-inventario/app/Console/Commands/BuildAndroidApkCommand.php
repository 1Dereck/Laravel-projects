<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Native\Mobile\Concerns\DisplaysMarketingBanners;
use Native\Mobile\Concerns\ManagesViteDevServer;
use Native\Mobile\Concerns\ManagesWatchman;
use Native\Mobile\Concerns\PlatformFileOperations;
use Native\Mobile\Concerns\RunsAndroid;
use Native\Mobile\Concerns\RunsIos;

#[Signature('app:bundle-android')]
#[Description('Prepares the Laravel bundle for Android NativePHP')]
class BuildAndroidApkCommand extends Command
{
    use DisplaysMarketingBanners, ManagesViteDevServer, ManagesWatchman, PlatformFileOperations, RunsAndroid, RunsIos;

    public function handle(): int
    {
        $buildType = 'debug';

        $nativephpDir = base_path('nativephp');
        if (! is_dir($nativephpDir)) {
            mkdir($nativephpDir, 0755, true);
        }

        $this->info('Criando bundle do Laravel para os assets do Android...');
        $this->prepareAndroidBuild(false, false);
        $this->info('Bundle do Laravel gerado com sucesso em nativephp/android/app/src/main/assets/laravel_bundle.zip!');

        return self::SUCCESS;
    }
}
