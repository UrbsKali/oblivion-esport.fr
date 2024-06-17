<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style/inter.css">
    <link rel="stylesheet" href="../assets/style/theme.css">
    <link rel="stylesheet" href="admin.css">
    <title>Oblivion | ADMIN</title>
    <link rel="shortcut icon" href="../assets/img/oblivion.png" type="image/x-icon">

</head>

<body>
    <!-- Material UI admin panel with sidebar -->
    <div class="admin-sidebar">
        <div class="admin-logo">
            <img src="../assets/img/oblivion.png" alt="">
            <h3>Oblivion</h3>
        </div>
        <div class="admin-menu">
            <ul>
                <li><a href="./admin.html">Inscriptions</a></li>
            </ul>
        </div>
    </div>
    <div class="admin-content">
        <div class="admin-header">
            <h1>Inscriptions</h1>
        </div>
        <div class="admin-table">
            <table>
                <tr>
                    <th>Logo</th>
                    <th>Nom</th>
                    <th>Tag</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <?php
                    require_once ('../db.php');

                    $query = $db->query('SELECT * FROM TWC_INSCRIPTION');
                    $data = [];
                    while ($row = $query->fetch_assoc()) {
                        $data[] = $row;
                    }

                    function formatTeams($array)
                    {
                        $logo_url  = $array['logo_url'];
                        if ($logo_url == null || $logo_url == '' || $logo_url == 'uploads/') {
                            $logo_url = '../assets/img/oblivion.png';
                        }
                        $teams = array(
                            'logo' => '../'.$array['logo_url'],
                            'name' => $array['nom_equipe'],
                            'tag' => $array['tag_equipe'],
                            'motivation' => str_replace(' ', '&puncsp;', $array['motivation_text']),
                            'players' => array(
                                array(
                                    'name' => $array['top_name'],
                                    'discord' => $array['top_discord'],
                                    'email' => $array['top_email']
                                ),
                                array(
                                    'name' => $array['jungle_name'],
                                    'discord' => $array['jungle_discord'],
                                    'email' => $array['jungle_email']
                                ),
                                array(
                                    'name' => $array['middle_name'],
                                    'discord' => $array['middle_discord'],
                                    'email' => $array['middle_email']
                                ),
                                array(
                                    'name' => $array['bot_name'],
                                    'discord' => $array['bot_discord'],
                                    'email' => $array['bot_email']
                                ),
                                array(
                                    'name' => $array['support_name'],
                                    'discord' => $array['support_discord'],
                                    'email' => $array['support_email']
                                )
                            )
                        );
                        return $teams;
                    }

                    foreach ($data as $team) {
                        $t = formatTeams($team);
                        echo '<tr>';
                        echo '<td><img src="' . $t['logo'] . '" alt="" srcset=""></td>';
                        echo '<td>' . $t['name'] . '</td>';
                        echo '<td>' . $t['tag'] . '</td>';
                        echo '<td><a class="open_popup" data-blob=' . json_encode($t) . '>Voir plus</a></td>';
                        echo '</tr>';
                    }

                    ?>
                </tr>
            </table>
        </div>
    </div>
    <div class="popup">
        <div class="popup-content">
            <a class="close">X</a>
            <div class="popup-header">
                <img src="../assets/img/oblivion.png" alt="" id="img">
                <h1 id="name">NOM EQUIPE</h1>
                <p>#</p>
                <h2 id="tag">TAG</h2>
            </div>
            <div class="popup-body">
                <h2>Joueurs</h2>
                <div class="players">
                    <div class="player">
                        <p>John Doe#jsp</p>
                        <p>discord ign</p>
                        <p>email</p>
                    </div>
                    <div class="player">
                        <p>John Doe#jsp</p>
                        <p>discord ign</p>
                        <p>email</p>
                    </div>
                    <div class="player">
                        <p>John Doe#jsp</p>
                        <p>discord ign</p>
                        <p>email</p>
                    </div>
                    <div class="player">
                        <p>John Doe#jsp</p>
                        <p>discord ign</p>
                        <p>email</p>
                    </div>
                    <div class="player">
                        <p>John Doe#jsp</p>
                        <p>discord ign</p>
                        <p>email</p>
                    </div>
                </div>
                <h2>Motivation</h2>
                <p id="motivation">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Voluptatum possimus labore
                    maiores culpa? Itaque ducimus iste hic, dolores harum voluptate, sit repellat architecto, inventore
                    ex ullam sapiente quae ab fuga?</p>
            </div>
        </div>
        <script>
            /* Open popup with all button */
            let openPopup = document.querySelectorAll('.open_popup');
            openPopup.forEach(element => {
                element.addEventListener('click', function (e) {
                    initPopup(e);
                    document.querySelector('.popup').style.display = 'block';
                });
            });

            /* Close popup with close button */
            let closePopup = document.querySelector('.close');
            closePopup.addEventListener('click', function () {
                document.querySelector('.popup').style.display = 'none';
            });

            function initPopup(event) {
                let data = event.target.dataset.blob;
                data = JSON.parse(data);
                document.getElementById('img').src = data.logo;
                document.getElementById('name').innerText = data.name;
                document.getElementById('tag').innerText = data.tag;
                document.getElementById('motivation').innerText = data.motivation;

                let players = document.querySelectorAll('.player');
                for (let i = 0; i < players.length; i++) {
                    players[i].children[0].innerText = data.players[i].name;
                    players[i].children[1].innerText = data.players[i].discord;
                    players[i].children[2].innerText = data.players[i].email;
                }
            }
        </script>
</body>

</html>