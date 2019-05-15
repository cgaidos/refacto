<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        // On vérifie la validité des paramètres avant de lancer la fonction de traitement du template
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;
        if ($quote) {
            // Il n'y a aucune raison de cloner l'instace
            $tpl->subject = $this->computeText($tpl->subject, $data);
            $tpl->content = $this->computeText($tpl->content, $data);
        } else {
            // On évite ainsi d'avoir un message en doublon
            $tpl->subject = "Vous n'avez pas fourni les éléments nécessaires.";
            $tpl->content = '';
        }
        return $tpl;
    }

    private function computeText($text, array $data)
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote)
        {
            // On retire le caractère souligné du nom de variable pour la cohérence
            $quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            // On donne un nom plus explicite à la variable
            $siteObject = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);

            // La variable $destination fait doublon avec $destinationOfQuote

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary     = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($quoteFromRepository),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($quoteFromRepository),
                        $text
                    );
                }
            }

            // La syntaxe n'est pas claire, on la remplace par un if plus lisible
            if (strpos($text, '[quote:destination_name]') !== false) {
                $text = str_replace('[quote:destination_name]', $destinationOfQuote->countryName, $text);
            }
        }

        // On teste la présence du lien dans le template et non la destination de l'objet quote
        if (strpos($text, '[quote:destination_link]') !== false) {
            $text = str_replace('[quote:destination_link]', $siteObject->url . '/' . $destinationOfQuote->countryName . '/quote/' . $quoteFromRepository->id, $text);
        }

        // On renomme $APPLICATION_CONTEXT en camel case pour rester cohérent
        $applicationContext = ApplicationContext::getInstance();

        $user = (isset($data['user']) and ($data['user'] instanceof User)) ? $data['user'] : $applicationContext->getCurrentUser();
        if ($user) {
            // La syntaxe n'est pas claire, on la remplace par un if plus lisible
            if (strpos($text, '[user:first_name]') !== false) {
                $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
            }
        }

        // On remplace les balises de retour à la ligne par l'équivalent html
        $text = str_replace('[EOL]', '<br>', $text);
        return $text;
    }
}
