<?php

namespace Foolz\FoolFuuka\Plugins\FileUpload\Model;

use Foolz\FoolFrame\Model\Context;
use Foolz\FoolFrame\Model\Model;
use Foolz\FoolFrame\Model\Preferences;

class WebM extends Model
{
    /**
     * @var Preferences
     */
    protected $preferences;

    public function __construct(Context $context)
    {
        parent::__construct($context);

        $this->preferences = $context->getService('preferences');
    }

    public function updateConfig($object)
    {
        $extensions = $object->getParam('ext_whitelist');
        $mime_types = $object->getParam('mime_whitelist');

        array_push($extensions, 'webm');
        array_push($mime_types, 'video/webm');

        $object->setParam('ext_whitelist', $extensions);
        $object->setParam('mime_whitelist', $mime_types);
    }

    public function processMedia($object, $audio)
    {
        if ($audio == false) {
            $video = json_decode(shell_exec($this->preferences->get('foolfuuka.plugins.file_upload_webm_support.path_ffprobe').' -v quiet -print_format json -show_streams -select_streams a '.$object->getParam('path')));
            if (isset($video->streams) && count($video->streams)) {
                throw new \Foolz\FoolFuuka\Model\MediaInsertInvalidFormatException(_i('The file you uploaded contains an audio stream which is not allowed.'));
            }
        }

        if ($object->getParam('dimensions') === false && $object->getParam('file')->getMimeType() == 'video/webm') {
            if ($this->preferences->get('foolfuuka.plugins.file_upload_webm_support.path_binary')) {
                exec($this->preferences->get('foolfuuka.plugins.file_upload_webm_support.path_binary').' -i '.$object->getParam('path').' -vframes 1 '.$object->getParam('path').'.png');

                $object->setParam('dimensions', getimagesize($object->getParam('path').'.png'));
                $object->setParam('preview_orig', $object->getParam('time').'s.png');
            }
        }
    }

    public function processThumb($object)
    {
        if ($object->getParam('media')->getMimeType() == 'video/webm') {
            exec($object->getParam('exec') .
                " " . $object->getParam('media')->getPathname() . ".png[0] -quality 80 -background none " .
                "-resize \"" . $object->getParam('thumb_width') . "x" . $object->getParam('thumb_height') .
                ">\" " . $object->getParam('thumb'));

            $object->set('done');
        }
    }
}
