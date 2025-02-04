<?php
//votos sim
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
// Verificar se a conexão foi bem-sucedida
if (!$con) {
    error_log("Conexão falhou: " . mysqli_connect_error());
    exit;
}

// Verificar se a página "Onde nos ouvir" já foi inserida nesta sessão
if (!isset($_SESSION['onde_nos_ouvir_inserida'])) {
    // Verificar se a página já existe no banco de dados
    $stmt = mysqli_prepare($con, "SELECT id FROM paginas WHERE titulo = ?");
    $titulo = 'Onde nos ouvir';
    mysqli_stmt_bind_param($stmt, 's', $titulo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 0) {
        // Inserção da página "Onde nos ouvir"
        $conteudo = 'Onde ouvir a Azul Turquesa';
        $tipo = 'links';
        
        $stmt = mysqli_prepare($con, "INSERT INTO paginas (titulo, conteudo, tipo) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sss', $titulo, $conteudo, $tipo);

        if (mysqli_stmt_execute($stmt)) {
            $pagina_id = mysqli_insert_id($con);
            
            // Lista de tags a serem associadas à página
            $tags = ['onde ouvir azul turquesa', 'links turquesa', 'onde achar azul turquesa', 'azul turquesa spotify', 'azul turquesa youtube', 'links', 'spotify', 'youtube'];
            
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
                if (!mysqli_stmt_execute($stmt)) {
                    error_log("Erro ao associar a página com a tag '$tag_nome': " . mysqli_error($con));
                }
            }

            // Marcar que a página foi inserida nesta sessão
            $_SESSION['onde_nos_ouvir_inserida'] = true;
        } else {
            error_log("Erro ao inserir a página: " . mysqli_error($con));
        }
    } else {
        error_log("A página 'Onde nos ouvir' já existe no banco de dados!");
    }
} else {
    error_log("A página 'Onde nos ouvir' já foi inserida nesta sessão!");
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
    <link rel="icon" href="../assets/img/icone favicon.png"> <!--o valor do favicon é icon--> 
    <title>Onde nos ouvir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
</head>

<body>

    <header>

    <!-- metadados para compartilhamento em redes sociais -->
    <?php 
    $titulo = "Galeria de fotos";
    $descricao = "Logos, ícones, ensaios e bastidores da trajetória da Azul Turquesa.";
    $imagem = "../assets/img/materias/galeria_de_fotos/primeira-formacao-azul-turquesa.jpeg"; // A URL de uma imagem de destaque
    $url = "http://localhost/azulTurquesa_website/app/onde_ouvir.php"; // A URL da página atual

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
                    <a href="../../index.php">Página inicial</a>
                </li>

                <li>
                    <a href="./sobre_nos.php">Sobre nós</a>
                </li>

                <li>
                    <a href="./downloads.php">Downloads</a>
                </li>

                <li>
                    <a href="./letras.php">Letras de nossas músicas</a>
                </li>

                <li>
                    <a href="./fotos.php">Galeria de fotos</a>
                </li>

                <li>
                    <a href="./onde_ouvir.php">Onde nos ouvir</a>
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
                        <form method="GET" action="../envia_enquete.php">
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
        <form action="../buscar.php" method="GET">
            <label for="tags">BBuscar por tags sem acentuações</label>
            <input type="text" name="tags" id="tags" placeholder="Ex: azul turquesa, fotos" required>
            <button type="submit">Buscar</button>
        </form>
        <br>

        <h1>Onde nos ouvir</h1>

        <div class="links-containers">
            <h3>Spotify</h3>
            <iframe style="border-radius:12px" src="https://open.spotify.com/embed/album/4HJDpFAYVhMTFpUdfIkAco?utm_source=generator" width="40%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
            
            <h3>Youtube</h3>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/uym7d-FZ4WQ?si=rhpxzUCC26LmLACr" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            
            <h3>Deezer</h3>
            <iframe title="deezer-widget" src="https://widget.deezer.com/widget/dark/track/2944263121" width="550" height="300" frameborder="0" allowtransparency="true" allow="encrypted-media; clipboard-write"></iframe>
            
            <h3>Apple Music</h3>
            <iframe allow="autoplay *; encrypted-media *; fullscreen *; clipboard-write" frameborder="0" height="440" style="width:100%;max-width:550px;overflow:hidden;border-radius:10px;" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-storage-access-by-user-activation allow-top-navigation-by-user-activation" src="https://embed.music.apple.com/br/album/anacoreta-single/1686812424"></iframe>
            
            <h3>Soundcloud</h3>
            <iframe width="40%" height="300" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/1423181056&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"></iframe><div style="font-size: 10px; color: #cccccc;line-break: anywhere;word-break: normal;overflow: hidden;white-space: nowrap;text-overflow: ellipsis; font-family: Interstate,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Garuda,Verdana,Tahoma,sans-serif;font-weight: 100;"><a href="https://soundcloud.com/user-262121079-817891411" title="Azul Turquesa" target="_blank" style="color: #cccccc; text-decoration: none;">Azul Turquesa</a> · <a href="https://soundcloud.com/user-262121079-817891411/espelho-de-cristal" title="Espelho De Cristal" target="_blank" style="color: #cccccc; text-decoration: none;">Espelho De Cristal</a></div>
            
            <h3>Bandcamp</h3>
            <iframe style="border: 0; width: 50%; height: 120px;" src="https://bandcamp.com/EmbeddedPlayer/track=1189228237/size=large/bgcol=ffffff/linkcol=0687f5/tracklist=false/artwork=small/transparent=true/" seamless><a href="https://azulturquesa.bandcamp.com/track/nostalgia">Nostalgia by Azul Turquesa</a></iframe>
            
            <br>
            <p><b>Sabia que você pode baixar gratuitamente as músicas da Azul Turquesa em boa qualidade? Clique abaixo!</b>
                <a href="./downloads.php" alt="Página de Download">Página de Downloads</a> 
            </p>
        </div>

          <!-- ############################################################################################################################################# -->

        <!-- compartilhamento de posts -->
        <div class="social-share">
            <!-- Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>" target="_blank" title="Compartilhar no Facebook">
                <img src="../assets/img/Estruturais/facebook logo.png" alt="Facebook" style="width: 50px; height: auto;">
            </a>

            <!-- Twitter -->
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($url); ?>&text=<?php echo urlencode($titulo); ?>" target="_blank" title="Compartilhar no Twitter">
                <img src="../assets/img/Estruturais/X_logo.jpg" alt="Twitter" style="width: 50px; height: auto;">
            </a>

            <!-- LinkedIn -->
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($url); ?>&title=<?php echo urlencode($titulo); ?>&summary=<?php echo urlencode($descricao); ?>" target="_blank" title="Compartilhar no LinkedIn">
                <img src="../assets/img/Estruturais/linkedin logo.png" alt="LinkedIn" style="width: 50px; height: auto;">
            </a>
        </div>      

        <br>
        <hr>
    
        <!-- formulário para comentário -->
        <br>
        <form action="comentarios.php" method="POST">
            <label for="nome">Nome:</label><br>
            <input type="hidden" name="pagina_id" value="235"> <!-- Substitua value pelo ID da página atual -->
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
            $pagina_id = 235; // Substitua pelo ID da página desejada

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

    </main>

        <aside>

        <h3>Últimos posts</h3><br>

        <a href="./sobre_nos.php" alt="Sobre nós">Sobre nós</a>
            <ul>
                <li>História da banda</li>
                <li>Curiosidades</li>
                <li>Membros</li>
            </ul>
        <br>
        <hr>
        <br>

        <a href="./downloads.php" alt="Downloads">Downloads</a>
            <ul>
                <li>Downloads das músicas</li>
                <li>Gratuito</li>
                <li>Boa qualidade</li>
            </ul>
        <br>
        <hr>
        <br>

        <a href="./onde_ouvir.php" alt="Onde nos ouvir">Onde nos ouvir</a>
            <ul>
                <li>Links da banda</li>
                <li>Streaming gratuito e pago</li>
                <li>Várias opções</li>
            </ul>
        <br>
        <hr>
        <br>

        <a href="./letras.php" alt="Letras">Letras de nossas músicas</a>
            <ul>
                <li>Links da banda</li>
                <li>Letras de nossas músicas</li>
                <li>Todas em um único lugar</li>
            </ul>
        <br>
        <hr>
        <br>

        <a href="./fotos.php" alt="Fotos">Galeria de fotos</a>
            <ul>
                <li>Fotos da banda</li>
                <li>Ensaios fotográficos</li>
                <li>Todas as formações</li>
            </ul>
        <br>
        <hr>
        <br>
        
        </aside>

    <footer> <p>&copy; 2025 Azul Turquesa. Todos os direitos reservados.</p></footer>

</body>

</html>