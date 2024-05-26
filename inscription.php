<?php

$forms = getFormsData();
$parsed_forms = parseForms($forms);
if (isset($parsed_forms["error"])) {
    header("Location: /page/inscription.html?message=" . $parsed_forms["error"]);
    exit(0);
}

$msg = "Bonjour " . $parsed_forms['nom_equipe'] . ",<br><br> Nous avons bien reçu votre inscription pour notre event. <br><br> Merci de votre participation, et à bientôt dans la faille !";
$to = $parsed_forms["top_email"] . "," . $parsed_forms["jungle_email"] . "," . $parsed_forms["middle_email"] . "," . $parsed_forms["bot_email"] . "," . $parsed_forms["support_email"];

$error_one = sendMail($to, "Confirmation d'inscription", $msg);



$msg_admin = "Une nouvelle équipe vient de s'inscrire à l'event : " . $parsed_forms['nom_equipe'] . "<br><br> Voici les informations de l'équipe : <br><br>";
// add all form data to the admin message
$msg_admin = "<table border='1'>";
$msg_admin .= "<tr><th>Champs</th><th>Valeur</th></tr>";
$msg_admin .= "<tr><td>Nom d'équipe</td><td>" . $parsed_forms["nom_equipe"] . "</td></tr>";
$msg_admin .= "<tr><td>TAG d'équipe</td><td>" . $parsed_forms["tag_equipe"] . "</td></tr>";
$msg_admin .= "<tr><td>top_name</td><td>" . $parsed_forms["top_name"] . "</td></tr>";
$msg_admin .= "<tr><td>top_discord</td><td>" . $parsed_forms["top_discord"] . "</td></tr>";
$msg_admin .= "<tr><td>top_email</td><td>" . $parsed_forms["top_email"] . "</td></tr>";
$msg_admin .= "<tr><td>jungle_name</td><td>" . $parsed_forms["jungle_name"] . "</td></tr>";
$msg_admin .= "<tr><td>jungle_discord</td><td>" . $parsed_forms["jungle_discord"] . "</td></tr>";
$msg_admin .= "<tr><td>jungle_email</td><td>" . $parsed_forms["jungle_email"] . "</td></tr>";
$msg_admin .= "<tr><td>middle_name</td><td>" . $parsed_forms["middle_name"] . "</td></tr>";
$msg_admin .= "<tr><td>middle_discord</td><td>" . $parsed_forms["middle_discord"] . "</td></tr>";
$msg_admin .= "<tr><td>middle_email</td><td>" . $parsed_forms["middle_email"] . "</td></tr>";
$msg_admin .= "<tr><td>bot_name</td><td>" . $parsed_forms["bot_name"] . "</td></tr>";
$msg_admin .= "<tr><td>bot_discord</td><td>" . $parsed_forms["bot_discord"] . "</td></tr>";
$msg_admin .= "<tr><td>bot_email</td><td>" . $parsed_forms["bot_email"] . "</td></tr>";
$msg_admin .= "<tr><td>support_name</td><td>" . $parsed_forms["support_name"] . "</td></tr>";
$msg_admin .= "<tr><td>support_discord</td><td>" . $parsed_forms["support_discord"] . "</td></tr>";
$msg_admin .= "<tr><td>support_email</td><td>" . $parsed_forms["support_email"] . "</td></tr>";
$msg_admin .= "<tr><td>have_logo</td><td>" . ($parsed_forms["have_logo"] == "on" ? "Oui" : "Non") . "</td></tr>";
$msg_admin .= "<tr><td>is_new</td><td>" . ($parsed_forms["is_new"] == "on" ? "Oui" : "Non") . "</td></tr>";
$msg_admin .= "<tr><td>nb_old_player</td><td>" . $parsed_forms["@nb_old_player"] . "</td></tr>";
$msg_admin .= "</table>";

$error_two = sendMail("urbain.lantres@gmail.com", "Nouvelle inscription", $msg_admin);

header("Location: /page/inscription.html?message=Votre inscription a bien été prise en compte");
exit(0);
/**
 * Récupère les données du formulaire
 * @return array
 */
function getFormsData()
{
    // Capture all form data into an associative array
    $forms = array(
        'nom_equipe' => $_POST['nom_equipe'],
        'tag_equipe' => $_POST['tag_equipe'],
        'top_name' => $_POST['top_name'],
        'top_discord' => $_POST['top_discord'],
        'top_email' => $_POST['top_email'],
        'jungle_name' => $_POST['jungle_name'],
        'jungle_discord' => $_POST['jungle_discord'],
        'jungle_email' => $_POST['jungle_email'],
        'middle_name' => $_POST['middle_name'],
        'middle_discord' => $_POST['middle_discord'],
        'middle_email' => $_POST['middle_email'],
        'bot_name' => $_POST['bot_name'],
        'bot_discord' => $_POST['bot_discord'],
        'bot_email' => $_POST['bot_email'],
        'support_name' => $_POST['support_name'],
        'support_discord' => $_POST['support_discord'],
        'support_email' => $_POST['support_email'],
        'have_logo' => $_POST['have_logo'],
        'is_new' => $_POST['is_new'],
        '@nb_old_player' => $_POST['nb_old_player'],
    );

    return $forms;
}
/** 
 * Traite les données du formulaire, en retirant les caractères spéciaux et en verifiant les champs vides
 * @param array $forms
 * @return array
 */
function parseForms($forms)
{
    $parsed_forms = array();
    foreach ($forms as $name => $form) {
        // check if null or empty
        if (is_null($form) || empty($form)) {
            if (str_contains($name, '@')) {
                continue;
            } else {
                return array('error' => 'Veuillez remplir tous les champs');
            }
        }
        $parsed_forms[$name] = htmlspecialchars($form);

    }
    return $parsed_forms;
}

function sendMail($to, $subject, $message)
{
    $from = "Oblivion Esports <noreply@oblivion-esport.fr>";

    $headers = array(
        'From' => $from,
        'Reply-To' => $from,
        'X-Mailer' => 'PHP/' . phpversion(),
        'MIME-Version' => '1.0',
        'Content-type' => 'text/html; charset=utf-8',
    );

    return mail($to, $subject, $message, $headers);
}