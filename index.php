<?php

// On reporte le code présenté dans l'exemple dans index.php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/Entity/Destination.php';
require_once __DIR__ . '/src/Entity/Quote.php';
require_once __DIR__ . '/src/Entity/Site.php';
require_once __DIR__ . '/src/Entity/Template.php';
require_once __DIR__ . '/src/Entity/User.php';
require_once __DIR__ . '/src/Helper/SingletonTrait.php';
require_once __DIR__ . '/src/Context/ApplicationContext.php';
require_once __DIR__ . '/src/Repository/Repository.php';
require_once __DIR__ . '/src/Repository/DestinationRepository.php';
require_once __DIR__ . '/src/Repository/QuoteRepository.php';
require_once __DIR__ . '/src/Repository/SiteRepository.php';
require_once __DIR__ . '/src/Repository/UserRepository.php';
require_once __DIR__ . '/src/TemplateManager.php';

$faker = \Faker\Factory::create();

// On ajoute une balise [EOL] pour soigner la présentation du message
$template = new Template(
    1,
    'Votre voyage avec une agence locale [quote:destination_name]',
    "
[EOL][EOL]Numéro de voyage : [quote:summary_html]
[EOL]Bonjour [user:first_name],[EOL][EOL]

Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name].[EOL]
[quote:destination_link][EOL][EOL]

Bien cordialement,[EOL][EOL]

L'équipe Evaneos.com[EOL]
www.evaneos.com[EOL]
");
$templateManager = new TemplateManager();

$message = $templateManager->getTemplateComputed(
    $template,
    [
        'quote' => new Quote($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $faker->date()),
        'user' => new User($faker->randomNumber(), $faker->firstName, $faker->lastName, $faker->email)
    ]
);
echo $message->subject . "\n" . $message->content;
