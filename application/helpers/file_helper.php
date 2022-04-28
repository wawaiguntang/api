<?php
class Base64fileUploads
{
    function is_base64($s)
    {
        // Check if there are valid base64 characters
        // if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;
        // Decode the string in strict mode and check the results
        $decoded = base64_decode($s, true);
        if (false === $decoded) return false;
        // Encode the string again
        if (base64_encode($decoded) != $s) return false;
        return true;
    }

    public function do_uploads($path, $base64string)
    {

        // $base64string = "data:" . $mime_type . ";base64," . $base64string;
        $this->check_dir($path);
        $extension = $this->check_file_type($base64string);
        if ($extension['status'] == true) {
            $extension = $extension['data'];
        } else {
            return $extension;
        }

        /*=================uploads=================*/
        list(, $base64string) = explode(';', $base64string);
        list(, $base64string)       = explode(',', $base64string);
        $fileName                  = uniqid() . date('Y_m_d') . '.' . $extension;
        if ($this->is_base64($base64string) != true) {
            return array('status' => false, 'message' => 'This Base64 String not allowed !');
            exit;
        }
        $base64string              = base64_decode($base64string);
        file_put_contents($path . $fileName, $base64string);
        return array('status' => true, 'message' => 'successfully upload !', 'data' => ['file_name' => $fileName, 'with_path' => $path . $fileName]);
    }

    public function check_dir($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
            return true;
        }
        return true;
    }

    public function check_file_type($base64string)
    {
        $mime_type = @mime_content_type($base64string);
        $allowed_file_types = [
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpg',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        foreach ($allowed_file_types as $k => $v) {
            if ($v == $mime_type) {
                return [
                    'status' => true,
                    'data' => $k
                ];
            }
        }

        return array('status' => false, 'message' => 'File type is NOT allowed !');
        exit;
    }
}


function push_file($path, $name)
{
  // make sure it's a file before doing anything!
  if(is_file($path))
  {
    // required for IE
    if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off'); }

    // get the file mime type using the file extension
    $this->load->helper('file');

    $mime = get_mime_by_extension($path);

    // Build the headers to push out the file properly.
    header('Pragma: public');     // required
    header('Expires: 0');         // no cache
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($path)).' GMT');
    header('Cache-Control: private',false);
    header('Content-Type: '.$mime);  // Add the mime type from Code igniter.
    header('Content-Disposition: attachment; filename="'.basename($name).'"');  // Add the file name
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: '.filesize($path)); // provide file size
    header('Connection: close');
    readfile($path); // push it out
    exit();
  }
}