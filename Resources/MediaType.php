<?php

namespace Bundle\DownloadBundle\Resources;

/*
 * MORE TYPES:
 * http://www.iana.org/assignments/media-types/
 * http://en.wikipedia.org/wiki/Internet_media_type
 *
 */

class MediaType
{

    public static $mediaTypes = array(
        'pdf' => 'application/pdf',
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/vnd.wave',
        'zip' => 'application/zip',
    );

    static function getType($ext)
    {
        if (isset(self::$mediaTypes[$ext])) {
            return self::$mediaTypes[$ext];
        } else {
            return 'application/octet-stream';
        }
    }

}