<?php
// PixelDrain downloader
//   Written By Jeremy Harmon <jeremy.harmon@zoho.com
//   http://github.com/zordtk/pixeldrain

$cfg = [
    'baseUrl' => "https://pixeldrain.com/api",
    'downloadPath' => '.'
];

function _fetchMetadata($url) {
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);

    curl_close($curl);
    return $result;
}

function get($id) {
    global $cfg;

    echo "[PixelDrain] Fetching metadata for ${id}...";
    $info = json_decode(_fetchMetadata("${cfg['baseUrl']}/list/${id}"));
    if( $info->success == true ) {
        echo " done.\n[PixelDrain] id is a file list, fetching files...\n";
        _handleFileList($id, $info);
    } else {
        $info = json_decode(_fetchMetadata("{$cfg['baseUrl']}/file/${id}/info"));
        if( $info->success != true ) 
            die(" failed!");
        echo " done.\n";

        $fileName = $info->name;
        echo "[PixelDrain] Downloading ${fileName}:\n\n";
        _getFile($info->id, $info->name);
    }
}

function _getFile($fileId, $fileName) {
    global $cfg;

    $curl = curl_init();

    $filePath 	= "${cfg['downloadPath']}/${fileName}";
    $fp 		= fopen($filePath, "wb");
    
    if( !$fp ) {
        die("\nFailed to open ${filePath} for writing!");
    }
    
    curl_setopt_array($curl, [
        CURLOPT_URL => "${cfg['baseUrl']}/file/${fileId}",
        CURLOPT_HEADER => 0,
        CURLOPT_NOPROGRESS => false,
        CURLOPT_FILE => $fp
    ]);

    curl_exec($curl);

    fclose($fp);
    curl_close($curl);
}


function _handleFileList($id, $info) {
    global $cfg;

    if( $info->success == true ) {
        $fileCount = $info->file_count;

        echo "[PixelDrain] ${id} contains ${fileCount} file(s).\n";
        for( $i=0; $i<$fileCount; $i++ ) {
            $fileId     = $info->files[$i]->id;
            $fileName   = $info->files[$i]->name;
            $fileNum    = $i+1;
            echo "[PixelDrain] Downloading[${fileNum}/${fileCount}] ${fileName}:\n\n";
            _getFile($fileId, $fileName);
        }
    }
}   

if( $argc > 1 ) {
    foreach( array_slice($argv, 1) as $id )
        get($id);
} else
    die("Usage: ${argv[0]} [id]\n");

?>
