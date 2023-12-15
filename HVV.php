<?php
#Infos zum API Key anfordern per Email auf https://www.hvv.de/de/fahrplaene/abruf-fahrplaninfos/datenabruf
$API_User = 'Hier Benutzer eintragen';
$API_Passwort = 'Hier API Code eintragen';
$API_URL = 'https://gti.geofox.de/gti/public/getRoute';

if (isset($_POST['text'])) {
    $Text = $_POST['text'];
    $Text = str_replace("/hvv ", "", $Text);
    $TextParts = explode(';', $Text);
    $Start = isset($TextParts[0]) && !empty($TextParts[0]) ? trim($TextParts[0]) : 'Jungfernstieg';
    $Ziel = isset($TextParts[1]) && !empty($TextParts[1]) ? trim($TextParts[1]) : 'Harburg';
    $Zeit = isset($TextParts[2]) && !empty($TextParts[2]) ? trim($TextParts[2]) : 'jetzt';
    $Datum = isset($TextParts[3]) && !empty($TextParts[3]) ? trim($TextParts[3]) : 'heute';
    
$Anfrage = array(
    "time" => array(
        "date" => $Datum,
        "time" => $Zeit
    ),
    "schedulesBefore" => 0,
    "schedulesAfter" => 0,
    "realtime" => "REALTIME",
    "start" => array(
        "combinedName" => $Start,
        "type" => "UNKNOWN"
    ),
    "language" => "de",
    "version" => 46,
    "dest" => array(
        "combinedName" => $Ziel,
        "type" => "UNKNOWN"
    ),
    "timeIsDeparture" => true
);
$Anfrage_json = json_encode($Anfrage);
$API_Signatur = hash_hmac("sha1", $Anfrage_json, $API_Passwort, true);
$API_Signatur = base64_encode($API_Signatur);
$headers = array(
    'Accept: application/json',
    'geofox-auth-signature: ' . $API_Signatur,
    'geofox-auth-user: ' . $API_User,
    'geofox-auth-type: HmacSHA1',
    'Accept-Encoding: gzip, deflate',
    'Content-Type: application/json'
);
$options = array(
    'http' => array(
        'header' => $headers,
        'method' => 'POST',
        'content' => $Anfrage_json
    )
);
$context = stream_context_create($options);
$response = file_get_contents($API_URL, false, $context);
$data = json_decode($response, true);
if ($data && isset($data['realtimeSchedules'][0])) {
    $API_Antwort_Eintrag = $data['realtimeSchedules'][0];
    $API_Antwort = "HVV Route: " . $API_Antwort_Eintrag['start']['name'] . " >>> " . $API_Antwort_Eintrag['dest']['name'] . "\n";
    $API_Antwort .= "Dauer: " . $API_Antwort_Eintrag['time'] . " Minuten\n\n";
    foreach ($API_Antwort_Eintrag['scheduleElements'] as $API_Antwort_Element) {
    if (strpos($API_Antwort_Element['line']['name'], 'Umstiegsfu') !== false) {
            $API_Antwort .= mb_convert_encoding("Umsteigen/Fußweg\n", 'UTF-8', 'ISO-8859-1');
            $API_Antwort .= "Von: " . $API_Antwort_Element['from']['name'] . "\n";
            $API_Antwort .= "Bis: " . $API_Antwort_Element['to']['name'] . "\n";
            $API_Antwort .= "\n";
        } else {
            $API_Antwort .= "Linie: " . $API_Antwort_Element['line']['name'] . " - " . $API_Antwort_Element['line']['direction'] . "\n";
            if (isset($API_Antwort_Element['from']['platform']) && !empty($API_Antwort_Element['from']['platform'])) {
                $API_Antwort .= "Abfahrt: " . $API_Antwort_Element['from']['depTime']['time'] . " von " . $API_Antwort_Element['from']['platform'] . "\n";
            }
            else{
                $API_Antwort .= "Abfahrt: " . $API_Antwort_Element['from']['depTime']['time'] ."\n";
            }
            $API_Antwort .= "Von: " . $API_Antwort_Element['from']['name'] . "\n";
            $API_Antwort .= "Bis: " . $API_Antwort_Element['to']['name'] . "\n";
            $API_Antwort .= "Ankunft: " . $API_Antwort_Element['to']['arrTime']['time'] . "\n";
            if (isset($API_Antwort_Element['attributes']) && is_array($API_Antwort_Element['attributes'])) {
                foreach ($API_Antwort_Element['attributes'] as $API_Antwort_Attribute) {
                    if (isset($API_Antwort_Attribute['type']) && !empty($API_Antwort_Attribute['type'])) {
                        $API_Antwort .= "Info zur Strecke: " . $API_Antwort_Attribute['text'].  "\n";
                    }
                }
            }
            $API_Antwort .= "\n";
        }
    }
} else {
    $API_Antwort = "Es wurde keine Fahrverbindung gefunden.";
}
$Antwort = array(
    'response_type' => 'in_channel',
    'text' => $API_Antwort
);
header('Content-Type: application/json');
echo json_encode($Antwort);
exit();
}
?>
