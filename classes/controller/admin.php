<?php

namespace Foolz\Foolframe\Controller\Admin\Plugins;

use Foolz\Foolframe\Model\Validation\ActiveConstraint\Trim;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebM extends \Foolz\Foolframe\Controller\Admin
{
    public function before()
    {
        parent::before();

        $this->param_manager->setParam('controller_title', 'WebM Preferences');
    }

    public function security()
    {
        return $this->getAuth()->hasAccess('maccess.admin');
    }

    function structure()
    {
        return [
            'open' => [
                'type' => 'open'
            ],
            'foolfuuka.plugins.upload_webm.binary_path' => [
                'preferences' => true,
                'type' => 'input',
                'label' => _i('The path to the ffmpeg/avconv binary.'),
                'class' => 'span3',
                'validation' => [new Trim()]
            ],
            'foolfuuka.plugins.upload_webm.ffprobe_path' => [
                'preferences' => true,
                'type' => 'input',
                'label' => _i('The path to the ffprobe binary.'),
                'class' => 'span3',
                'validation' => [new Trim()]
            ],
            'foolfuuka.plugins.upload_webm.allow_mods' => [
                'preferences' => true,
                'type' => 'checkbox',
                'help' => _i('Allow Moderators to upload WebM files.')
            ],
            'foolfuuka.plugins.upload_webm.allow_mods_audio' => [
                'preferences' => true,
                'type' => 'checkbox',
                'help' => _i('Allow Moderators to upload WebM files with audio streams.')
            ],
            'foolfuuka.plugins.upload_webm.allow_users' => [
                'preferences' => true,
                'type' => 'checkbox',
                'help' => _i('Allow Users to upload WebM files.')
            ],
            'foolfuuka.plugins.upload_webm.allow_users_audio' => [
                'preferences' => true,
                'type' => 'checkbox',
                'help' => _i('Allow Moderators to upload WebM files with audio streams.')
            ],
            'submit' => [
                'type' => 'submit',
                'class' => 'btn-primary',
                'value' => _i('Submit')
            ],
            'close' => [
                'type' => 'close'
            ],
        ];
    }

    function action_manage()
    {
        $this->param_manager->setParam('method_title', 'Manage');

        $data['form'] = $this->structure();

        $this->preferences->submit_auto($this->getRequest(), $data['form'], $this->getPost());

        $this->builder->createPartial('body', 'form_creator')
            ->getParamManager()->setParams($data);

        return new Response($this->builder->build());
    }
}
