<?php

use Foolz\Foolframe\Model\Context;
use Foolz\Plugin\Event;

class HHVM_UploadWebM
{
    public function run()
    {
        Event::forge('Foolz\Plugin\Plugin::execute.foolz/foolfuuka-plugin-upload-webm')
            ->setCall(function ($result) {
                /* @var Context $context */
                $context = $result->getParam('context');

                /** @var Autoloader $autoloader */
                $autoloader = $context->getService('autoloader');
                $autoloader->addClassMap([
                    'Foolz\Foolframe\Controller\Admin\Plugins\WebM' => __DIR__.'/classes/controller/admin.php',
                    'Foolz\Foolfuuka\Plugins\UploadWebM\Model\WebM' => __DIR__.'/classes/model/webm.php'
                ]);

                $context->getContainer()
                    ->register('foolfuuka-plugin.upload_webm', 'Foolz\Foolfuuka\Plugins\UploadWebM\Model\WebM')
                    ->addArgument($context);

                Event::forge('Foolz\Foolframe\Model\Context.handleWeb.has_auth')
                    ->setCall(function ($object) use ($context) {
                        if ($context->getService('auth')->hasAccess('maccess.admin')) {
                            $context->getRouteCollection()->add(
                                'foolfuuka.plugin.upload_webm.admin',
                                new \Symfony\Component\Routing\Route(
                                    '/admin/plugins/webm/{_suffix}',
                                    [
                                        '_suffix' => 'manage',
                                        '_controller' => 'Foolz\Foolframe\Controller\Admin\Plugins\WebM::manage'
                                    ],
                                    [
                                        '_suffix' => '.*'
                                    ]
                                )
                            );

                            Event::forge('Foolz\Foolframe\Controller\Admin.before.sidebar.add')
                                ->setCall(function ($object) {
                                    $sidebar = $object->getParam('sidebar');
                                    $sidebar[]['plugins'] = [
                                        'content' => [
                                            'webm/manage' => [
                                                'level' => 'admin',
                                                'name' => 'WebM Preferences',
                                                'icon' => 'icon-file'
                                            ]
                                        ]
                                    ];
                                    $object->setParam('sidebar', $sidebar);
                                });
                        }
                    });

                Event::forge('Foolz\Foolfuuka\Model\Media::upload.config')
                    ->setCall(function ($object) use ($context) {
                        $auth = $context->getService('auth');
                        $pref = $context->getService('preferences');

                        if (
                            $auth->hasAccess('maccess.admin')
                            || ($auth->hasAccess('maccess.mod') && $pref->get('foolfuuka.plugins.upload_webm.allow_mods'))
                            || $pref->get('foolfuuka.plugins.upload_webm.allow_users')
                        ) {
                            $context->getService('foolfuuka-plugin.upload_webm')->updateConfig($object);
                        }
                    });

                Event::forge('Foolz\Foolfuuka\Model\Media::insert.result.media_data')
                    ->setCall(function ($object) use ($context) {
                        $auth = $context->getService('auth');
                        $pref = $context->getService('preferences');

                        $audio = $auth->hasAccess('maccess.admin')
                            || ($auth->hasAccess('maccess.mod') && $pref->get('foolfuuka.plugins.upload_webm.allow_mods_audio'))
                            || $pref->get('foolfuuka.plguins.upload_webm.allow_users_audio');

                        $context->getService('foolfuuka-plugin.upload_webm')->processMedia($object, $audio);
                    });

                Event::forge('Foolz\Foolfuuka\Model\Media::insert.result.create_thumbnail')
                    ->setCall(function ($object) use ($context) {
                        $context->getService('foolfuuka-plugin.upload_webm')->processThumb($object);
                    });
            });
    }
}

(new HHVM_UploadWebM())->run();
