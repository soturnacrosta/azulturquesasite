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
    // Verificar se a página "Sobre nós" já foi inserida
    $titulo = 'Pusilânime: Release Oficial';
    $conteudo = 'É com imenso orgulho que a banda paraibana de Doom/Black Metal Azul Turquesa anuncia seu primeiro EP oficial intitulado Pusilânime.';

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
        $tags = ['ep', 'pusilânime', 'pusilanime', 'ep azul turquesa', 'musicas', 'album', 'álbum', 'musicas para baixar', 'obsessao', 'cacofonia', 'cid 10 f 19 5'];

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
        $_SESSION['pagina_pusilanime_release_inserida'] = true;
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
    <title>Pusilânime: Release Oficial</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/style.css" type="text/css">

</head>

<body>

    <header>
            
    <!-- metadados para compartilhamento em redes sociais -->
    <?php 
    $titulo = "Pusilânime: Release Oficial";
    $descricao = "É com imenso orgulho que a banda paraibana de Doom/Black Metal Azul Turquesa anuncia seu primeiro EP oficial intitulado Pusilânime.";
    $imagem = "../../assets/img/materias/pusilanime_release/soturna-e-efemera-pusilanime3.jpg"; 
    $url = "http://localhost/azulTurquesa_website/app/materias/pusilanime_release.php"; // A URL da página atual

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
        <h1>Pusilânime: Release Oficial</h1>

        <img src="../../assets/img/materias/pusilanime_release/pusilanime-release.jpg" alt="Casa da pólvora" max width="800" class="center-img">
        <br>
        <p>É com imenso orgulho que a banda paraibana de Doom/Black Metal Azul Turquesa anuncia seu primeiro EP oficial intitulado Pusilânime. 
        <br><br>
        A banda com dois anos de existência vem criando progressivamente conhecimento de produção em home studio. O EP foi gravado e produzido no completo faça você mesmo, pelo duo formado pelos paraibanos Mailton "Soturna Cröstä" Lemos e Tamyres "Efêmera" Meireles. A banda já possui três singles e cinco demos lançados.
        <br><br>
        O lançamento do EP que conta com quatro faixas e 16 minutos de duração está programado para o dia 4 de janeiro de 2024 à meia noite e meia, horário que a lua entra em minguante.
        <br><br>
        Em termos de sonoridade e conceito, tudo começou na tentativa de reinventar a roda do Doom/Black Metal. As trilhas sonoras de videogames antigos de terror são a principal inspiração para fazer o som da Azul Turquesa. Os timbres de VIBRATO, PHASER, saturadores de fita, vinil e rádio tomam conta da sonoridade.
        <br><br></p>
          
        <div class="pusilanime-release">
        <img src="../../assets/img/materias/pusilanime_release/soturna-e-efemera-pusilanime2.jpg" alt="Casa da pólvora" max width="400">
        <br>

            <p>Quatro epopéias compõem esse EP que sintetiza o que aprendemos nesses dois anos de projeto.
            <br><br>
            Aprofundamos ainda mais na estética Horror Doom/Black Metal que estamos trabalhando há tanto tempo e que esperamos o momento certo de trazer para o mundo, mas cada coisa tem seu momento de aparecer.
            <br><br>
            O foco ainda é o horror e o romance, porém deixando um pouco de lado a fantasia tradicional da banda, estamos explorando a dor e psicose que impregna nossas vísceras de modo que não nos tornam meros assassinos ou agressores, mas sim loucos que lutam para manter o próprio controle da situação que estamos. Pusilânime traz uma imersão completa na psicose humana. Aproveitamos nossa imersão em influências do Depressive Black Metal e demos uma lapidada no som. 
            <br><br>

        </div>
            <br>
            <div class="pusilanime-release-fotos">
            <img src="../../assets/img/materias/pusilanime_release/soturna-e-efemera-pusilanime6.jpg" alt="Casa da pólvora" max width="400">
            <img src="../../assets/img/materias/pusilanime_release/soturna-e-efemera-pusilanime7.jpg" alt="Casa da pólvora" max width="400">
            <img src="../../assets/img/materias/pusilanime_release/soturna-e-efemera-pusilanime3.jpg" alt="Casa da pólvora" max width="400">
            <br>
            <img src="../../assets/img/materias/pusilanime_release/soturna-e-efemera-pusilanime.jpg" alt="Casa da pólvora" max width="400">
            <img src="../../assets/img/materias/pusilanime_release/soturna-e-efemera-pusilanime4.jpg" alt="Casa da pólvora" max width="400">
            <img src="../../assets/img/materias/pusilanime_release/soturna-e-efemera-pusilanime5.jpg" alt="Casa da pólvora" max width="400">
        </div>

        <br><br>

        <p>Créditos:<br><br>
        <ul>
        <li>Soturna Cröstä: Vocais, guitarras, baixo e teclas;</li>

        <li>Efêmera: bateria;</li>

        <li>Eduardo Vermgod: introdução CID 10 F19.5.</li>

        <li>Fotografias: @kaaiof (Instagram).</li>

        <li>Capa: @ink_coral (Instagram).</li>
        </ul>

        <br>Letras: <a href="../letras.php">clique aqui.</a><br> 
        Para ouvir: <a href="https://linktr.ee/azulturquesa"> clique aqui.</a><br> <!-- para links externos adicionar o https -->
        </p>

        </div>

        <br>Última atualização: 4 de fevereiro de 2025.

  
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
            <input type="hidden" name="pagina_id" value="376"> <!-- Substitua value pelo ID da página atual -->
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
            $pagina_id = 376; // Substitua pelo ID da página desejada

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