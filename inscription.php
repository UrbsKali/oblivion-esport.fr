<?php
// get the database connection
require_once('db.php');

$forms = getFormsData();
$parsed_forms = parseForms($forms);
if (isset($parsed_forms["error"])) {
    header("Location: /page/inscription.html?message=" . $parsed_forms["error"]);
    exit(0);
}
saveToDB($parsed_forms);

$msg = "Bonjour " . $parsed_forms['nom_equipe'] . ",<br><br> Nous avons bien reçu votre inscription pour notre event. <br><br> Merci de votre participation, et à bientôt dans la faille !";
$to = $parsed_forms["top_email"] . "," . $parsed_forms["jungle_email"] . "," . $parsed_forms["middle_email"] . "," . $parsed_forms["bot_email"] . "," . $parsed_forms["support_email"];

$error_one = sendMail($to, "Confirmation d'inscription", $msg);



$msg_admin = "Une nouvelle équipe vient de s'inscrire à l'event : " . $parsed_forms['nom_equipe'] . "<br><br> Vous trouverez les informations sur le dashboard ADMIN<br><br>";

$error_two = sendMail("thomas.jego@protonmail.com,kyliann94@gmail.com", "Nouvelle inscription", $msg_admin);

header("Location: /page/inscription.html?message=Votre inscription a bien été prise en compte");
exit(0);

/**
 * Récupère les données du formulaire
 * @return array
 */
function getFormsData()
{
    // Get file upload
    if ($_FILES['logo']['size'] == 0) {
        $_POST['have_logo'] = 0;
    } else {
        $_POST['have_logo'] = 1;
    } 
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["logo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["logo"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
    }
    if ($uploadOk == 1) {
        move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file);
    }
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
        '@have_logo' => $_POST['have_logo'],
        'motivation_text' => $_POST['motivation_text'],
        'logo_url' => $target_file,
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

/**
 * Envoie un mail html
 * @param string $to l'adresse mail du destinataire (peut être plusieurs adresses séparées par des virgules)
 * @param string $subject
 * @param string $message
 * @return bool true si le mail a été envoyé, false sinon
 */
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
/**
 * Sauvegarde les données dans la base de données
 * @param array $forms le tableau des données du formulaire (nom_equipe, tag_equipe, ...) en string
 */
function saveToDB($forms)
{
    global $db;
    try {
        $query = "INSERT INTO TWC_INSCRIPTION (nom_equipe, tag_equipe, top_name, top_discord, top_email, jungle_name, jungle_discord, jungle_email, middle_name, middle_discord, middle_email, bot_name, bot_discord, bot_email, support_name, support_discord, support_email, logo_url, motivation_text, have_logo) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssssssssssssssssss", $forms['nom_equipe'], $forms['tag_equipe'], $forms['top_name'], $forms['top_discord'], $forms['top_email'], $forms['jungle_name'], $forms['jungle_discord'], $forms['jungle_email'], $forms['middle_name'], $forms['middle_discord'], $forms['middle_email'], $forms['bot_name'], $forms['bot_discord'], $forms['bot_email'], $forms['support_name'], $forms['support_discord'], $forms['support_email'], $forms['logo_url'], $forms['motivation_text'], $forms['@have_logo']);
        $stmt->execute();
        $stmt->close();
    } catch (Throwable $e) {
        echo "Error during insertion : " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}