<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191011144736 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // english
        //$this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'offer.landing.3g', 'Subscription: %currency% %price% with VAT per %period% <br/> (Includes unlimited videos)')");
        //$this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'offer.landing.3g.1', 'Access to hundreds of sport videos and news every day. Stay tuned!')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5dh49d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g', 'The Games service constitutes an additional benefit to the telecommunications service provided within the value added service and is a weekly subscription service allowing for premium games that don’t have any in-app purchases or ads on it (the “100% Sport”).')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5dl9fy', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.1', 'The cost for the access to the Service is gross %currency% %price% incl. VAT / per %period% (you will be charged %currency% %price% incl. VAT gross 1 times per %period%.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5dlf8d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.2', 'The Service is available for mobile phones or other devices (Android utilizing SIM cards in the networks of T-Mobile.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5ifh7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.3', 'The fee will be added to your account with your operator (subscription phone) or deducted from your account balance (pre-paid phone).')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5vk9fy', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.4', 'An internet connection is necessary to use the multimedia content received as part of the Service. Data transmission charges are not included in the service fee. If the use of the Service requires you to download data using GSM transmission, these fees will be charged by the Operator in accordance with the current price list of the contracted Operator.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5fh9fk', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.5', 'To place an order, you must read the content presented on the Activation Page and follow the instructions indicated on it.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5cnj0f', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.6', 'Registration to the Service is tantamount to acceptance of the Regulations and the Privacy Policy. After registration to the Service, you will receive a free welcome message confirming that the registration for the Service has been successful.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5cm8fj', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.7', 'The Service is activated for an indefinite time until you terminate the subscription. To terminate the subscription, please send an SMS with STOP STOP to number 80425 (0 zł)')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5cj7fu', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.8', 'The service provider and entity providing the Service is: Origindata SAS, Address: 14 Bis rue Daru. 75008, registered in the commercial register in Paris, France. under FR 48849375662')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5chf8f', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.9', 'Any complaints are to be sent to info-pl@mobileinfo.biz / support@origin-data.com or you can call 800 00 7173 (local charge) from Monday to Friday from 09:00 to 17:00.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5f1id8', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.10', 'The Organiser shall contact You via the phone or email You provided. The Terms and Conditions relating to the Service (“T&C”) are available at: <a href=\"/terms-and-conditions\">Terms and conditions</a>')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('aj8s7d54-0ebf-43d8-9730-d793bd5dfj8f', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.11', 'By clicking “Subscribe”, you acknowledge to enter into an obligation to pay for the Service via your mobile number according to above time interval. You furthermore accept and acknowledge the Organiser’s T&Cs, the immediate performance of the Service as well as the associated loss of your otherwise existing right of withdrawal within 14 days. Moreover, you also agree to save a copy of the Organiser’s T&Cs in force at the time of the conclusion of the contract for the Service, access to which will be provided to you (in the form of a hyperlink) in the SMS you will receive confirming the conclusion of the contract for the Service.')");
        //$this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('d8d9s954-0ebf-43d8-9730-d793bd5dfj8f', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'copyright', '©ORIGINDATA 2019 All rights reserved')");

        //$this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7hf854-0ebf-43d8-9730-d793bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'menu.footer.imprint', 'Imprint')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7hf854-0ebf-43d8-5478-d793bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'about_us.title', 'About us')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7hf854-0ebf-5487-9730-d793bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'about_us.name', 'Name')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7hf854-0ebf-d8d8-9730-d793bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'about_us.address', 'Address')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('ad7hf854-5478-43d8-9730-d793bd5dfk7r', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'about_us.vat_number', 'VAT Number')");

        // polish
        //$this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d7931ddj8d7d', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'offer.landing.3g', 'Subskrypcja: %price% %currency% z VAT co %period% <br/> (Obejmuje nieograniczoną liczbę filmów)')");
        //$this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bdd76f5d', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'offer.landing.3g.1', 'Codziennie miej dostęp do wiadomości i setek filmów o sporcie. Bądź na bieżąco!')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('dj9fk454-0ebf-43d8-9730-d793bd5dh49d', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g', 'Serwis Games stanowi świadczenie dodatkowe w ramach usługi o podwyższonej opłacie i jest co tydzień usługą subskrypcyjną, która umożliwia pozwalając na gry premium, w których nie ma żadnych zakupów ani reklam (“100% Sport”).')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('dj8fir54-0ebf-43d8-9730-d793bd5dl9fy', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.1', 'Koszt dostępu do Usługi wynosi %price% %currency% brutto. VAT / co %period% (opłata %price% %currency% z VAT brutto 1 co %period%).')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('fld0d054-0ebf-43d8-9730-d793bd5dlf8d', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.2', 'Serwis dostępny jest na telefony komórkowe bądź inne urządzenia końcowe Android, posiadające kartę SIM w sieci T-Mobile.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('a1kf8d54-0ebf-43d8-9730-d793bd5ifh7r', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.3', 'Opłata zostanie doliczona do Twojego rachunku w u twojego operatora (telefon na abonament) lub odliczona od środków na koncie (telefon na kartę).')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('sks9sd54-0ebf-43d8-9730-d793bd5vk9fy', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.4', 'Połączenie internetowe jest konieczne dla korzystania z otrzymanych w ramach Serwisu treści multimedialnych. Do opłaty za dostęp do Serwisu, nie są wliczone opłaty za transmisję danych. W przypadku gdy korzystanie z Serwisu wymaga pobierania przez Ciebie danych z wykorzystaniem transmisji GSM, opłaty te będą pobierane przez Operatora, zgodnie z aktualnym cennikiem zakontraktowanego Operatora.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('8sus9d54-0ebf-43d8-9730-d793bd5fh9fk', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.5', 'Aby dokonać zamówienia, musisz zapoznać się z treścią przedstawioną na Stronie Aktywacyjnej i podążać za instrukcjami na niej wskazanymi.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('ziq8qd54-0ebf-43d8-9730-d793bd5cnj0f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.6', 'Rejestracja do Serwisu jest jednoznaczna z akceptacją Regulaminu i Polityki Prywatności. Po rejestracji do Serwisu, otrzymasz bezpłatną wiadomość powitalną potwierdzającą, że rejestracja do usługi zakończyła się powodzeniem.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('apa0zd54-0ebf-43d8-9730-d793bd5cm8fj', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.7', 'Usługa jest aktywowana na czas nieokreślony, do czasu aż z niej zrezygnujesz. Aby dokonać rezygnacji należy wysłać SMS o treści STOP STOP na numer 80425 (koszt: 0 zł).')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('qvn9gd54-0ebf-43d8-9730-d793bd5cj7fu', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.8', 'Usługodawcą i podmiotem realizującym dodatkowe świadczenie jest: Origindata SAS, Address: 14 Bis rue Daru. 75008 zarejestrowany w Rejestrze Handlowym w Paris, France Wielka Brytania pod numerem FR 48849375662')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('47fh5d54-0ebf-43d8-9730-d793bd5chf8f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.9', 'Kontakt, w razie reklamacji:  info-pl@mobileinfo.biz / support@origin-data.com, numer telefoniczny: 800 00 7173 (opłata lokalna) od poniedziałku do piątku w godzinach 09:00 – 17:00.')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('49fo3d54-0ebf-43d8-9730-d793bd5f1id8', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.10', 'Organizator będzie kontaktować się z Klientem na podany przez niego numer telefonu lub e-mail. Regulamin usługi: <a href=\"/terms-and-conditions\">Regulamin</a>')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('a1u1ud54-0ebf-43d8-9730-d793bd5dfj8f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'disclaimer.landing.3g.11', 'Poprzez przyciśniecie na przycisk ”Subskrybować” użytkownik wyraźnie potwierdza, że wie, że zamówienie pociąga za sobą obowiązek zapłaty poprzez rachunek telefoniczny według powyżej opisanej częstotliwości Serwisu. Dalej użytkownik potwierdza, że zapoznał się z treścią niniejszego Regulaminu organizatora i go wyraźnie akceptuje, a także zgadza się na natychmiastowe rozpocznie świadczenia Serwisu i związaną z tym utratę jego prawa do odstąpienia od umowy o udostępnianie Serwisu w przeciągu 14 dni. Ponadto, wyrażasz również zgodę na to, że niezwłoczne pobierzesz i zachowasz obowiązującą kopię Regulaminu usługi Organizatora w chwili zawarcia umowy o usługę (potwierdzenie zawarcia umowy), który będzie ci udostępniony (w postaci linka www) w otrzymanym przez ciebie SMS-ie potwierdzającym zawarcie umowy na usługę.')");
        //$this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('dk98d754-0ebf-43d8-9730-d793bd5dfj8f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'dh14iu7d-ebd4-12e8-91c4-02bb25d3dh7e', 'copyright', '©ORIGINDATA 2019 Wszelkie prawa zastrzeżone')");

        //$this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d793bd1dk8fu', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'menu.footer.imprint', 'Odcisk')");
        //$this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du47dj54-0ebf-43d8-9730-d7931dd3j49f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'period.week', 'tydzień')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du4fk8f8-0ebf-43d8-9730-d7931dd3j49f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'about_us.title', 'O nas')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du4a9qo9-0ebf-43d8-9730-d7931dd3j49f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'about_us.name', 'Nazwa')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du4lo7j2-0ebf-43d8-9730-d7931dd3j49f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'about_us.address', 'Adres')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du4kd9d7-0ebf-43d8-9730-d7931dd3j49f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'about_us.vat_number', 'Numer VAT')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du4kd9d7-0ebf-43d8-9730-f8f9a5d3j49f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'buttons.subscribe', 'DOŁĄCZ')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('du4kd9d7-0ebf-43d8-9730-sks8s7d3j49f', '5179fd32-ebd4-11e8-95c4-02bb250f0f22', 'menu.footer.contact_us', 'Skontaktuj się z nami')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d7931ddj8d7d'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d7931dd3j49f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bdd76f5d'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7hf854-0ebf-43d8-9730-d793bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du47dj54-0ebf-43d8-9730-d793bd1dk8fu'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5dh49d'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5dl9fy'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5dlf8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5ifh7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5vk9fy'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5fh9fk'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5cnj0f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5cm8fj'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5cj7fu'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5chf8f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5f1id8'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'aj8s7d54-0ebf-43d8-9730-d793bd5dfj8f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'dj9fk454-0ebf-43d8-9730-d793bd5dh49d'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'dj8fir54-0ebf-43d8-9730-d793bd5dl9fy'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'fld0d054-0ebf-43d8-9730-d793bd5dlf8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1kf8d54-0ebf-43d8-9730-d793bd5ifh7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'sks9sd54-0ebf-43d8-9730-d793bd5vk9fy'");
        $this->addSql("DELETE FROM translations WHERE uuid = '8sus9d54-0ebf-43d8-9730-d793bd5fh9fk'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ziq8qd54-0ebf-43d8-9730-d793bd5cnj0f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'apa0zd54-0ebf-43d8-9730-d793bd5cm8fj'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'qvn9gd54-0ebf-43d8-9730-d793bd5cj7fu'");
        $this->addSql("DELETE FROM translations WHERE uuid = '47fh5d54-0ebf-43d8-9730-d793bd5chf8f'");
        $this->addSql("DELETE FROM translations WHERE uuid = '49fo3d54-0ebf-43d8-9730-d793bd5f1id8'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a1u1ud54-0ebf-43d8-9730-d793bd5dfj8f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7hf854-0ebf-43d8-5478-d793bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7hf854-0ebf-5487-9730-d793bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7hf854-0ebf-d8d8-9730-d793bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'ad7hf854-5478-43d8-9730-d793bd5dfk7r'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du4fk8f8-0ebf-43d8-9730-d7931dd3j49f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du4a9qo9-0ebf-43d8-9730-d7931dd3j49f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du4lo7j2-0ebf-43d8-9730-d7931dd3j49f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du4kd9d7-0ebf-43d8-9730-d7931dd3j49f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du4kd9d7-0ebf-43d8-9730-f8f9a5d3j49f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'du4kd9d7-0ebf-43d8-9730-sks8s7d3j49f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'd8d9s954-0ebf-43d8-9730-d793bd5dfj8f'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'dk98d754-0ebf-43d8-9730-d793bd5dfj8f'");
    }
}
