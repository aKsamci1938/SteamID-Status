<?php

$steamApiKey = ''; //STEAM APİ KEY GELECEK

if (isset($_POST['steamID'])) {
    $inputSteamId = $_POST['steamID'];

    if (is_numeric($inputSteamId) && strlen($inputSteamId) == 17) {
        $steamId64 = $inputSteamId;
    } else if (preg_match('/^STEAM_0:[01]:\d+$/', $inputSteamId)) {
        list(, $y, $z) = explode(':', $inputSteamId);
        $steamId64 = bcadd(bcadd(bcmul($z, '2'), '76561197960265728'), $y);
    } else {
        $apiUrl = "https://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?key=$steamApiKey&vanityurl=$inputSteamId";

        $response = file_get_contents($apiUrl);

        if ($response !== false) {
            $data = json_decode($response, true);

            if (isset($data['response']['success']) && $data['response']['success'] == 1) {
                $steamId64 = $data['response']['steamid'];
            } else {
                $errorMessage = 'Kullanıcı bulunamadı.';
            }
        } else {
            $errorMessage = 'Steam API ile iletişim kurulamadı.';
        }
    }

    if (isset($steamId64)) {
        $apiUrl = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key=$steamApiKey&steamids=$steamId64";

        $response = file_get_contents($apiUrl);

        if ($response !== false) {
            $data = json_decode($response, true);

            if (isset($data['response']['players'][0])) {
                $user = $data['response']['players'][0];
                $steamId64 = $user['steamid'];
                $username = $user['personaname'];
                $avatar = $user['avatarfull'];
                $profileUrl = $user['profileurl'];
                $steamId3 = "STEAM_0:" . $user['profilestate'] . ":" . $user['profilestate'];
                $steam32 = bcsub($user['steamid'], '76561197960265728');
                $steam64 = $user['steamid'];
            } else {
                $errorMessage = 'Kullanıcı bulunamadı.';
            }
        } else {
            $errorMessage = 'Steam API ile iletişim kurulamadı.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Steam ID Status View - By aKsamci</title>

    <!-- aKsamci CSS -->
    <link rel="stylesheet" href="css/style.css">
    <style>


            .fixed-button {
                margin: auto;
                background-color: transparent;
                border: none;
                color: white;
                text-decoration: none;
            }


            .send-button {
                position: relative;
                bottom: -40px;
                margin: auto;

            }

            .span{
                bottom: 10px;
            }


    </style>
    <!-- aKsamci CSS END -->

</head>
<body>
    <div class="container">
        <div class="userst">
            <div class="profile-image">
                <?php
                if (isset($avatar)) {
                    echo '<img src="' . $avatar . '" alt="profileimages">';
                }
                ?>
            </div>
            <div class="user-details">
            <div class="span">
            <?php
            if (isset($username)) {
                echo '<div class="username">' . $username . '</div><br>';
            }
            if (isset($steamId64)) {
                echo '<div class="status">SteamID64: ' . $steamId64 . '</div><br>';
            }
            if (isset($steamId3)) {
                echo '<div class="status">SteamID3: ' . $steamId3 . '</div><br>';
            }
            if (isset($steam32)) {
                echo '<div class="status">Steam32 ID: ' . $steam32 . '</div><br>';
            }
            if (isset($steam64)) {
                echo '<div class="status">Steam64 ID: ' . $steam64 . '</div><br>';
            }
            if (isset($profileUrl)) {
                echo '<div class="status">Profile URL: <a href="' . $profileUrl . '" target="_blank">' . $profileUrl . '</a></div><br>';
            }
            ?>

                </div>

                <button class="fixed-button send-button" type="submit" onclick="window.location.href = 'index.php';">Geri</button>
            </div>
        </div>
    </div>
</body>
</html>
