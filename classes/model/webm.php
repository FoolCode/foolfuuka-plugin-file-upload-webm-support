<?php

namespace Foolz\Foolfuuka\Plugins\UploadWebM\Model;

class WebM
{
    public static function updateConfig($object)
    {
        $extensions = $object->getParam('ext_whitelist');
        $mime_types = $object->getParam('mime_whitelist');

        array_push($extensions, 'webm');
        array_push($mime_types, 'video/webm');

        $object->setParam('ext_whitelist', $extensions);
        $object->setParam('mime_whitelist', $mime_types);
    }

    public static function processMedia($object)
    {
        if ($object->getParam('dimensions') === false && $object->getParam('file')->getMimeType() == 'video/webm') {
            exec('/usr/local/bin/ffmpeg -i '.$object->getParam('path').' -vframes 1 '.$object->getParam('path').'.png');

            $object->setParam('dimensions', getimagesize($object->getParam('path').'.png'));
            $object->setParam('preview_orig', $object->getParam('time').'s.png');
        }
    }

    public static function processThumb($object)
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
