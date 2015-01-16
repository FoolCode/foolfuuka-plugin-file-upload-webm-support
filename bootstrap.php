<?php

use Foolz\Foolframe\Model\Context;
use Foolz\Plugin\Event;

class HHVM_FileUploadWebMSupport
{
    public function run()
    {
        Event::forge('Foolz\Plugin\Plugin::execute#foolz/foolfuuka-plugin-file-upload-webm-support')
            ->setCall(function ($result) {
                /* @var Context $context */
                $context = $result->getParam('context');

                /** @var Autoloader $autoloader */
                $autoloader = $context->getService('autoloader');
                $autoloader->addClassMap([
                    'Foolz\Foolframe\Controller\Admin\Plugins\FileUploadWebMSupport' => __DIR__.'/classes/controller/admin.php',
                    'Foolz\Foolfuuka\Plugins\FileUpload\Model\WebM' => __DIR__.'/classes/model/upload.php'
                ]);

                $context->getContainer()
                    ->register('foolfuuka-plugin.file_upload_webm_support', 'Foolz\Foolfuuka\Plugins\FileUpload\Model\WebM')
                    ->addArgument($context);

                Event::forge('Foolz\Foolframe\Model\Context::handleWeb#obj.afterAuth')
                    ->setCall(function ($object) use ($context) {
                        if ($context->getService('auth')->hasAccess('maccess.admin')) {
                            $context->getRouteCollection()->add(
                                'foolfuuka.plugin.file_upload_webm_support.admin',
                                new \Symfony\Component\Routing\Route(
                                    '/admin/plugins/file_upload_webm/{_suffix}',
                                    [
                                        '_suffix' => 'manage',
                                        '_controller' => 'Foolz\Foolframe\Controller\Admin\Plugins\FileUploadWebMSupport::manage'
                                    ],
                                    [
                                        '_suffix' => '.*'
                                    ]
                                )
                            );

                            Event::forge('Foolz\Foolframe\Controller\Admin::before#var.sidebar')
                                ->setCall(function ($object) {
                                    $sidebar = $object->getParam('sidebar');
                                    $sidebar[]['plugins'] = [
                                        'content' => [
                                            'file_upload_webm/manage' => [
                                                'level' => 'admin',
                                                'name' => 'File Upload: WebM Support',
                                                'icon' => 'icon-file'
                                            ]
                                        ]
                                    ];
                                    $object->setParam('sidebar', $sidebar);
                                });
                        }
                    });

                Event::forge('Foolz\Foolfuuka\Model\MediaFactory::forgeFromUpload#var.config')
                    ->setCall(function ($object) use ($context) {
                        $auth = $context->getService('auth');
                        $pref = $context->getService('preferences');

                        if (
                            $auth->hasAccess('maccess.admin')
                            || ($auth->hasAccess('maccess.mod') && $pref->get('foolfuuka.plugins.file_upload_webm_support.allow_mods'))
                            || $pref->get('foolfuuka.plugins.file_upload_webm_support.allow_users')
                        ) {
                            $context->getService('foolfuuka-plugin.file_upload_webm_support')->updateConfig($object);
                        }
                    });

                Event::forge('Foolz\Foolfuuka\Model\Media::insert#var.media')
                    ->setCall(function ($object) use ($context) {
                        $auth = $context->getService('auth');
                        $pref = $context->getService('preferences');

                        $audio = $auth->hasAccess('maccess.admin')
                            || ($auth->hasAccess('maccess.mod') && $pref->get('foolfuuka.plugins.file_upload_webm_support.allow_mods_audio'))
                            || $pref->get('foolfuuka.plugins.file_upload_webm_support.allow_users_audio');

                        $context->getService('foolfuuka-plugin.file_upload_webm_support')->processMedia($object, $audio);
                    });

                Event::forge('Foolz\Foolfuuka\Model\Media::insert#exec.createThumbnail')
                    ->setCall(function ($object) use ($context) {
                        $context->getService('foolfuuka-plugin.file_upload_webm_support')->processThumb($object);
                    });
            });
    }
}

(new HHVM_FileUploadWebMSupport())->run();
