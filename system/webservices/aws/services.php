
<?php
/**
 * The file webservice.
 * This web-service can be called by an ajax request by specifying axn = "framework/file".
 * This web-service contains all thefile management actions line upload, download remote file, delete file etc.
 * @category web-service
 *
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */
defined('_TWACHK') or die;

class twaWebServices_aws_services extends twaWebServices {

    /**
     * Upload to the CDN
     * This service is called when you want to upload a file to the CDN.
     * POST variables must specify the bucket and the key.
     *
     * @return String A JSON string is returned with "returnCode" = 0 if successful, and 1 on error.
     * @access public
     */

    public function cdn(){
        global $framework;
        $router = $framework->load('twaRouter');
        global $app;
        global $s3;
        if(!$s3){
            $this->fail(112, "CDN Not Defined");
        }

        try{
            $result = $s3->putObject(array(
                'Bucket'     => $router->getPost('bucket'),
                'Key'        => $router->getPost('key'),
                'SourceFile' => $framework->basepath.$router->getPost('file'),
                'ACL' => 'public-read',
                'CacheControl' => 'max-age=315360000'
            ));
            $url = "https://s3.amazonaws.com/".$router->getPost('bucket')."/".$router->getPost('key');

            echo '{"returnCode":0, "url":"'.$url.'"}';

        } catch(Exception $e) {
            echo '{"returnCode":1,"errorCode":'."113".',"error":"Error on sending to the bucket.Message: '.$e->getMessage().'"}';
        }
    }


}


?>