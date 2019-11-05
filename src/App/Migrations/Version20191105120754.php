<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191105120754 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-7sh3-43d8-9730-d793bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'footer.cookies_disclaimer', 'We use cookies to ensure that we give you the best experience on our website. We also use cookies to ensure we show you advertising that is relevant to you. If you continue without changing your settings, we’ll assume that you accept to receive all cookies on the 100% Sport website. More info or change your settings <a href=\"%cookies_settings_url%\">here</a>')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-7sh3-9730-d793bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.title', 'Cookies settings')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-43d8-7sh3-d793bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.introduction', 'We use cookies, pixels, and other technologies (collectively, \"cookies\") to recognise your IP address, browser, user ID or device, learn more about your interests, and provide you with essential features and services and for additional purposes, including:')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-43d8-9730-7sh3bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.1', 'Recognising you when you access 100% Sport. This allows us to provide you with product recommendations, display personalised content, recognise you as 100% Sport member, and provide other customised features and services.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-43d8-9730-d7sh3d5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.2', 'Conducting research and diagnostics to improve 100% Sport content, products, and services.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-43d8-9730-d77sh35dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.3', 'Preventing fraudulent activity.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-43d8-9730-d797sh3dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.4', 'Improving security.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-43d8-9730-d7937sh3fk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.5', 'Delivering content, including ads, relevant to your interests on 100% Sport sites.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-43d8-9730-d793b7sh3k7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.6', 'Reporting. This allows us to measure and analyse the performance of our services.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-43d8-9730-d793bd7sh37r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.1', '100% Sport cookies allow you to take advantage of some of 100% Sport essential features. For instance, if you block or otherwise reject our cookies, you will not be able to purchase our products, recognize as 100% Sport member, or use any 100% Sport products and services that require cookies to sign in.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-43d8-9730-d793bd57sh3r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.2', 'Approved third parties may also set cookies when you interact with 100% Sport services. Third parties include search engines, providers of measurement and analytics services, social media networks, and advertising companies. Third parties use cookies in the process of delivering content, including ads relevant to your interests, and to measure the effectiveness of their ads.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7a7sk3-0ebf-43d8-9730-d793bd5d7sh3', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.3', 'You can manage browser cookies through your browser settings. The \"Help\" feature on most browsers will tell you how to prevent your browser from accepting new cookies, how to have the browser notify you when you receive a new cookie, how to disable cookies, and when cookies will expire. If you disable all cookies on your browser, neither we nor third parties will transfer cookies to your browser. If you do this, however, you may have to manually adjust some preferences every time you visit a site and some features and services may not work.')");

        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du477sh3-0ebf-43d8-9730-d793bd1a7sk3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'footer.cookies_disclaimer', 'Używamy plików cookie w celu zapewnienia optymalnej obsługi użytkownika na naszej stronie internetowej. Używamy również plików cookie, aby wyświetlane reklamy były odpowiednie dla użytkownika. Jeśli użytkownik kontynuuje używanie strony internetowej 100% Sport bez zmiany ustawień, przyjmujemy, że użytkownik wyraża zgodę na otrzymywanie wszystkich plików cookie ze strony internetowej 100% Sport. <a href=\"%cookies_settings_url%\">Tutaj</a> można znaleźć więcej informacji na ten temat lub wprowadzić odpowiednie zmiany.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-7sh3-43d8-9730-d793bd1a7sk3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.title', 'Ustawienia plików cookie')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-7sh3-9730-d793bd1a7sk3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.introduction', 'Używamy plików cookie, pikseli i innych technologii (zwane łącznie jako „pliki cookie”) do rozpoznawania adresu IP, przeglądarki lub identyfikatora użytkownika albo urządzenia oraz aby dowiedzieć się więcej na temat zainteresowań użytkownika oraz zapewnić użytkownikowi podstawowe funkcje i usługi, a także w celach dodatkowych, obejmujących:')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-7sh3-d793bd1a7sk3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.1', ' Rozpoznawanie użytkownika podczas uzyskiwania dostępu do sklepu 100% Sport. To pozwala nam oferować rekomendacje produktowe, wyświetlać spersonalizowane treści, rozpoznawać użytkownika jako członka 100% Sport i zapewniać inne dostosowane funkcje i usługi.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-7sh3bd1a7sk3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.2', 'Prowadzenie badań i diagnostyki w celu poprawienia zawartości, produktów i usług 100% Sport.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d7sh3d1a7sk3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.3', 'Zapobieganie oszustwom.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d77sh31a7sk3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.4', 'Poprawę bezpieczeństwa.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d797sh3a7sk3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.5', 'Zapewnianie na stronach 100% Sport materiałów edukacyjnych, w tym reklam, dopasowanych do zainteresowań użytkownika. ')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d7937sh37sk3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.6', 'Raportowanie. To pozwala nam mierzyć i analizować efektywność naszych usług.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bd7sh3k3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.1', 'Pliki cookie 100% Sport pozwalaja na korzystanie z niektórych zasadniczych funkcji 100% Sport. Na przykład, jeżeli użytkownik zablokuje lub w innych sposób odrzuci nasze pliki cookie, użytkownik nie będzie mógł kupować naszych produktów oraz nie będzie rozpoznawany jako członek 100% Sport, a także nie będzie mógł używać jakichkolwiek produktów i usług 100% Sport, które podczas logowania wymagają użycia plików cookie.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bd17sh33', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.2', 'Zatwierdzone strony trzecie mogą także ustawić pliki cookie podczas kontaktowania się z usługami 100% Sport. Strony trzecie obejmują wyszukiwarki, operatorów urządzeń do pomiaru i analizy, sieci mediów społecznościowych i kampanie reklamowe. Strony trzecie używają plików cookie w ramach procesu dostarczania treści, w tym reklam dopasowanych do zainteresowań użytkownika, aby mierzyć skuteczność swoich reklam.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bd1a7sh3', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.3', 'Użytkownik może zarządzać plikami cookie w ustawieniach swojej przeglądarki. Funkcja „Pomoc” w większości przeglądarek informuje użytkowników o tym, jak zabezpieczyć przeglądarkę przed akceptowaniem nowych plików cookie, jak wyłączyć pliki cookie, pliki cookie, a także kiedy pliki cookie wygasają. W przypadku wyłączenia wszystkich plików cookie w przeglądarce uniemożliwi to nam oraz stronom trzecim przenoszenie plików cookie do przeglądarki użytkownika. Wyłączenie plików cookie może spowodować, że użytkownik będzie musiał ustawiać ręcznie pewne właściwości podczas każdego wejścia na stronę internetową, a niektóre funkcje i usługi mogą nie działać.')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-7sh3-43d8-9730-d793bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-7sh3-9730-d793bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-43d8-7sh3-d793bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-43d8-9730-7sh3bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-43d8-9730-d7sh3d5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-43d8-9730-d77sh35dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-43d8-9730-d797sh3dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-43d8-9730-d7937sh3fk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-43d8-9730-d793b7sh3k7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-43d8-9730-d793bd7sh37r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-43d8-9730-d793bd57sh3r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7a7sk3-0ebf-43d8-9730-d793bd5d7sh3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du477sh3-0ebf-43d8-9730-d793bd1a7sk3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-7sh3-43d8-9730-d793bd1a7sk3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-7sh3-9730-d793bd1a7sk3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-7sh3-d793bd1a7sk3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-7sh3bd1a7sk3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d7sh3d1a7sk3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d77sh31a7sk3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d797sh3a7sk3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d7937sh37sk3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bd7sh3k3'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bd17sh33'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bd1a7sh3'");
    }
}
