<?php

/**
 * Documentation Page for the Website
 *
 * This script serves as the documentation page for the website, providing insights into the code structure,
 * responsive design implementation, and potential improvements for future development.
 *
 * @package Documentation
 */

include("../config/config.php");
$pageTitle = "Dokumentation";
include("../view/header.php");
?>

<main>
    <article>
        <header>
            <h1>Dokumentation av webbplatsen</h1>
        </header>
        <section>
            <h3>Kodstruktur och designöverväganden</h3>

            <p>Min webbplats för Nättraby Vägmuseum är organiserad med en tydlig och modulariserad kodstruktur för att underlätta underhåll, utveckling och skalbarhet. Jag har strukturerat filerna i olika kataloger baserat på deras funktionella ansvarsområden – till exempel separerar jag konfigurationsfiler, stilark, PHP-skript och vyer i egna mappar. Detta gör koden lättare att navigera och förstå för andra utvecklare eller mig själv i framtiden.</p>
            <p>I config-mappen lagras konfigurationsfiler som config.php, vilken innehåller databasanslutningssträngar och andra globala inställningar. Detta centraliserar konfigurationen och gör det enkelt att göra ändringar som påverkar hela applikationen.</p>
            <p>css-mappen innehåller stilark för att styla webbplatsens utseende. Jag använder separata filer som base.css, components.css, layout.css osv., för att dela upp stilar baserat på deras användningsområden. Detta gör det lättare att underhålla och uppdatera webbplatsens utseende.</p>
            <p>Databasfiler lagras i db-mappen. Här finns .sqlite-databasfiler som innehåller webbplatsens data. Att separera datalagret från applikationslogiken underlättar dataskydd och underhåll.</p>
            <p>Bilder lagras i img-mappen, vilket gör det enkelt att hantera webbplatsens multimediaresurser på ett organiserat sätt.</p>
            <p>I public-mappen finns de faktiska PHP-sidorna som användarna interagerar med. Genom att separera dessa från resten av applikationen (som konfigurationsfiler och bibliotek), skyddas känslig kod och det blir tydligt vilka filer som är tillgängliga för användarna.</p>
            <p>src-mappen innehåller PHP-bibliotek och hjälpfunktioner som används för att interagera med databasen, generera HTML-innehåll och hantera användarautentisering. Detta stödjer en DRY-princip (Don't Repeat Yourself) genom att centralisera återanvändbar kod.</p>
            <p>view-mappen innehåller återanvändbara vyer som headers, footers och navigationsmenyer. Detta förenklar utvecklingen av nya sidor och underhåll av konsistent design över hela webbplatsen.</p>
            <p>När det gäller responsiv design har jag använt CSS media queries och flexibla layouter för att säkerställa att webbplatsen ser bra ut och fungerar väl på olika enheter och skärmstorlekar. Jag testade webbplatsen på mobiler, surfplattor och skrivbordsdatorer för att säkerställa god användarupplevelse överallt.</p>

            <h3>Framtida förbättringar</h3>
            <p>För framtida förbättringar ser jag flera möjligheter. För det första skulle jag vilja implementera en mer dynamisk innehållshantering, kanske genom att införa ett CMS (Content Management System), vilket skulle underlätta uppdateringar av webbplatsens innehåll utan att behöva direkt manipulera databasen eller PHP-koden.För det andra, att förbättra tillgängligheten genom att följa WCAG-riktlinjerna ännu striktare, så att webbplatsen är tillgänglig för alla användare, inklusive dem med funktionsnedsättningar.
            <p>En annan spännande möjlighet skulle vara att skapa ett forum där besökare kan diskutera och dela med sig av sina upplevelser relaterade till vägmuseumet. Detta skulle inte bara främja gemenskapen bland intresserade, utan också ge värdefull feedback och nya idéer till museets utveckling. Dessutom kan integrationen av kommentarsfunktioner på specifika utställningar eller artiklar öka interaktionen och engagemanget hos webbplatsbesökare. Genom att erbjuda besökarna att dela sina tankar och erfarenheter kan vi skapa en mer levande och engagerande webbplats.</p>   <p>Slutligen skulle prestandaoptimeringar, som att implementera bild- och kodminifiering samt lat inläsning av bilder, förbättra laddningstider och användarupplevelsen för besökare med långsamma internetanslutningar. Detta är särskilt relevant i en tid där mobila enheter dominerar och nätverksförhållandena kan variera stort.
            </p>
        </section>
    </article>
</main>

<?php include("../view/footer.php"); ?>
