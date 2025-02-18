<?php
//votos sim
ini_set('display_errors', 1);
error_reporting(E_ALL);

$con = mysqli_connect('localhost','root','', 'enquete');
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
// Inicia a sessão para controlar a execução
session_start();

// Conexão com o banco de dados
$con = mysqli_connect('localhost','root','', 'tags');
if (!$con) {
    error_log("Conexão falhou: " . mysqli_connect_error());
    exit; // Interrompe a execução caso a conexão falhe
}

// Configura a codificação de caracteres para UTF-8
mysqli_set_charset($con, "utf8");

// Inicia uma transação
mysqli_begin_transaction($con);

try {
 
    $titulo = 'Nosso primeiro ensaio em estúdio';
    $conteudo = 'Sobre o nosso primeiro ensaio como banda em estúdio, na Music Obsession & Red TV, resolvemos escrever um pouco sobre essa experiência magnifica de estar lado a lado com os amigos promovendo um momento de harmonia e união...';

    // Verificar se a página já existe no banco de dados
    $stmt = mysqli_prepare($con, "SELECT id FROM paginas WHERE titulo = ?");
    mysqli_stmt_bind_param($stmt, 's', $titulo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 0) {
        // Página não existe, inserir no banco
        $stmt = mysqli_prepare($con, "INSERT INTO paginas (titulo, conteudo) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $titulo, $conteudo);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Erro ao inserir a página: " . mysqli_error($con));
        }

        $pagina_id = mysqli_insert_id($con);

        // Lista de tags a serem associadas à página
        $tags = ['primeiro ensaio', 'ensaio azul turquesa', 'estudio', 'instrumentos azul turquesa'];

        foreach ($tags as $tag_nome) {
            // Verificar se a tag já existe
            $stmt = mysqli_prepare($con, "SELECT id FROM tags WHERE nome = ?");
            mysqli_stmt_bind_param($stmt, 's', $tag_nome);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 0) {
                // Tag não existe, inserir no banco
                $stmt = mysqli_prepare($con, "INSERT INTO tags (nome) VALUES (?)");
                mysqli_stmt_bind_param($stmt, 's', $tag_nome);
                mysqli_stmt_execute($stmt);
                $tag_id = mysqli_insert_id($con); // Pega o ID da tag inserida
            } else {
                // Tag já existe, pegar o ID
                mysqli_stmt_bind_result($stmt, $tag_id);
                mysqli_stmt_fetch($stmt);
            }

            // Verificar se a associação já existe
            $stmt = mysqli_prepare($con, "SELECT tag_id FROM pagina_tags WHERE pagina_id = ? AND tag_id = ?");
            $stmt = mysqli_prepare($con, "SELECT tag_id FROM pagina_tags WHERE pagina_id = ? AND tag_id = ?");
            if (!$stmt) {
                die("Erro na preparação da consulta: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($stmt, 'ii', $pagina_id, $tag_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 0) {
                // Associar a tag à página
                $stmt = mysqli_prepare($con, "INSERT INTO pagina_tags (pagina_id, tag_id) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt, 'ii', $pagina_id, $tag_id);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Erro ao associar a página com a tag '$tag_nome': " . mysqli_error($con));
                }
            }
        }

        // Marcar que a página foi inserida nesta sessão
        $_SESSION['pagina_primeiro_ensaio_inserida'] = true;
    } else {
        // Página já existe no banco de dados, nenhuma ação necessária
        $pagina_id = null; // Apenas para referência
    }

    // Commit da transação
    mysqli_commit($con);
} catch (Exception $e) {
    // Rollback em caso de erro
    mysqli_rollback($con);
    error_log($e->getMessage());
    exit; // Interrompe a execução em caso de erro
} finally {
    // Fechar a conexão
    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="author" content = "Mailton Lemos">
    <meta name="description" content="Azul Turquesa Website"> <!-- se atentar nos valores das tags, não é freestyle. -->
    <link rel="icon" href="../../assets/img/icone favicon.png"> <!--o valor do favicon é icon--> 
    <title>Nosso primeiro ensaio em estúdio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/style.css" type="text/css">

</head>

<body>

    <header>
            
    <!-- metadados para compartilhamento em redes sociais -->
    <?php 
    $titulo = "Nosso primeiro ensaio em estúdio!";
    $descricao = "Sobre o nosso primeiro ensaio como banda em estúdio, na Music Obsession & Red TV, resolvemos escrever um pouco sobre essa experiência magnifica de estar lado a lado com os amigos promovendo um momento de harmonia e união...";
    $imagem = "../../assets/img/materias/primeiro_ensaio_em_estudio/primeiro-ensaio-em-estudio-azul-turquesa.jpg"; 
    $url = "http://localhost/azulTurquesa_website/app/materias/primeiro_ensaio.php"; // A URL da página atual

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
                    <a href="../../../index.php">Página inicial</a>
                </li>

                <li>
                    <a href="../sobre_nos.php">Sobre nós</a>
                </li>

                <li>
                    <a href="../downloads.php">Downloads</a>
                </li>

                <li>
                    <a href="../letras.php">Letras de nossas músicas</a>
                </li>

                <li>
                    <a href="../fotos.php">Galeria de fotos</a>
                </li>

                <li>
                    <a href="../onde_ouvir.php">Onde nos ouvir</a>
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
                        <form method="GET" action="../../envia_enquete.php">
                            <!-- Opções da enquete -->
                                <input type="radio" name="voto" value="Sim">Sim!<br> <!-- o nome deve ser igual para não poder dar dois votos ao mesmo tempo -->
                                <input type="radio" name="voto" value="Nao">Não!<br>
                                <input type="radio" name="voto" value="Um pouco">Um pouco...<br>
                            <input class="botao" type="submit" value="Votar">
                            <br><hr>
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
        <form action="../../buscar.php" method="GET">
            <label for="tags">Buscar por tags sem acentuações</label>
            <input type="text" name="tags" id="tags" placeholder="Ex: azul turquesa, fotos" required>
            <button type="submit">Buscar</button>
        </form>
        <br>

        <div class="sobre-nos">
        <h1>Nosso primeiro ensaio em estúdio!</h1>

            <img src="../../assets/img/materias/primeiro_ensaio_em_estudio/primeiro-ensaio-em-estudio-azul-turquesa.jpg" alt="Azul Turquesa no seu primeiro ensaio em estúdio 2022" class="center-img"><br>

            <p><b>Soturna Cröstä:</b><br><br>

                "Num calor miserável, perdi o ônibus e tive que ir de aplicativo de corrida para poder chegar a tempo do ensaio, não podíamos perder tempo. O sol estava tão fervente que doía a pele. Chegando na funerária perto do estúdio, encontrei Sidney e depois Efêmera, e então tomamos rumo para o estúdio. Era tarde de sábado e estava de ressaca, pois bebi demasiadamente com meus companheiros de outra banda, a Carnivalia.
                Só lembro que foi difícil, eu tinha marcado dois ensaios seguidos, deveria cantar por quatro horas seguidas. O estúdio é muito bem estruturado e aconchegante e o dono e dona são pessoas muito legais e atenciosos.  Estava me perguntando se conseguiria aguentar até o fim, mas deu tudo certo. A experiência de estar num estúdio pela primeira vez com a banda foi inesquecível. 
                Fomos bem, alguns erros que havíamos percebido antes foram corrigidos em partes, a questão do tempo de Mal encorpado, por exemplo, melhorou bastante, mas ainda faltam algumas coisas como a variação do refrão. Mais um ano esfria da banda Velho também ficou bem melhor, pois já tínhamos nos reunidos para tocar em casa sem bateria, ficamos perdidos assim, mas com Efêmera e Bruna determinando a levada ficou tudo mais fácil. 
                Na primeira metade do ensaio o microfone estava com a altura meio baixa para mim e tive que me curvar, eu não sabia regular ele. Pena de mim, fiquei com dor nas costas, mas perdi a timidez e pedi para regular e então ficou perfeito. Sobre o meu vocal, achei que estava melhorando conforme eu ia cantando. Mas o foco do ensaio foi sincronizar o tempo entre os instrumentos, eu estava lá de complemento. Nós estamos nos esforçando e isso está dando resultados. A ideia é que no segundo ensaio possamos também tocar uma outra música, inédita, que será lançado no primeiro trabalho oficial da banda no futuro próximo". 
            </p><br>

           <p><b>Siel Boná:</b><br><br>

            "Me conectou novamente a música".

           </p><br>
            
            <p><b>Efêmera:</b><br><br>

            "Prazer, me chamo Tamyres, conhecida como Efêmera, toco bateria há 5 anos, é algo que levarei até minha sepultura, bem, música pra mim é resignação, posso sentir a magia de Mr.Crowley como o Ozzy fala no instante em que minhas baquetas esvoaçam sob às caixas, um êxtase profano, "o meu momento"; quando toco posso tirar todos os fantasmas que me asfixia, eu posso ser eu mesma.  
            <br>
            Me inspiro muito em Dave Grohl ex batera do Nirvana e a Luana Dametto da Crypta, curto ouvir Bathory, The Foreshadowing, Katatonia, Windhand, Black Tongue, John Wayne, Pense, Ratos De Porão, Project46, Claustrofobia e Mayhem, e sim, sobre o ensaio com a Azul Turquesa, foi num sábado a tarde, eu estava ouvindo Death no busão quando passei da parada, meus camaradas estavam do outro lado falando "quem pegaria a merda desse busão" e lá estava eu, passando do ponto de ônibus hahahah, no ensaio pegamos os acordes com toda nossa atmosfera brutal e fizemos acontecer, foi insano! 
            <br>
            E termino meu texto dizendo uma frase que carrego criptograficamente em meu crânio: Nolite te bastardes carborundorum ("Não permita que os bastardos reduzam você a cinzas")". 
            <br><br>
            "Nunca deixem que um filho da puta te cuspam a face"!

            </p><br>

            <p><b>Bruna Penazzi:</b><br><br>

            "Bom, foi meu primeiro ensaio da vida, o primeiro de vários. E como era de se esperar, eu estava nervosa pois comecei a tocar o baixo há pouco tempo. Mas quando chegamos lá tudo passou e eu estava apenas dando o meu máximo para tocar na nossa banda, e deu tudo certo. foi incrível, a queda que eu tinha pelo baixo só aumentou e é algo que eu vou levar comigo na vida. É minha primeira banda também, com meus melhores amigos, o que torna tudo isso ainda mais fantástico.
            <br><br>
            Espero que curtam muito o nosso som. 
            <br><br>
            Abraços".
            <br><br>
                    
            <p><b>Pedro Dias:</b><br><br>

            “No primeiro ensaio da Azul Turquesa destruímos cordas e palhetas e conseguimos tirar o som, 10/10”.

            </p><br><br>

            <p>Duas cordas toradas e três palhetas quebradas, esse foi o resumo do dia.</p>
            <br>

            <img src="../../assets/img/materias/primeiro_ensaio_em_estudio/efemera-e-bruna-azul-turquesa.jpeg" alt="Efêmera e Bruna Pênazzi Ex Azul Turquesa" max width="400">
            <img src="../../assets/img/materias/primeiro_ensaio_em_estudio/pedro-e-sidney-azul-turquesa.jpeg" alt="Siel Boná e Pedro Dias Ex Azul Turquesa" max width="400">
            <img src="../../assets/img/materias/primeiro_ensaio_em_estudio/azul-turquesa-2022.jpeg" alt="Azul Turquesa 2022" max width="300">
            <br><br>

            </p>Bônus: uma pequena execução de Mal Encorpado.</p><br>
            <iframe allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" frameborder="0" height="315" src="https://www.youtube.com/embed/Tbr4gh08l_A" title="YouTube video player" width="560"></iframe><br />
            <br>

            Contato: azulturquesabanda@gmail.com<br>

            Links: Linktree<br>

            Instagram: @azulturquesadoomblack
            </p>
            <br>

            <h4>Última atualização: 4 de fevereiro de 2025.</h4>
            </div>

            <br>
        
  
        <!-- ############################################################################################################################################# -->
            <!-- Compartilhamento de posts -->
            <div class="social-share">
                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>" target="_blank" title="Compartilhar no Facebook">
                    <img src="../../assets/img/Estruturais/facebook logo.png" alt="Facebook" style="width: 50px; height: auto;">
                </a>

                <!-- Twitter -->
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($url); ?>&text=<?php echo urlencode($titulo); ?>" target="_blank" title="Compartilhar no Twitter">
                    <img src="../../assets/img/Estruturais/X_logo.jpg" alt="Twitter" style="width: 50px; height: auto;">
                </a>

                <!-- LinkedIn -->
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($url); ?>&title=<?php echo urlencode($titulo); ?>&summary=<?php echo urlencode($descricao); ?>" target="_blank" title="Compartilhar no LinkedIn">
                    <img src="../../assets/img/Estruturais/linkedin logo.png" alt="LinkedIn" style="width: 50px; height: auto;">
                </a>
        </div>

        <!-- formulário para comentário -->
        <br>
        <form action="../comentarios.php" method="POST">
            <label for="nome">Nome:</label><br>
            <input type="hidden" name="pagina_id" value="372"> <!-- Substitua value pelo ID da página atual -->
            <input type="text" name="nome" id="nome" required> <!-- o problema aqui era o redirecionamento da pagina para o processamento do comentario e
            o id correto da página que deve ser colocado manualmente. As tuplas devem bater tanto no banco de dados quanto nos envios do site pra la, não pode haver 
            mais lá e menos aqui e vice-versa -->
            <br>
            <label for="comentario">Comentário:</label><br>
            <textarea name="comentario" id="comentario" rows="4" required></textarea>
            <br>
            <button type="submit">Enviar Comentário</button>
        </form>
            
        <br>
        <hr>

        <!-- exibição dos comentário -->

        <?php
            // Conectar ao banco de dados
            $con = mysqli_connect('localhost','root','', 'tags');
            // Verificar conexão
            if (!$con) {
                die("Conexão falhou: " . mysqli_connect_error());
            }

            // Definir manualmente o ID da página
            $pagina_id = 372; // Substitua pelo ID da página desejada

            // Verificar se o ID da página existe na tabela `paginas`
            $sql = "SELECT id FROM paginas WHERE id = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $pagina_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 0) {
                die("ID da página inválido.");
            }

            mysqli_stmt_close($stmt);

            // Recuperar os comentários associados à página
            $sql_comentarios = "SELECT nome, comentario, data_comentario FROM comentarios WHERE pagina_id = ? ORDER BY data_comentario DESC";
            $stmt_comentarios = mysqli_prepare($con, $sql_comentarios);
            mysqli_stmt_bind_param($stmt_comentarios, 'i', $pagina_id);
            mysqli_stmt_execute($stmt_comentarios);
            $result_comentarios = mysqli_stmt_get_result($stmt_comentarios);

            // Exibir os comentários
            if (mysqli_num_rows($result_comentarios) > 0) {
                echo "<h3>Comentários:</h3>";
                while ($row = mysqli_fetch_assoc($result_comentarios)) {
                    echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>";
                    echo "<strong>" . htmlspecialchars($row['nome']) . "</strong><br>";
                    echo "<small>" . htmlspecialchars($row['data_comentario']) . "</small><br>";
                    echo "<p>" . htmlspecialchars($row['comentario']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>Nenhum comentário encontrado para esta página.</p>";
            }

            mysqli_stmt_close($stmt_comentarios);

            // Fechar a conexão
            mysqli_close($con);
        ?>

        <div class="post-destaque">

        <h3>Postagem em destaque<h3><br>
        <h2><b>Pusilânime: Release Oficial</b></h2><br>
        <p>5 de Janeiro de 2024</p><br>
        <div class="destaque-img">
            <img src="../../assets/img/materias/pusilanime_release/pusilanime-release.jpg" alt="Casa da pólvora" max width="200">
            <p>É com imenso orgulho que a banda paraibana de Doom/Black Metal Azul Turquesa anuncia seu primeiro EP oficial intitulado Pusilânime. 
            A banda com dois anos de existência vem criando progressivamente conhecimento de produção em home studio. O EP foi gravado e produzido no completo faça você mesmo, pelo duo formado pelos paraibanos Mailton "Soturna Cröstä" Lemos e Tamyres "Efêmera" Meireles. A banda já possui três singles e cinco demos lançados.
            O lançamento do EP que conta com quatro faixas e 16 minutos de duração está programado para o dia 4 de janeiro de 2024 à meia noite e meia, horário que a lua entra em minguante.</p>
        </div>
        <br>
        <a href="./pusilanime_release.php">Ler Mais</a>
        </div>

    </main>

   <aside>

   <h3>Arquivo</h3><br>
        
        <h4>Janeiro 2024</h4>
        <br>
        <a href="./pusilanime_release.php" alt="Pusilânime release oficial">Pusilânime: Release Oficial</a>
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
        <a href="./anacoreta.php" alt="Anacoreta lyrics">Anacoreta English Lyrics</a>
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
        <a href="../onde_ouvir.php" alt="Onde nos ouvir">Onde nos ouvir</a>
            <ul>
                <li>Links da banda</li>
                <li>Streaming gratuito e pago</li>
                <li>Várias opções</li>
            </ul>
        <br>
        <br>

        <a href="../letras.php" alt="Letras">Letras de nossas músicas</a>
            <ul>
                <li>Links da banda</li>
                <li>Letras de nossas músicas</li>
                <li>Todas em um único lugar</li>
            </ul>
        <br>
        <br>

        <a href="../fotos.php" alt="Fotos">Galeria de fotos</a>
            <ul>
                <li>Fotos da banda</li>
                <li>Ensaios fotográficos</li>
                <li>Todas as formações</li>
            </ul>
        <br>
        <br>

        <a href="../downloads.php" alt="Downloads">Downloads</a>
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
        <a href="./primeiro_ensaio.php" alt="Primeiro ensaio em estudio">Nosso primeiro ensaio em estúdio</a>
            <ul>
                <li>Curiosidades</li>
                <li>Histórias</li>
                <li>Fotos</li>
            </ul>
        <br>
        <br>
        <a href="../sobre_nos.php" alt="Sobre nos">Sobre nós</a>
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