<?php

namespace Foolz\FoolFrame\Controller\Admin\Plugins;

use Foolz\FoolFrame\Model\Validation\ActiveConstraint\Trim;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileUploadWebMSupport extends \Foolz\FoolFrame\Controller\Admin
{
    public function before()
    {
        parent::before();

        $this->param_manager->setParam('controller_title', 'File Upload: WebM Support');
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
            'foolfuuka.plugins.file_upload_webm_support.path_binary' => [
                'preferences' => true,
                'type' => 'input',
                'label' => _i('The path to the ffmpeg/avconv binary.'),
                'class' => 'span3',
                'validation' => [new Trim()]
            ],
            'foolfuuka.plugins.file_upload_webm_support.path_ffprobe' => [
                'preferences' => true,
                'type' => 'input',
                'label' => _i('The path to the ffprobe binary.'),
                'class' => 'span3',
                'validation' => [new Trim()]
            ],
            'foolfuuka.plugins.file_upload_webm_support.allow_mods' => [
                'preferences' => true,
                'type' => 'checkbox',
                'help' => _i('Allow Moderators to upload WebM files.')
            ],
            'foolfuuka.plugins.file_upload_webm_support.allow_mods_audio' => [
                'preferences' => true,
                'type' => 'checkbox',
                'help' => _i('Allow Moderators to upload WebM files with audio streams.')
            ],
            'foolfuuka.plugins.file_upload_webm_support.allow_users' => [
                'preferences' => true,
                'type' => 'checkbox',
                'help' => _i('Allow Users to upload WebM files.')
            ],
            'foolfuuka.plugins.file_upload_webm_support.allow_users_audio' => [
                'preferences' => true,
                'type' => 'checkbox',
                'help' => _i('Allow Users to upload WebM files with audio streams.')
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
