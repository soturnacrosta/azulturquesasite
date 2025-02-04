<!-- ATENÇÃO!!!! O INDEX POR PADRÃO DO SERVIDOR ONLINE ESTÁ CONFIGURADO PARA FICAR NA RAÍZ DO HTDOCS E NÃO NA PASTA DO PROJETO. SE CERTIFIQUE 
 DE COLOCÁ-LO NO LOCAL DEVIDO. --> 
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//votos sim
$con = mysqli_connect('localhost', 'root', '', 'enquete'); // Dados de conexão para o servidor local
$sql="SELECT quant_votos_sim FROM enquete";
$retorno=mysqli_query($con, $sql);
$dados=mysqli_fetch_assoc($retorno);
$votossim = $dados['quant_votos_sim'];

//votos não
$sql="SELECT quant_votos_nao FROM enquete";
$retorno=mysqli_query($con, $sql);
$dados=mysqli_fetch_assoc($retorno);
$votosnao = $dados['quant_votos_nao'];

//votos um pouco 
$sql="SELECT quant_votos_um_pouco FROM enquete";
$retorno=mysqli_query($con, $sql);
$dados=mysqli_fetch_assoc($retorno);
$votosumpouco = $dados['quant_votos_um_pouco'];

//quantidade de votos
$totalvotos = $votossim + $votosnao + $votosumpouco;
?>


<?php
/*foi utilizado o seguinte passo a passo para que as tags não se repetissem durante os f5:
iniciar sessão apenas uma vez, pois se não controlar a sessão ele contibilizava indefinidademente;
no mysql, resetar o banco de daddos desativando a foreign key:
SET FOREIGN_KEY_CHECKS = 0;  -- Desabilita a verificação de chave estrangeira
DELETE FROM pagina_tags;     -- Exclui os dados relacionados
DELETE FROM paginas;         -- Exclui as páginas
DELETE FROM tags;            -- Exclui as tags
SET FOREIGN_KEY_CHECKS = 1;  -- Reabilita a verificação de chave estrangeira

depois:
DELETE FROM pagina_tags;  -- Exclui as associações de tags com páginas
DELETE FROM paginas;      -- Exclui as páginas
DELETE FROM tags;         -- Exclui as tags

A LINHA:
ALTER TABLE paginas ADD UNIQUE (titulo);

IMPEDE QUE DUPLIQUE OS DADOS;
*/
// Inicia a sessão para controlar a execução
session_start();

// Conexão com o banco de dados
$con = mysqli_connect('localhost', 'root', '', 'tags'); // Dados de conexão para o servidor local

// Verificar se a conexão foi bem-sucedida
if (!$con) {
    error_log("Conexão falhou: " . mysqli_connect_error());
    exit;
}

// Verificar se a página "Homepage" já foi inserida nesta sessão
if (!isset($_SESSION['homepage_inserida'])) {
    // Verificar se a página já existe no banco de dados para evitar duplicidade
    $titulo = 'Homepage';
    $conteudo = 'Homepage do site da Azul Turquesa';
    $tipo = 'homepage';

    $stmt = mysqli_prepare($con, "SELECT id FROM paginas WHERE titulo = ?");
    mysqli_stmt_bind_param($stmt, 's', $titulo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 0) {
        // Inserir a página caso não exista
        $stmt = mysqli_prepare($con, "INSERT INTO paginas (titulo, conteudo, tipo) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sss', $titulo, $conteudo, $tipo);
        if (mysqli_stmt_execute($stmt)) {
            $pagina_id = mysqli_insert_id($con);

            // Lista de tags a serem associadas à página
            $tags = ['homepage do site azul turquesa', 'posts'];

            foreach ($tags as $tag_nome) {
                // Verificar se a tag já existe
                $stmt = mysqli_prepare($con, "SELECT id FROM tags WHERE nome = ?");
                mysqli_stmt_bind_param($stmt, 's', $tag_nome);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 0) {
                    // Inserir a tag se não existir
                    $stmt = mysqli_prepare($con, "INSERT INTO tags (nome) VALUES (?)");
                    mysqli_stmt_bind_param($stmt, 's', $tag_nome);
                    if (mysqli_stmt_execute($stmt)) {
                        $tag_id = mysqli_insert_id($con);
                    }
                } else {
                    // Obter o ID da tag existente
                    mysqli_stmt_bind_result($stmt, $tag_id);
                    mysqli_stmt_fetch($stmt);
                }

                // Associar a página com a tag
                $stmt = mysqli_prepare($con, "INSERT INTO pagina_tags (pagina_id, tag_id) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt, 'ii', $pagina_id, $tag_id);
                mysqli_stmt_execute($stmt);
            }

            // Marcar que a página foi inserida nesta sessão
            $_SESSION['homepage_inserida'] = true;
        }
    }
}

// Fechar a conexão
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="author" content = "Mailton Lemos">
    <meta name="description" content="Azul Turquesa Website"> <!-- se atentar nos valores das tags, não é freestyle. -->
    <link rel="icon" href="./azulTurquesa_website//assets/img/icone favicon.png"> <!--o valor do favicon é icon--> 
    <title>Azul Turquesa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./azulTurquesa_website/assets/css/style.css" type="text/css">

    <style>
        p {
            word-break: break-word; /*para colocar hífens e quebra automatica de linha */
        }

    </style>

</head>

<body>

    <header>

        <!-- metadados para compartilhamento em redes sociais -->
        <?php 
        $titulo = "Azul Turquesa home page";
        $descricao = "Azul Turquesa é uma one man band paraibana de Doom e Black Metal que experimenta novas formas de fazer som a cada lançamento. Explorando o contraste entre a brutalidade e crueza do Black Metal e a beleza e soturnez do Doom Metal.";
        $imagem = "./assets/img/materias/galeria_de_fotos/azul-turquesa-logo-branco.png"; // A URL de uma imagem de destaque
        $url = "localhost/index.php"; // A URL da página atual

        ?>
        <meta property="og:title" content="<?php echo $titulo; ?>">
        <meta property="og:description" content="<?php echo $descricao; ?>">
        <meta property="og:image" content="<?php echo $imagem; ?>">
        <meta property="og:url" content="<?php echo $url; ?>">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="<?php echo $titulo; ?>">
        <meta name="twitter:description" content="<?php echo $descricao; ?>">
        <meta name="twitter:image" content="<?php echo $imagem; ?>">
        
    </header>
        <nav aria-label="Navegação primávia">

            <ul>
                <legend><b>Menu</b></legend>

                <li>
                    <a href="./index.php">Página inicial</a> 
                </li>


                <li>
                    <a href="./azulTurquesa_website/app/sobre_nos.php">Sobre nós</a>
                </li>

                <li>
                    <a href="./azulTurquesa_website/app/downloads.php">Downloads</a>
                </li>

                <li>
                    <a href="./azulTurquesa_website/app/letras.php">Letras de nossas músicas</a>
                </li>

                <li>
                    <a href="./azulTurquesa_website/app/fotos.php">Galeria de fotos</a>
                </li>

                <li>
                    <a href="./azulTurquesa_website/app/onde_ouvir.php">Onde nos ouvir</a>
                </li>
                <br>
                
            </ul>

            <!-- Spotify Embed -->
        <div class="spotify-container">
            <iframe style="border-radius:12px" src="https://open.spotify.com/embed/album/4HJDpFAYVhMTFpUdfIkAco?utm_source=generator" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>        </div>
        
            <div class="list">
            <ul style="list-style: none; padding: 0; text-align: center;">
                <li>
                    <a href="https://www.facebook.com/Azul-Turquesa-100564319187499" target="_blank" style="text-decoration: none; color: inherit;">
                        <img src="https://i.imgur.com/schYrX3.png" alt="Facebook" style="width: 50px; height: auto;">
                        <div style="font-size: 14px; margin-top: 5px;">Facebook</div>
                    </a>
                </li>
                <li>
                    <a href="https://www.youtube.com/channel/UCFNR2eraIieEESKRySyJ1Xw" target="_blank" style="text-decoration: none; color: inherit;">
                        <img src="https://i.imgur.com/rtNeHYe.png" alt="YouTube" style="width: 50px; height: auto;">
                        <div style="font-size: 14px; margin-top: 5px;">YouTube</div>
                    </a>
                </li>
                <li>
                    <a href="https://www.instagram.com/azulturquesadoomblack/" target="_blank" style="text-decoration: none; color: inherit;">
                        <img src="https://i.imgur.com/DLcSzOa.png" alt="Instagram" style="width: 50px; height: auto;">
                        <div style="font-size: 14px; margin-top: 5px;">Instagram</div>
                    </a>
                </li>
            </ul>            
           
            <ul>
                <section class="enquete">
                <fieldset>  
                <h1>Você gostou do novo site?</h1>
                <form method="GET" action="./azulTurquesa_website/envia_enquete.php">
                    <!-- Opções da enquete -->
                        <input type="radio" name="voto" value="Sim">Sim!<br> <!-- o nome deve ser igual para não poder dar dois votos ao mesmo tempo -->
                        <input type="radio" name="voto" value="Nao">Não!<br>
                        <input type="radio" name="voto" value="Um pouco">Um pouco...<br>
                    <input class="botao" type="submit" value="Votar">

                    <h4>Resultado da enquete<h4>
                        <p>Sim! <?php echo $votossim;?></p>
                        <p>Não! <?php echo $votosnao;?></p>
                        <p>Um pouco... <?php echo $votosumpouco;?></p>
                        <p>Total de votos: <?php echo $totalvotos;?></p>
                    </section>

                </form>
                </fieldset>
                <br>
            </ul>

            <iframe style="border: 0; width: 236px; height: 350px;" src="https://bandcamp.com/EmbeddedPlayer/track=1189228237/size=large/bgcol=ffffff/linkcol=0687f5/tracklist=false/transparent=true/" seamless><a href="https://azulturquesa.bandcamp.com/track/nostalgia">Nostalgia by Azul Turquesa</a></iframe>

            <ul>
                <h4>Email para contato:<br> azulturquesabanda@gmail.com</h4>
            </ul>
        
        </div>
     </nav>
    <main>
        <!-- form da busca -->
        <form action="./azulTurquesa_website/buscar.php" method="GET">
            <label for="tags">Buscar por tags:</label>
            <input type="text" name="tags" id="tags" placeholder="Ex: azul turquesa, fotos" required>
            <button type="submit">Buscar</button>
        </form>
        <br>

        <div class="mainpage-container">

            <h2>Sobre nós</h2><br>
            <div class="mainpage-container1">
                <!--a primeira postagem tem sua foto maior que as outras como padrão -->
                <img src="./azulTurquesa_website/assets/img/materias/sobre_nos/soturna-azulTurquesa.jpg" alt="Soturna Crosta da banda de black metal paraibana Azul Turquesa"  width="500" height="auto" class="left-img"><br>
                <p>Azul Turquesa é uma one man band paraibana de Doom e Black Metal que experimenta novas formas de fazer som a cada lançamento. Explorando o contraste entre a brutalidade e crueza do Black Metal e
                    a beleza e soturnez do Doom Metal, Azul Turquesa aborda questões existenciais e depressão, além de romances e histórias de horror.<br>
                    A banda foi originalmente formada no dia 30 de novembro de 2021 quando fora lançada a demo intitulada AZUL TURQUESA (DEMO) contendo as faixas Obsessão (DEMO) e Mal Encorpado (DEMO). Embora formada nessa data, a banda já 
                    viera sendo planejada pelo membro fundador Mailton "Soturna Cröstä" Lemos.<br><br>
                <a href="./azulTurquesa_website/app/sobre_nos.php" alt="Sobre nós">Ler mais</a></p>
            </div>
            <br><br>
          

            <br>
            <h2>Pusilânime: Release Oficial</h2><br>
            <div class="mainpage-container2">
                <img src="./azulTurquesa_website/assets/img/materias/pusilanime_release/pusilanime-release.jpg" alt="Pusilânime capa Azul Turquesa"  width="300" height="auto" class="left-img"><br>
                <p>É com imenso orgulho que a banda paraibana de Doom/Black Metal Azul Turquesa anuncia seu primeiro EP oficial intitulado Pusilânime.

                A banda com dois anos de existência vem criando progressivamente conhecimento de produção em home studio. O EP foi gravado e produzido no completo faça você mesmo, pelo duo formado pelos paraibanos Mailton "Soturna Cröstä" Lemos e Tamyres "Efêmera" Meireles. A banda já possui três singles e cinco demos lançados.

                O lançamento do EP que conta com quatro faixas e 16 minutos de duração está programado para o dia 4 de janeiro de 2024 à meia noite e meia, horário que a lua entra em minguante.

                Em termos de sonoridade e conceito, tudo começou na tentativa de reinventar a roda do Doom/Black Metal. As trilhas sonoras de videogames antigos de terror são a principal inspiração para fazer o som da Azul Turquesa. Os timbres de VIBRATO, PHASER, saturadores de fita, vinil e rádio tomam conta da sonoridade.<br><br>
                <a href="./azulTurquesa_website/app/materias/pusilanime_release.php" alt="Pusilanime Release Oficial">Ler mais</a></p>
            </div>
            <br><br>
           

            
            <br>
            <h2>Nosso primeiro ensaio em estúdio</h2><br>
            <div class="mainpage-container3">
                <img src="./azulTurquesa_website/assets/img/materias/primeiro_ensaio_em_estudio/primeiro-ensaio-em-estudio-azul-turquesa.jpg" alt="Azul Turquesa em seu primeiro ensaio com ex-membros"  width="300" height="auto" class="left-img"><br>
                <p>Soturna Cröstä:

                "Num calor miserável, perdi o ônibus e tive que ir de aplicativo de corrida para poder chegar a tempo do ensaio, não podíamos perder tempo. O sol estava tão fervente que doía a pele. Chegando na funerária perto do estúdio, encontrei Sidney e depois Efêmera, e então tomamos rumo para o estúdio. Era tarde de sábado e estava de ressaca, pois bebi demasiadamente com meus companheiros de outra banda, a Carnivalia. Só lembro que foi difícil, eu tinha marcado dois ensaios seguidos, deveria cantar por quatro horas seguidas. O estúdio é muito bem estruturado e aconchegante e o dono e dona são pessoas muito legais e atenciosos. Estava me perguntando se conseguiria aguentar até o fim, mas deu tudo certo. A experiência de estar num estúdio pela primeira vez com a banda foi inesquecível. Fomos bem, alguns erros que havíamos percebido antes foram corrigidos em partes, a questão do tempo de Mal encorpado, por exemplo, melhorou bastante, mas ainda faltam algumas coisas como a variação do refrão. Mais um ano esfria da banda Velho também ficou bem melhor, pois já tínhamos nos reunidos para tocar em casa sem bateria, ficamos perdidos assim, mas com Efêmera e Bruna determinando a levada ficou tudo mais fácil. Na primeira metade do ensaio o microfone estava com a altura meio baixa para mim e tive que me curvar, eu não sabia regular ele...</br><br>
                <a href="./azulTurquesa_website/app/materias/primeiro_ensaio.php" alt="Primeiro ensaio">Ler mais</a></p>
            </div>
            <br><br>
            

            <br>
            <h2>Anacoreta - English Lyrics</h2><br>
            <div class="mainpage-container4">
                <img src="./azulTurquesa_website/assets/img/materias/anacoreta-english/azul-turquesa-anacoreta.jpg" alt="Azul Turquesa anacoreta capa"  width="300" height="auto" class="left-img"><br>
                <p>Anacoreta/Anchoress
                While I dwell in solitude

                I feel myself ever more

                Shackled to your vague quietude

                Hostage of your fleeting love


                The time which here doesn't run

                Moves the hands of the clock

                Kidnaps our souls into the north

                Immaculate, simple love


                On the blue sky I see stars

                In the light of stars I see us

                In the daytime love your weakness

                In that infinite we're alone


                Amidst the waves seduce me

                In you I found abundant light

                Defeated by its own solitude

                Silently you extend hands


                "Ever since you left me

                Memories are no more than ashes

                Time here has never passed

                To live is to remember dead pain"


                The clock hand that runs not here

                Out there flees the void of feelings

                On the stars I project my cuts

                The planet and its rotations


                The fear that caresses my chest

                A capricious last hope

                Suffering, a narrow path

                A posthumous wait, alliance and ode


                My joyful loves moves me

                I feel nostalgic in pain

                At nine when they inject me with medicine

                Without first saying please


                They call us schizophrenic

                For all we've been through together

                With such academic bullshit

                And their filthy religions


                Our history is not over

                I know you haven't forgotten me

                You've always, always loved me

                Being alone always hurt me


                "Since you have left me

                The days don't have the same colours

                The song of the rooster is gone

                Food does not have taste"

                The clock hand that runs not here

                Out there flees the void of feelings

                On the stars I project my cuts

                The planet and its rotations


                The fear that caresses my chest

                A capricious last hope

                Suffering, a narrow path

                A posthumous wait, alliance and ode


                "I even recall the nights

                When we were alone

                Running afraid of the whip

                But I loved to hear your voice


                From that time of magic

                When we were sedated

                By the sky that shone down

                The violet in our roads...
            
                <br><br>
                <a href="./azulTurquesa_website/app/materias/anacoreta.php" alt="Azul Turquesa Anacoreta English Lyrics">Ler mais</a></p>
            </div>
            <br><br>
                
            <br>
            <h2>Downloads</h2><br>
            <div class="mainpage-container2">
                <img src="./azulTurquesa_website/assets/img/materias/downloads/Pusilanime-Azul-Turquesa.jpg" alt="Pusilânime capa Azul Turquesa"  width="300" height="auto" class="left-img"><br>
                <p>Aqui fica disponível para Download gratuito alguns trabalhos da Azul Turquesa em boa qualidade para todos curtirem.<br><br>
                <a href="./azulTurquesa_website/app/downloads.php" alt="Downloads">Ler mais</a></p>
            </div>
            <br><br>
                <!-- último não leva barra hr -->
            </div>
            
        </div>

        <br><br><br>

        <!-- ############################################################################################################################################# -->

        <!-- compartilhamento de posts -->
        <div class="social-share">
            <!-- Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>" target="_blank" title="Compartilhar no Facebook">
                <img src="./azulTurquesa_website/assets/img/Estruturais/facebook logo.png" alt="Facebook" style="width: 50px; height: auto;">
            </a>

            <!-- Twitter -->
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($url); ?>&text=<?php echo urlencode($titulo); ?>" target="_blank" title="Compartilhar no Twitter">
                <img src="./azulTurquesa_website/assets/img/Estruturais/X_logo.jpg" alt="Twitter" style="width: 50px; height: auto;">
            </a>

            <!-- LinkedIn -->
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($url); ?>&title=<?php echo urlencode($titulo); ?>&summary=<?php echo urlencode($descricao); ?>" target="_blank" title="Compartilhar no LinkedIn">
                <img src="./azulTurquesa_website/assets/img/Estruturais/linkedin logo.png" alt="LinkedIn" style="width: 50px; height: auto;">
            </a>
        </div>
        
    </main>
    
    <aside>

    <h3>Arquivo</h3><br>
    
    <h4>Janeiro 2024</h4>
    <br>
    <a href="./azulTurquesa_website/app/materias/pusilanime_release.php" alt="Pusilânime release oficial">Pusilânime: Release Oficial</a>
        <ul>
            <li>Release Oficial</li>
            <li>Curiosidades</li>
            <li>Fotos</li>
        </ul>
    <br>
    <hr>
    <br>

    <h4>Junho 2023</h4>
    <br>
    <a href="./azulTurquesa_website/app/materias/anacoreta.php" alt="Anacoreta lyrics">Anacoreta English Lyrics</a>
        <ul>
            <li>English lyrics</li>
            <li>Listen</li>
            <li>Art</li>
        </ul>
    <br>
    <hr>
    <br>

    <h4>Agosto 2022</h4>
    <br>
    <a href="./azulTurquesa_website/app/onde_ouvir.php" alt="Onde nos ouvir">Onde nos ouvir</a>
        <ul>
            <li>Links da banda</li>
            <li>Streaming gratuito e pago</li>
            <li>Várias opções</li>
        </ul>
    <br>
    <br>

    <a href="./azulTurquesa_website/app/letras.php" alt="Letras">Letras de nossas músicas</a>
        <ul>
            <li>Links da banda</li>
            <li>Letras de nossas músicas</li>
            <li>Todas em um único lugar</li>
        </ul>
    <br>
    <br>

    <a href="./azulTurquesa_website/app/fotos.php" alt="Fotos">Galeria de fotos</a>
        <ul>
            <li>Fotos da banda</li>
            <li>Ensaios fotográficos</li>
            <li>Todas as formações</li>
        </ul>
    <br>
    <br>

    <a href="./azulTurquesa_website/app/downloads.php" alt="Downloads">Downloads</a>
        <ul>
            <li>Downloads das músicas</li>
            <li>Gratuito</li>
            <li>Boa qualidade</li>
        </ul>
    <br>
    <hr>
    <br>

    <h4>Fevereiro 2022</h4>
    <br>
    <a href="./azulTurquesa_website/app/materias/primeiro_ensaio.php" alt="Primeiro ensaio em estudio">Nosso primeiro ensaio em estúdio</a>
        <ul>
            <li>Curiosidades</li>
            <li>Histórias</li>
            <li>Fotos</li>
        </ul>
    <br>
    <br>
    <a href="./azulTurquesa_website/app/sobre_nos.php" alt="Sobre nos">Sobre nós</a>
        <ul>
            <li>História</li>
            <li>Release</li>
            <li>Poscionamento</li>
        </ul>
    <br>
    <br>
    
    </aside>

    <footer> <p>&copy; 2025 Azul Turquesa. Todos os direitos reservados.</p></footer>

</body>

</html>