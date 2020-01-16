<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200114132106 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dck9d8', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'navbar.menu.terms', 'AGB')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dk9d8a', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'menu.hamburger.terms', 'AGB')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dkf9f8', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'menu.footer.terms', 'AGB')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0k9d8ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'menu.hamburger.contact_us', 'Kontakt')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0k9d8d7', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'menu.footer.contact_us', 'Kontakt')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dc1j17', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text.1', 'Die Kosten werden deiner Handy-Rechnung hinzugefügt')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0d1j17a', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text.2', 'Schau unbegrenzt Sportvideos für nur %price% %currency% pro Woche inkl. Mehrwertsteuer an')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e01j17ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text.3', 'Du kannst dein Abo im Bereich Konto jederzeit kündigen')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e1j171ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'annotation_block.text.4', 'Für das Herunterladen von Daten fallen Gebühren gemäß Vertrag oder Paket an')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dc101s', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'wifi.button', 'ABONNIEREN!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0101sba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'buttons.subscribe', 'ABONNIEREN!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0ddf9da', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'offer.landing', 'Abonnement: %price% %currency% inkl. MwSt. / %period% (Beinhaltet unbegrenzt viele Videos)')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc13o', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'lure_block.text', 'Über 40 Sportarten!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc3oa', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'landing.text.pay2watch', 'Klicke auf <span class=\"play-text-link red\">Play</span> <br/> und schaue weiter!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dc3oba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'copyright', '©ORIGINDATA 2020 Alle rechten voorbehouden')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0d3o1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'footer.cookies_disclaimer', 'Wir verwenden Cookies, um sicherzustellen, dass Sie unsere Website optimal nutzen können. Wir verwenden Cookies auch, um sicherzustellen, dass wir Ihnen Werbung zeigen, die für Sie relevant ist. Wenn Sie fortfahren, ohne Ihre Einstellungen zu ändern, gehen wir davon aus, dass Sie dem Empfang aller Cookies auf der 100% Sport-Website zustimmen. Mehr Infos und ändern Ihrer Einstellungen <a href=\"%cookies_settings_url%\">hier</a>.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e03oc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.introduction', 'Wir verwenden Cookies, Pixel und andere Technologien (hier \"Cookies“ genannt), um Ihre IP-Adresse, Browser, Benutzer-ID oder Gerät wiederzuerkennen, mehr über Ihre Interessen zu erfahren und Ihnen wichtige Funktionen und Dienste anzubieten, und für zusätzliche Zwecke darunter:')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e3occ1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.1', '• Sie erkennen, wenn Sie 100% Sport besuchen. Dadurch können wir Ihnen Produktempfehlungen geben, personalisierte Inhalte anzeigen, Sie als 100% Sport-Mitglied erkennen und andere angepasste Funktionen und Dienste anbieten.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd03odcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.2', '• Erhebung von Daten und Diagnosen zur Verbesserung der Inhalte, Produkte und Dienste von 100% Sport.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd3o0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.3', '• Verhindern betrügerischer Aktivitäten.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7c3oe0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.4', '• Verbesserung der Sicherheit.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-73o0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.5', '• Bereitstellung von Inhalten auf 100% Sport-Websites, einschließlich Anzeigen, die für Ihre Interessen relevant sind')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-3od0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.list.6', '• Berichte. So können wir die Qualität unserer Dienste messen und analysieren.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-963o-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.1', 'Mit 100% Sport-Cookies können Sie einige der wichtigsten Funktionen von 100% Sport nutzen. Wenn Sie unsere Cookies beispielsweise blockieren oder auf andere Weise ablehnen, können Sie nicht unsere Produkte kaufen, nicht als 100% Sport-Mitglied erkannt werden und keine 100% Sport-Produkte und -Dienste verwenden, für deren Anmeldung Cookies erforderlich sind.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-93o3-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.2', 'Zugelassene Dritte können auch Cookies setzen, wenn Sie mit 100% Sport-Diensten interagieren. Zu den Dritten zählen Suchmaschinen, Anbieter von Mess- und Analysediensten, soziale Netzwerke und Werbefirmen. Dritte verwenden Cookies, um Inhalte zu liefern, einschließlich Anzeigen, die für Ihre Interessen relevant sind, und um die Wirksamkeit ihrer Anzeigen zu messen.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-3o93-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.text.3', 'Sie können Browser-Cookies über Ihre Browsereinstellungen verwalten. Über die Hilfefunktion der meisten Browser erfahren Sie, wie Sie verhindern können, dass Ihr Browser neue Cookies akzeptiert, wie Sie der Browser benachrichtigen kann, wenn Sie ein neues Cookie erhalten, wie Sie Cookies deaktivieren und wann Cookies ablaufen. Wenn Sie alle Cookies in Ihrem Browser deaktivieren, übertragen weder wir noch Dritte Cookies an Ihren Browser. In diesem Fall müssen Sie jedoch möglicherweise bei jedem Besuch einer Website einige Einstellungen manuell anpassen, und einige Funktionen und Dienste funktionieren möglicherweise nicht.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b3o-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'cookies_settings.title', 'Einstellungen für Cookies')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc19d', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'buttons.join.now', 'Doe nu mee')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dc9dba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'offer.account', 'Bekijk onbeperkt videos van je favoriete sporten en teams! <br> Slechts %price% %currency% pro %period%')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc9da', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'period.week', 'woche')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0d9d1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'buttons.confirm', 'Bestätigen')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e09dc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'buttons.yes', 'Ja')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e9dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'buttons.no', 'Nein')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd09ddcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'messages.action.unsubscribe.confirm', 'Willst du wirklich kündigen?')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd9d0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'buttons.leave', 'Verlasse 100% Sport')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7c9de0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'buttons.unsubscribe', 'Verlasse 100% Sport')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-79d0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'subscription.text.resubscribe', 'Um dich erneut anzumelden, kontaktiere uns bitte')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-9dd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'contact_us.title', 'Kontakt')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-969d-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'contact_us.text.1', 'Hast du ein Problem im 100% Sport? Wir geben dir klare Antworten!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-99d3-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'contact_us.text.2', 'Kontaktiere uns über unser Kontaktformular unten')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9d93-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'contact_us.field.email', 'E-Mail-Adresse')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b9d-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'contact_us.field.comments', 'Kommentare')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-49d8-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'buttons.send', 'SENDEN')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-9d38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'contact_us.field.mandatory', 'Pflichtfelder')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc9d8', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'account.header-text', 'Abo-Info')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1jd', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'about_us.ceo', 'CEO')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dccjda', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', 'about_us.title', 'Over ons')");

//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a1a1daee-efbf-4b38-9693-7cd0e0dcc1ba', '5179fa73-ebd4-11e8-95c4-02bb250f0f22', '', '')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dck9d8'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dk9d8a'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dkf9f8'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0k9d8ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0k9d8d7'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dc1j17'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0d1j17a'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e01j17ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e1j171ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dc101s'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0101sba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0ddf9da'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dcc13o'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dcc3oa'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dc3oba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0d3o1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e03oc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e3occ1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd03odcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd3o0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7c3oe0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-73o0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-3od0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-963o-7cd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-93o3-7cd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-3o93-7cd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b3o-9693-7cd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dcc19d'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dc9dba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dcc9da'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0d9d1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e09dc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e9dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd09ddcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd9d0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7c9de0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-79d0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-9dd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-969d-7cd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-99d3-7cd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9d93-7cd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b9d-9693-7cd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-49d8-9693-7cd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-9d38-9693-7cd0e0dcc1ba'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dcc9d8'");

       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dcc1jd'");
       $this->addSql("DELETE FROM translations WHERE uuid = 'a1a1daee-efbf-4b38-9693-7cd0e0dccjda'");
//       $this->addSql("DELETE FROM translations WHERE uuid = ''");
//       $this->addSql("DELETE FROM translations WHERE uuid = ''");
       // $this->addSql("DELETE FROM translations WHERE uuid = ''");
    }
}
