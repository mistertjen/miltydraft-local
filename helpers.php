<?php

function d($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function dd($var) {
    echo '<pre>';
    var_dump($var);
    die('</pre>');
}

function e($condition, $yes, $no = '') {
    if($condition) echo $yes;
    else echo $no;
}

function get($param, $default = null) {
    if(isset($_POST[$param])) return $_POST[$param];
    if(isset($_GET[$param])) return $_GET[$param];
    return $default;
}

function url($uri) {
    return $_ENV['URL'] . $uri;
}

function get_draft($id) {
    $draft = file_get_contents('https://' . $_ENV['BUCKET'] . '.' . $_ENV['REGION'] . '.digitaloceanspaces.com/draft_' . $id . '.json');
    $draft = json_decode($draft, true);;

    if($draft == false) return null;

    return $draft;
}

function get_draft_local($id) {
    // $draft = file_get_contents('https://' . $_ENV['BUCKET'] . '.' . $_ENV['REGION'] . '.digitaloceanspaces.com/draft_' . $id . '.json');
    $draft = file_get_contents('drafts/' . $id . '.json', "w") or die("Unable to open file!");;
    $draft = json_decode($draft, true);;

    if($draft == false) return null;

    return $draft;
}

function return_error($err) {
    die(json_encode(['error' => $err]));
}

function return_data($data) {
    header('Access-Control-Allow-Origin: *');
    die(json_encode($data));
}

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

function save_draft_local($draft) {
  $file = fopen('drafts/' . $draft['id'] . '.json', "w") or die("Unable to create file!");
  $contents = json_encode($draft);
  fwrite($file, $contents);
  fclose($file);
}

function save_draft($draft) {
    $s3 = new \Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => 'us-east-1',
        'endpoint' => 'https://' . $_ENV['REGION'] . '.digitaloceanspaces.com',
        'credentials' => [
            'key'    => $_ENV['ACCESS_KEY'],
            'secret' => $_ENV['ACCESS_SECRET'],
        ],
    ]);


    $result = $s3->putObject([
        'Bucket' => $_ENV['BUCKET'],
        'Key'    => 'draft_' . $draft['id'] . '.json',
        'Body'   => json_encode($draft),
        'ACL'    => 'public-read'
    ]);

    return $result;
}

?>
