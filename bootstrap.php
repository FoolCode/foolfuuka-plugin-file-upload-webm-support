<?php

use Foolz\Foolframe\Model\Context;
use Foolz\Plugin\Event;

class HHVM_UploadWebM
{
    public static function run()
    {
        Event::forge('Foolz\Plugin\Plugin::execute.foolz/foolfuuka-plugin-upload-webm')
            ->setCall(function ($result) {
                /* @var Context $context */
                $context = $result->getParam('context');

                /** @var Autoloader $autoloader */
                $autoloader = $context->getService('autoloader');
                $autoloader->addClassMap([
                    'Foolz\Foolfuuka\Plugins\UploadWebM\Model\WebM' => __DIR__.'/classes/model/webm.php'
                ]);

                $context->getContainer()
                    ->register('foolfuuka-plugin.upload_webm', 'Foolz\Foolfuuka\Plugins\UploadWebM\Model\WebM')
                    ->addArgument($context);

                Event::forge('Foolz\Foolfuuka\Model\Media::upload.config')
                    ->setCall('Foolz\Foolfuuka\Plugins\UploadWebM\Model\WebM::updateConfig')
                    ->setPriority(1);

                Event::forge('Foolz\Foolfuuka\Model\Media::insert.result.media_data')
                    ->setCall('Foolz\Foolfuuka\Plugins\UploadWebM\Model\WebM::processMedia')
                    ->setPriority(1);

                Event::forge('Foolz\Foolfuuka\Model\Media::insert.result.create_thumbnail')
                    ->setCall('Foolz\Foolfuuka\Plugins\UploadWebM\Model\WebM::processThumb')
                    ->setPriority(1);
            });
    }
}

(new HHVM_UploadWebM())->run();
