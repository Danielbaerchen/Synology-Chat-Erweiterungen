<?php
if (isset($_POST['text'])) {
    $Text = $_POST['text'];
    $Text = str_replace("/vrr ", "", $Text);
    $TextParts = explode(';', $Text);
    $Start = isset($TextParts[0]) && !empty($TextParts[0]) ? trim($TextParts[0]) : 'Bochum HBF';
    $Ziel = isset($TextParts[1]) && !empty($TextParts[1]) ? trim($TextParts[1]) : 'Ruhr-Universität';
    $Zeit = isset($TextParts[2]) && !empty($TextParts[2]) ? trim($TextParts[2]) : 'now';
    $stopFinderUrlStart = 'http://openservice-test.vrr.de/static03/XML_STOPFINDER_REQUEST?name_sf=' . urlencode($Start) . '&outputFormat=rapidJSON&type_sf=stop&version=10.5.17.3';
    $stopFinderResponseStart = file_get_contents($stopFinderUrlStart);
    $stopFinderDataStart = json_decode($stopFinderResponseStart, true);
    $startStationId = '';
    if (isset($stopFinderDataStart['locations'][0]['id'])) {
        $startStationId = $stopFinderDataStart['locations'][0]['id'];
    }
    $stopFinderUrlDestination = 'http://openservice-test.vrr.de/static03/XML_STOPFINDER_REQUEST?name_sf=' . urlencode($Ziel) . '&outputFormat=rapidJSON&type_sf=stop&version=10.5.17.3';
    $stopFinderResponseDestination = file_get_contents($stopFinderUrlDestination);
    $stopFinderDataDestination = json_decode($stopFinderResponseDestination, true);
    $destinationStationId = '';
    if (isset($stopFinderDataDestination['locations'][0]['id'])) {
        $destinationStationId = $stopFinderDataDestination['locations'][0]['id'];
    }
if (!empty($startStationId) && !empty($destinationStationId)) {
    $tripRequestUrl = 'http://openservice-test.vrr.de/static03/XML_TRIP_REQUEST2?name_destination=' . urlencode($destinationStationId) . '&name_origin=' . urlencode($startStationId) . '&outputFormat=rapidJSON&type_destination=any&type_origin=any&type_via=any&version=10.5.17.3&calcOneDirection=1&exclMOT_12=1&exclMOT_14=1&exclMOT_15=1&exclMOT_16=1&date=' . urlencode($Zeit);
    $tripRequestResponse = file_get_contents($tripRequestUrl);
    $tripRequestData = json_decode($tripRequestResponse, true);
    $durationSeconds = $tripRequestData['journeys'][0]['legs'][0]['duration'];
    $durationMinutes = ceil($durationSeconds / 60);
    if (isset($tripRequestData['journeys'][0])) {
        $journey = $tripRequestData['journeys'][0];
        $legs = $journey['legs'];
        $API_Antwort = "VRR Route: " . $legs[0]['origin']['name'] . " >>> " . $legs[count($legs) - 1]['destination']['name'] . "\n";
         $API_Antwort .= "Dauer: " . $durationMinutes . " Minuten\n\n";
        foreach ($legs as $leg) {
            $departureTime = date('H:i', strtotime($leg['origin']['departureTimePlanned']));
            $arrivalTime = date('H:i', strtotime($leg['destination']['arrivalTimePlanned']));
            $transportation = $leg['transportation']['name'];
            $direction = $leg['transportation']['destination']['name'];
            $API_Antwort .= "Abfahrt: " . $departureTime . " von " . $leg['origin']['name'] . "\n";
            $API_Antwort .= "Ankunft: " . $arrivalTime . " an " . $leg['destination']['name'] . "\n";
            $API_Antwort .= "Linie: " . $transportation . " - " . $direction . "\n";
            $API_Antwort .= "\n";
        }
    } else {
        $API_Antwort = "Fahrverbindung nicht gefunden.";
    }
}
 else {
        $API_Antwort = "Start- oder Ziel-Haltestelle nicht gefunden.";
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
