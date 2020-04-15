<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Utm</title>
</head>
<body>
    
<?php
            $host = "https://utm.md";
            $fp = fsockopen("www.utm.md", 443, $errno, $errstr, 30);
            $images = array();
            if (!$fp) {
                echo "$errstr ($errno)<br />\n";
            } else {
                $out = "GET / HTTPS/1.0\r\nHost:  utm.md\r\nAccept: */*\r\n\r\n";
                $out .= "Host: www.utm.md\r\n";
                $out .= "Connection: Close\r\n\r\n";
                $content = "";
                $regEx = '/^[^?]*\.(jpg|jpeg|gif|png)/';
                $img = array();
                $imgSrc = array();
                fwrite($fp, $out);
                while (!feof($fp)) { 
                    // ex 1 start get content with get http with socket
                    $data = htmlspecialchars(fgets($fp, 128));
                    if (trim($data) != "") {
                        echo $data . "<br>";
                        $content = strval($data);
                        if(strpos($content, 'img')) {
                            array_push($img, $content);
                        }
                    }
                }
                foreach($img as $image){
                    // ex 2 sart to get all images from ex 1
                    $data = html_entity_decode($image);
                    $libxml_previous_state = libxml_use_internal_errors( true );
                    $doc = new DOMDocument();
                    $doc->loadHTML($data);
                    libxml_use_internal_errors( $libxml_previous_state );
                    $xpath = new DOMXPath($doc);
                    $src = $xpath->evaluate('string(//img/@src)');
                    
                    if(preg_match('/^[^?]*\.(jpg|gif|png)/', $src, $matches)){
                        echo $src . "<br>";
                        if(!strpos(strval($src), '/')){
                            array_push($imgSrc, $host.$src);
                        } else {
                            array_push($imgSrc, $src);
                        }
                    } else {
                        $lazy = $xpath->evaluate('string(//img/@lazy)');
                            if(preg_match('/^[^?]*\.(jpg|gif|png)/', $lazy, $matches)) {
                                echo $lazy . "<br>";
                                if(!strpos(strval($lazy), '/')){
                                    array_push($imgSrc, $host.$lazy);
                                } else {
                                    array_push($imgSrc, $lazy);
                                }
                            } else {
                                $wide = $xpath->evaluate('string(//img/@wide)');
                                    if(preg_match('/^[^?]*\.(jpg|gif|png)/', $wide, $matches)) {
                                        echo $wide . "<br>";
                                        if(!strpos(strval($wide), '/')){
                                            array_push($imgSrc, $host.$wide);
                                        } else {
                                            array_push($imgSrc, $wide);
                                        }
                                    } else {
                                        echo "FALSE <br>";
                                    }
                            }
                    }
                }

                // ex 3 start with download images 
                echo "<pre>";
                print_r($imgSrc);
                foreach($imgSrc as $img) {
                    $file_name = basename($img);

                    if(file_put_contents($file_name, file_get_contents($img))){
                        echo "File downloaded sccessfully";
                    } else {
                        echo "File downloading failed";
                    }
                }
                fclose($fp);
            }
            ?>


</body>
</html>