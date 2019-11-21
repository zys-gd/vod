<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191119110947 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // general kazakh
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9fd8', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'lure_block.text', '40-тан астам спорт түрлері қамтылған!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9k9d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'offer.landing.3g.1', 'Күн сайын жүздеген спорттық видеолар мен жаңалықтарды көру мүмкіндігі. Бізбен бірге қалыңыз!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25djk9d7', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'menu.footer.contact_us', 'Бізбен байланысыңыз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dk9d7d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'contact_us.title', 'Бізбен байланысыңыз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb2k9d7f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'menu.footer.terms', 'Ережелер мен шарттар')");

        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9a7q', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'annotation_block.text.1', 'Бағасы ұялы телефоныңыз шотына қосып қойылады')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dja7qd', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'annotation_block.text.2', 'Күніне %price% теңгеге шексіз спорттық бейнелерді көріңіз. Күн сайын жүздеген спорттық бейнелер мен жаңалықтарға қол жетімділік. Бізбен бірге қалыңыз!')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25da7q8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'annotation_block.text.3', 'Жазылысты «Менің аккаунтым» бөлімінде кез келген уақытта тоқтатуға болады')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25a7qf8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'annotation_block.text.4', 'Деректерді жүктеу үшін десте мөлшерлемесіне сәйкес төлемақы алынады')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb2a7q9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'menu.footer.faq', 'ЖҚС')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bba7qj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'copyright', '©ORIGINDATA 2019 Барлық құқықтар сақталған')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02ba7qdj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.title', 'ЖҚС')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02a7q5dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.q.1', '100% sports қызметіне жазылысымды қалай тоқтатуға болады?')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-0a7q25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.a.1', '100% sports жазылысты қызмет болып табылады. Егер жазылысты тоқтатпасаңыз, ол әр апта немесе әр күні (теңшеуге сәйкес) автоматты түрде жаңаланады. Сіз қалаған уақытта жазылысты тоқтатуыңыз мүмкін. Бұл үшін қызметіндегі жеке мәзіріңізге (дисплейдің жоғарғы оң жағындағы белгіше) кіріп, «100% sports қызметінен шығу» сілтемесін басыңыз. Ұялы байланыс операторыңызға қарай, жазылысты СМС жіберу арқылы тоқтатуыңыз мүмкін.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-a7qb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.q.2', 'Барлық видеоларды қай жерде көрсем болады?')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-9a7q-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.a.2', 'Жақсы көрген спорт видеоларыңызды көріп рақаттануыңыз үшін <a href=\"%home_url%\" class=\"text-danger\">басты бет</a> бастапқы бетімізге кіріп, видеолардың біреуін таңдаңыз. Басты мәзірден кіріп бүкіл каталогымызды қарап шығуыңыз мүмкін (тақырыпаты немесе санаты бойынша іздеу арқылы).')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-a7q4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.q.3', 'Мен спорттық ойындардың бірін жүктеп алдым, бірақ оны смартфоныма орната алмай тұрмын, не болып жатыр?')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-1a7q-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.a.3', 'Біріншіден, Google Play ішінде түрлі дүкендерден алынған қолданбаларды орнатуға рұқсат етілгенін қамтамасыз етіңіз: құрылғыңызда «Теңшеу» белгішесін басып, «Қауіпсіздік» мәзірінен «Құрылғыны басқару» тарауын таңдаңыз да, «Белгісіз қорлардан алынған қолданбаларды орнатуға рұқсат беру» опциясын қосыңыз. Сондай-ақ, жинақтауышыңызда жеткілікті бос жер бар екенін тексеріп қойғаныңыз жөн. Егер бұл мәселе шешілмесе, бізбен кідіртпей байланысыңыз.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-a7q8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.q.4', '100% sports қызметінде видеоларды шолу және одан ойындарды жүктеу қауіпсіз бе?')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ea7q-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.a.4', 'Иә, 100% sports қызметі ұсынатын контенттің барлығы мобильді құрылғыңыз бен дербес деректеріңіз үшін мүлдем қауіпсіз.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-a7q4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.q.5', 'Мен 100% sports қызметіне жазылдым. Егер жазылысты жойсам, қанша уақытқа дейін видеоларды көре аламын?')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8da7q-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.a.5', '100% sports қызметіне соңғы төленген мерзімнің аяғына дейін кіруіңіз мүмкін. Мәселен, күнделікті жазылыс болған жағдайда жазылыс жаңаланып болған болса, барлық видеоларды сол күннің ақырына дейін көруіңіз мүмкін болады. Немесе егер сіз әрапталық жазылысқа ие болып, жазылысыңыз соңғы рет дүйсенбі күні жаңаланған болса, жексенбі күніге дейін (7 күн) қызметті пайдалануыңыз мүмкін.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8a7q0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.q.6', 'Мобильді сайт менің аккаунтым мен 100% sports қызметіне жазылысымды анықтай алмай жатыр.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51da7qd0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'faq.a.6', '3G немесе Wi-Fi қосылымыңыз дұрыс істеп тұрғанын тексеріңіз. Егер дұрыс істеп тұрған болса, бірақ бұл мәселе шешілмесе, бізбен байланысып, сол мәселе туралы ақпаратты, телефон нөміріңізді, ұялы байланыс операторыңыздың атын және құрылғыңыз жөнінде жазып жіберіңіз.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51a7q9d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.title', '100% sports қызметінің ережелері мен шарттары')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('5a7qd9d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.text.description', 'Бұл ережелер («Ережелер мен шарттар»), бір жағынан, сіз және, екінші жағынан, (бұдан былай 100% sports қызметі болып аталатын) ORIGINDATA Ltd компаниясы арасындағы заңды келісімнің арнайы шарттары болып табылады.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('a7q8d9d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_1.title.1', 'Мәні')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj8a9s', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_1.text.1',  '100% sports мобильді құрылмаларға ойындар ұсынатын мобильді қызмет болып табылады.100% sports осы қызметті ұсынады: 100% sports қызметінің <a href=\"%home_url%\">мобильді сайтында</a> жазылысты растаған кезде автоматты түрде белсендірілетін күнделікті немесе әрапталық жазылыс.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25d8a9sd', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_2.title.1', 'Шарттар')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb258a9s8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_2.text.1',  'Бұл қызмет тек %country% ішіндегі пайдаланушыларға ғана қолжетімді. Бұл қызметті пайдалану үшін оператордың рұқсатын алуыңыз шарт.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb28a9sf8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_3.title.1', 'Қызмет сипаттамасы')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb8a9s9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_3.title.1.1', 'Жазылыс')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02b8a9sj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_3.text.1',  'Бұл жазылыс қызметі сізге телефоныңыздан 100% sports қызметінің каталогынан әр апта шексіз спорттық видеоларды көру мүмкіндігін береді және бұл қызметтің бағасы %price% (+WAP қосылымы үшін төлемақы) болып табылады. 100% sports қызметіне жазылысыңыздың мерзімі өтпеген болса, сіз әр күні немесе әр апта сайын шексіз көлемдегі контентті көре аласыз.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-028a9sdj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_3.text.2',  'Жазылыстық қызметке қосылып, видеолар мен ойындарды ашуға рұқсат алу үшін 100% sports қызметінің мобильді веб-сайтында жазылу батырмасын басыңыз. Жазылысыңыз расталған соң сізді веб-сайтымыздың бастапқы бетіне бағыттаймыз және сіз ол жерде 100% sports қызметінің каталогындағы шексіз спорттық видеолар мен ойындардан рахаттанасыз.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-08a9s5dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_3.text.3',  'Күнделікті немесе әрапталық жазылысыңыз, одан өзіңіз бас тартпасаңыз, автоматты түрде жаңаланады. Дербес мәзіріңізден «100% sports қызметінен шығу» сілтемесін басу арқылы қалаған уақытта жазылысты тоқтатуыңыз мүмкін. 100% sports қызметі жазылысты кез келген уақытта тоқтату құқығын өзіне қалдырады.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-8a9s25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_4.title.1', 'Ақпарраттық қолдау')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-8a9s-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_4.text.1',  'Бізбен байланысыңыз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-8a9s-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_5.title.1', 'Төлемдер')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-8a9s-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_5.text.2',  'Факті бойынша төлейтін пайдаланушы болсаңыз, бағасы ұялы телефоныңыз шотына қосып қойылады. Егер алдымен төлейтін пайдаланушы болсаңыз, жазылысты және/немесе сатып алуды ақырына дейін бітіру үшін алдын ала төленген картаңызда жеткілікті сома бар екенін тексеріңіз.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d88a9s-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_5.text.3',  'Төлемақылар ұялы байланыс операторына қарай түрлі болуы мүмкін. Нақты баға жазылысқа немесе сатып алуға дейін қызмет көрсету шарттарымен бірге көрсетіледі.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8a9s0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_5.text.1',  'Осы жазылыстық қызметтің жалпы құны әрбір бетте жарнамаланады және сол жарнама арқылы 100% sports қызметтеріне тіркелуге болады. Келісілген қызмет үшін төлемақының жалпы немесе ішінара сомалары ұялы байланыс операторыңыз белгілеген тәртіпке сәйкес апта немесе күн сайын алынады. Алайда, жазылыстың ең жоғары бағасы бір күнге немесе бір аптаға %price% болады.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('518a9sd0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_5.text.4',  '100% sports веб-сайтына 3G немесе 4G ұялы байланысы арқылы кіру үшін қосымша навигациялық төлемақы алынуы мүмкін. Бұл ақылардың мөлшері ұялы телефон операторы мен сіз өзара келіскен тарифтер мен шарттарға байланысты болады, сондықтан 100% sports қызметі мұндай шығындар үшін жауапты болып саналмайды.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('58a9s9d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_6.title.1', 'Қызметке қол жеткізу')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('8a9sd9d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_6.text.1',  'Тек заңды жауапкершілікті көтеру жасына жеткен тұлғалар мен кәмелетке толмаған әрекетті (14 жастан асқан) тұлғалар ғана 100% sports қызметтеріне өтінім беріп, оларды келісімшарт бойынша пайдалануы тиіс. Кәмелетке толмаған әрекетті емес тұлғалар 100% sports қызметтерін ата-анасының немесе заңды қамқоршыларының рұқсатымен ғана пайдалануы тиіс. Ата-аналары мен заңды қамқоршылары қарауындағы тұлғалардың іс-әрекеттеріне кез келген жағдайда жауапты болады. 100% sports ешқандай жағдайда заңды қамқоршының орнын баса алмайды, сондықтан ұялы байланыс желісінің иесі барлық салдар үшін жауапты болып есептеледі.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9f99', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_6.text.2',  '100% sports қызметін пайдалану үшін сізде барлық қажетті ресурстар (мобильді құрылғы, бағдарламалық жасақтама, Интернет байланысы) болуы керек. 100% sports жөндеу, түзету және техникалық қызмет көрсету жұмыстарын орындау үшін өз қызметтерін тоқтатып қоюы мүмкін. Сондай-ақ, 100% sports нақты қызметтерді немесе өнімдерді уақытша не үнемі өзгертіп қоюы мүмкін.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj999d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_7.title.1', 'Пайдаланушының жауапкершілігі')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj998d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_7.text.1',  '100% sports көрсеткен қызметінің өз бақылауынан тыс түрде пайдаланылуы нәтижесінде немесе оған тиісті болмаған себептерден орын алған барлық залал немесе жарақат үшін жауапты болып табылмайды. Сізден 100% sports қызметтерін бұрыс пайдаланбау және үшінші тараптардың заңды құқықтарын, соның ішінде сауда белгісі құқықтарын, патенттерге, авторлық құқық және басқа зияткерлік және өнеркәсіптік меншікке қатысты құқықтарын құрметтеу талап етіледі.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25d99f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_7.text.2',  'Сондай-ақ, сіз 100% sports қызметтерін коммерциялық мақсаттарда және осы «Ережелер мен шарттарда» сипатталған кез келген басқа орынсыз мақсаттарда пайдалануға рұқсат берілмейді деген шартқа өз келісіміңізді растайсыз.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25999f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_8.title.1', '100% sports қызметінің жауапкершілігі')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb299j9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_8.text.1',  '100% sports қызметі үшінші тараптардың сайтындағы сілтеме арқылы кіруге болатын Веб / Wap / мобильді интернет сайттарының техникалық қолжетімділігі немесе контенті үшін жауапты емес. Сондықтан, 100% sports қызметі үшінші тараптардың веб-сайттарында көрсетілген өнімдерді, қызметтерді, контентті, ақпаратты, деректерді, файлдарды және кез келген түрдегі материалдарды мақұлдамайды және оларға қолдау көрсетпейді. Сондай-ақ, 100% sports қызметі өзіне байланысты емес сілтемеленген сыртқы сайттардағы контент пен қызметтердің сапасын, заңдылығын, сенімділігін және пайдалылығын бақыламайды және оларға жауапты емес.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb99dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_8.text.2',  '100% sports сұралған өнімдер немесе қызметтер 100% sports қызметіне жатқызып болмайтын себептерден (соның ішінде, видеоңыз оператордың байланыс ақаулықтарына немесе мобильді құрылғыңыз конфигурациясына қатысты техникалық мәселелерге байланысты жүктелмеген жағдайлар) дұрыс жеткізілмеген не көрсетілмеген жағдайда жауапты болып табылмайды.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02b995dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_8.text.3',  'Егер сіз кез келген заңсыз материалдарды көрсеңіз, дереу бізбен байланысыңыз.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-029925dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_9.title.1', 'Зияткерлік пен өнеркәсіптік меншікке қатысты құқықтар')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-099b25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_9.text.1',  '100% sports мобильді порталындағы барлық контент, ойындар, дизайн және бағдарламалық кодтар авторлық құқықпен толығымен қорғалған: егер сізде керекті рұқсат болмаса, кез-келген элементтің көшірмесін жасауға, оны таратуға және сатуға толық тыйым салынады.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-99bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_9.text.2',  'Сонымен қатар, 100% sports веб-сайтындағы барлық белгілер (логотиптер, шрифттер, белгішелер және т.б.) 100% sports қызметіне немесе үшінші тараптарға тиесілі, сондықтан оларды арнайы рұқсатсыз көшіруге немесе таратуға толық тыйым салынады. Сондай-ақ, 100% sports қызметінің келісімінсіз осы «Ережелер мен шарттардың» барлығын немесе бір бөлігін көшіруге, көбейтуге немесе қолдануға тыйым салынады.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-9599-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_9.text.3',  '100% sports қызметі өзінің мобильді веб-сайтында бар болған кез келген контентті, қызметтерді немесе қызметтік бағдарламаларды біржақты жою, қосу немесе өзгерту құқығын өзіне қалдырады.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-9994-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_10.title.1', 'Дербес деректер')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-99c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_10.text.1', '1998 жылда қабылданған «Деректерді қорғау туралы» заңға сай, пайдаланушы деректерді бақылайтын компанияға келесі мекенжайы бойынша хат жіберу арқылы өзінің кез келген дербес деректерін алу, түзету және/немесе оларға қарсы наразылығын көрсету құқығына ие: 3rd Floor, 207 Regent street, London W1B 3HH, UK.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-1199-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_11.title.1', 'Өзгертулер')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-1998-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_11.text.1', '100% sports қызметі осы «Ережелер мен шарттарды», бағаларды және жазылысқа тиісті нені болса да өзгерту құқығын құқығын өзіне қалдырады.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-ebd4-99e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_12.title.1', 'Компания туралы ақпарат')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-eb99-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'terms.block_12.text.1', 'Аты: ORIGINDATA Ltd <br> Мекенжайы: 3rd Floor, 207 Regent street, London W1B 3HH, UK. <br> ҚҚС төлеушісінің нөмірі: GB 250 2427 42 <br> Компанияның тіркеу нөмірі: 10294628')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-e994-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'landing.text.pay2watch', '<span class=\"play-text-link red\">Oйнату</span> түймесін басыңыз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d9d0-99d4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'contact_us.text.1', '100% sports қызметі туралы сұрақтарыңыз бар ма? Біз сізге анық жауап береміз! Төмендегі байланыс үлгімізді пайдаланып бізбен байланысыңыз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d999-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'contact_us.text.2', 'Төмендегі байланыс нысанын қолдана отырып, бізбен хабарласыңыз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d8d990-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'contact_us.field.email', 'Электрондық пошта мекенжайы')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'contact_us.field.comments', 'Пікірлер')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25d1sj9d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'contact_us.field.mandatory', '*Міндетті өрістер')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25dsj9dd', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'buttons.send', 'ЖІБЕРУ')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25sj9d8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'contact_us.user.title', 'Пайдаланушы')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25dj9a0s', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'account.header-text', 'Жазылым туралы ақпарат')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25d9a0sd', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'subscription.status.subscribed', '100% sports қызметіне жазылдыңыз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb259a0s8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'account.text.subscribed', 'Сіз 100% спорттың бірісіз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb29a0sf8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'account.text.leave', 'Қызметтен шығып кетуді қаласаңыз, төмендегі сілтемені басыңыз.')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb9a0s9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'subscription.status.unsubscribed', '100% sports қызметіне жазылысыңыз жойылды')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02b9a0sj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'offer.account', 'Күніне тек %price% теңгеден шексіз спорттық видеоларды көріңіз')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-029a0sdj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'buttons.join.now', 'ТӨЛЕУСІЗ ҚОСЫЛУ')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25dj91l9', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.15157409-49a4-4823-a7f9-654ac1d7c12f', 'Кеңінен таралған видеолар')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25dj1l9d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.244b3d58-fb86-4ba1-b221-9a0f3916306d', 'Жекпе-жек')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25d1l98d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.4ec02ca3-779f-454a-8280-f59cd9600c2a', 'Гольф')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb251l9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.51d606c2-ab33-44ec-8098-05994b0e75e5', 'Экстремалды спорт түрлері')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb21l99f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.70b66027-8e25-400b-8e44-cc4dc399f350', 'Жеңіл атлетика')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb1l9j9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.74558a73-902b-46cc-ab90-48a104585d38', 'Мотоспорт түрлері')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02b1l9dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.986e13fa-9dfe-42e6-92f2-2cff6f60b42e', 'Крикет')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-021l95dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.ab6ad264-bd64-4fd0-9010-9c8e503a1ad3', 'Футбол')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-01l925dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.bb215764-2aad-4ca5-83cf-78f841a230e8', 'Іріктеу ойындары')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-1l9b25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.d25f4027-8e25-400b-8e44-cc4dc39fg52f', 'Қысқы спорт түрлері')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-91l9-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.dbeb8e19-ea7f-4443-970d-b113ecf819d9', 'Теннис')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-1l94-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.e14034f0-ac4d-4925-94d4-cbf9d61fae93', 'Басқа спорт түрлері')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11l9-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.f49a555c-d10e-45c6-a6cb-de09cb59658f', 'Баскетбол')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-1l98-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.games', 'Спорт ойындары')");
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-e1l9-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', 'category.title', 'Санат')");

//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('51d899d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '', '')");

        // beeline kazakh
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25d1djf8', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', 'buttons.subscribe', 'ТӨЛЕУСІЗ ҚОСЫЛУ')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25d2jf8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', 'buttons.confirm', 'ЖАЗЫЛЫСТЫ РАСТАУ')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bbk9d79f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', 'offer.landing', 'Күніне тек %price% теңгеден шексіз спорттық видеоларды көріңіз - Айрықша ұсыныс: БІРІНШІ КҮНІ ТЕГІН! Жаңа пайдаланушылар үшін тек бір рет жарамды')");

//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dj9z1q', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25djz1qd', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25dz1q8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb25z1qf8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb2z1q9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bbz1qj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bz1qdj9f8d', '5179f17c-ebd4-11e8-95c4-02bb25d8f6d4', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");


        // general english faq.title
        $this->addSql("INSERT INTO translations (uuid, language_id, `key`, translation) VALUES ('5179fd32-ebd4-11e8-95c4-02bb250f0s8s', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', 'faq.title', 'FAQ')");

        // beeline english
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bb2djf8d8d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', 'buttons.subscribe', 'JOIN FOR FREE')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02b4jf8d9f8d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', 'buttons.confirm', 'CONFIRM SUBSCRIPTION')");
        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8d9d0-ebd4-11e8-95c4-02bk9d7j9f8d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', 'offer.landing', 'Watch unlimited sport videos for only %price% %currency% per %period% - Exclusive offer: FIRST DAY FREE! Valid only once for new users')");

//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8dz1q-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51d8z1q0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51dz1qd0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('51z1q9d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('5z1qd9d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
//        $this->addSql("INSERT INTO translations (uuid, language_id, carrier_id, `key`, translation) VALUES ('z1q8d9d0-ebd4-11e8-95c4-02bb25dj9f8d', '5179f17c-ebd4-11e8-95c4-02bb250f0f22', '3de06e41-f889-4f95-aac3-ebd31d9d7dg4', '', '')");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dj9fd8'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dj9k9d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25djk9d7'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dk9d7d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb2k9d7f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25d1djf8'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25d2jf8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bbk9d79f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb2djf8d8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02b4jf8d9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bk9d7j9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '5179fd32-ebd4-11e8-95c4-02bb250f0s8s'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dj9a7q'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dja7qd'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25da7q8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25a7qf8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb2a7q9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bba7qj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02ba7qdj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02a7q5dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-0a7q25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-a7qb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-9a7q-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-a7q4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-1a7q-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-a7q8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ea7q-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-a7q4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8da7q-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8a7q0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51da7qd0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51a7q9d0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '5a7qd9d0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'a7q8d9d0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dj8a9s'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25d8a9sd'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb258a9s8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb28a9sf8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb8a9s9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02b8a9sj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-028a9sdj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-08a9s5dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-8a9s25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-8a9s-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-8a9s-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-8a9s-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d88a9s-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8a9s0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '518a9sd0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '58a9s9d0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '8a9sd9d0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dj9f99'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dj999d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dj998d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25d99f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25999f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb299j9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb99dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02b995dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-029925dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-099b25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-99bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-9599-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-9994-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-99c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-1199-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-1998-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-99e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-eb99-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-e994-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-99d4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d999-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d990-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dj9z1q'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25djz1qd'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25dz1q8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb25z1qf8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bb2z1q9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bbz1qj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8d9d0-ebd4-11e8-95c4-02bz1qdj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8dz1q-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d8z1q0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51dz1qd0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51z1q9d0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '5z1qd9d0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = 'z1q8d9d0-ebd4-11e8-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb25d1sj9d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb25dsj9dd'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb25sj9d8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb25dj9a0s'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb25d9a0sd'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb259a0s8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb29a0sf8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb9a0s9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02b9a0sj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-029a0sdj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb25dj91l9'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb25dj1l9d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb25d1l98d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb251l9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb21l99f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02bb1l9j9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-02b1l9dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-021l95dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-01l925dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-95c4-1l9b25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-91l9-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11e8-1l94-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-11l9-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-ebd4-1l98-95c4-02bb25dj9f8d'");
        $this->addSql("DELETE FROM translations WHERE uuid = '51d899d0-e1l9-11e8-95c4-02bb25dj9f8d'");
    }
}
