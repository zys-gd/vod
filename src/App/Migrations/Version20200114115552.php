<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200114115552 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'period.day', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'period.week', 
                'semana');
        ");

        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '94cc88d7-3bd9-4ac3-a8a5-8833f34a9caf', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'offer.landing.3g', 
                'Suscripción: %price% %currency% por %period%');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'dfa82f2d-99e0-48a5-bd59-edb6da2ce1e9', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'wifi.button, buttons.subscribe', 
                'Suscribir');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'e9e5f121-0def-4ab0-ba39-63d564a36ba5', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'offer.landing.3g.1', 
                'Accede a cientos de vídeos deportivos y noticias todos los días. ¡Permanece atento!');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '811eb015-ab85-424c-a9c2-e993267228fe', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'menu.footer.imprint', 
                'Imprimir');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'b5e02805-08e0-42ee-838b-c2f748b1cbf9', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'menu.hamburger.contact_us', 
                'Contacto');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'a89dca86-7ed4-4ae7-a329-1a812f3bd03f', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'landing.text.pay2watch', 
                '¡Haz clic en <span class=\"play-text-link red\">jugar</span><br/> y continúa viendo!');
        ");

        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '27be6b7a-75d6-4218-9604-8346adcb9898', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g', 
                'El servicio de Vídeos constituye un beneficio adicional para el servicio de telecomunicaciones que se ofrece dentro del servicio de valor agregado y es un servicio de suscripción semanal en el que se visualizan vídeos sin compras o anuncios en la aplicación (el \"100% Sport\").');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'bec40d3a-461d-41f7-9bf3-b82d29f81af4', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.1', 
                'El coste por el acceso al Servicio es de 2.99 EUR bruto incl. IVA / diario (se le cobrará 2.99 EUR incl. 1 vez por semana.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '5af073b0-c7dc-4ce8-acc2-ead373074d25', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.2', 
                'El Servicio está disponible para teléfonos móviles u otros dispositivos (Android que utiliza tarjetas SIM en las redes de Vodafone).');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '3c81e72c-3833-4520-bd3c-d6c72efe4bd8', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.3', 
                'El cargo se agregará a su cuenta con su operador (teléfono de suscripción) o se deducirá del saldo de su cuenta (teléfono prepago).');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '0ac3bcd8-b365-4843-a9a3-6b42cec7ae0b', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.4', 
                'Es necesaria una conexión a Internet para usar el contenido multimedia recibido como parte del Servicio. Los cargos por transmisión de datos no están incluidos en la tarifa del servicio. Si el uso del Servicio requiere que descargue datos utilizando la transmisión GSM, el Operador le cobrará estas tarifas de acuerdo con la lista de precios actual del Operador contratado.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'ee0e36d0-c51b-4698-87ae-bf28e14fa809', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.5', 
                'Para realizar un pedido, debe leer el contenido presentado en la página de activación y seguir las instrucciones indicadas en él.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'b2ca190a-06e2-43aa-8835-e15394b61570', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.6', 
                'El registro en el Servicio equivale a la aceptación del Reglamento y la Política de privacidad. Después de registrarse en el Servicio, recibirá un mensaje de bienvenida gratuito confirmando que el registro para el Servicio ha sido exitoso.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '05704a0b-013e-46aa-bf34-91324d8756cd', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.7', 
                'El Servicio se activa por tiempo indefinido hasta que finalice la suscripción. Para finalizar la suscripción, envíe un SMS con STOP STOP al número (0 €)');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'ec16aebe-18f2-4007-8252-66b3112dcfd8', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.8', 
                'El proveedor de servicios y la entidad que oferta el Servicio es: Origindata SAS, Dirección: 14 Bis rue Daru. 75008, inscrita en el registro comercial en París, Francia. bajo FR 48849375662');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'c2d784da-780f-4059-af32-25465389382b', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.9', 
                'Cualquier queja debe enviarse a info-es@mobileinfo.biz / support@origin-data.com o puede llamar al 900 907 274 (cargo local) de lunes a viernes de 09:00 a 17:00.');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '1b61c9de-4eba-4897-aebc-718c8b62e354', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.10', 
                'El Organizador se comunicará con usted por teléfono o correo electrónico que proporcionó. Los Términos y condiciones relacionados con el Servicio (\"T&C\") están disponibles en: http://100sport.tv/terms-and-conditions');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'b7c7691d-8df3-4a74-96f7-3666cb4d770d', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'disclaimer.landing.3g.11', 
                'Al hacer clic en \"Suscribir\", usted reconoce que tiene la obligación de pagar el Servicio a través de su número de teléfono móvil de acuerdo con el intervalo de tiempo anterior. Además, acepta y reconoce los T&C del Organizador, el desempeño inmediato del Servicio, así como la asociada pérdida de su derecho de renuncia existente en un plazo de 14 días. A su vez, también acepta guardar una copia de los T&C del Organizador vigentes en el momento de la celebración del contrato del Servicio, cuyo acceso se le proporcionará (en forma de hipervínculo) en el SMS que recibirá confirmando la conclusión del contrato del Servicio.');
        ");

        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'c95f359a-ef94-4e26-bc8e-2196e07d8142', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'subscription.status.not_subscribe', 
                '¡Bienvenido a 100% Sport! <small>Obtén vídeos ilimitados cada %period% por solo %price%/%currency%</small>');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '10025a10-35c3-45f9-8025-3ecbbdf9d19e', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'offer.account', 
                'Ve vídeos deportivos ilimitados por solo 2.99 EUR al día');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '19b275c8-0c0f-493d-9f88-5145bab746e9', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'offer.landing', 
                'Ve vídeos deportivos ilimitados por solo 2.99 EUR al día');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '161574e9-6b98-4a7e-a85f-296cfd624c13', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'offer.annotation_block', 
                'Ve vídeos deportivos ilimitados por solo 2.99 EUR al día');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                'cd0f7019-db10-47ce-a755-4269afc15220', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'subscription.text.enjoy', 
                '¡Disfruta de videos deportivos ilimitados!');
        ");
        $this->addSql("
            INSERT INTO translations (uuid, language_id, `key`, translation) VALUES (
                '494671bf-35e1-4fe5-8175-abe89459c85a', 
                '5179fb6e-ebd4-11e8-95c4-02bb250f0f22', 
                'buttons.see_all', 
                'Ver todo');
        ");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
