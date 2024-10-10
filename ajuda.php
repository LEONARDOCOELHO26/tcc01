<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Administrativo - Condomínio</title>
    <script src="https://kit.fontawesome.com/45d9f78a17.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            height: 100vh;
            width: 200px;
            background-color: #333;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }
        .logosidebar {
            text-align: center;
            margin-bottom: 20px;
        }
        .logosidebar img {
            width: 80px;
            height: 80px;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 10px 20px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #444;
        }
        .sidebar a.active {
            background-color: #007BFF;
        }
        .sidebar span i {
            margin-right: 10px;
        }
        .container {
            margin-left: 240px;
            padding: 20px;
            width: calc(100% - 240px);
        }
        .search-bar {
            display: flex;
            margin-bottom: 20px;
        }
        .search-bar input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-bar button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #0056b3;
        }
        .article {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .article h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logosidebar">
            <img src="img/EspaçoCon__1_-removebg-preview.png" alt="Logo">
        </div>
        <a href="admin_home.php" onclick="navigate('Painel')"><i class="fa-solid fa-chart-line"></i> Painel</a>
        <a href="user.php" onclick="navigate('Usuarios')"><i class="fa-solid fa-users"></i> Usuários</a>
        <a href="ajuda.php" onclick="navigate('Ajuda')"><i class="fa-solid fa-question"></i> Ajuda</a>
        <a href="relatorio.php" onclick="navigate('Reservas')"><i class="fa-solid fa-newspaper"></i> Relatórios</a>
        <a href="salas.php"><i class="fa-solid fa-gear"></i> Configuração Salas</a>
        <a href="?logout=true"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sair</a>
    </div>
    <div class="container">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Pesquisar artigos...">
            <button onclick="searchArticles()">Pesquisar</button>
        </div>
        <div class="article" id="article1">
            <h3>Como usar o painel</h3>
            <p>O painel principal do sistema fornece uma visão geral das principais atividades e métricas do condomínio. Ele exibe gráficos e estatísticas sobre reservas de áreas comuns, usuários ativos, notificações recentes e outras informações relevantes para os administradores tomarem decisões informadas.</p>
        </div>
        <div class="article" id="article2">
            <h3>Gerenciando usuários</h3>
            <p>Esta tela permite aos administradores gerenciar os usuários do condomínio. É possível adicionar novos usuários, editar informações de usuários existentes e excluir usuários. A tela também permite a busca por usuários específicos e exibe uma lista de todos os usuários cadastrados, com informações como nome, apartamento e número de telefone.</p>
        </div>
        <div class="article" id="article3">
            <h3>Ajuda</h3>
            <p>A seção de ajuda oferece artigos e guias sobre como utilizar o sistema. Inclui tópicos como o uso do painel, gerenciamento de usuários e geração de relatórios. Esta seção é essencial para novos administradores ou para resolver dúvidas comuns sobre o funcionamento do sistema.</p>
        </div>
        <div class="article" id="article4">
            <h3>Gerar relatórios</h3>
            <p>Nesta tela, os administradores podem gerar relatórios detalhados sobre diversas atividades do condomínio, como reservas de áreas comuns, utilização de serviços, entre outros. Os relatórios podem ser exportados em diferentes formatos (por exemplo, PDF ou Excel) para análise posterior.</p>
        </div>
        <div class="article" id="article5">
            <h3>Configuração Salas</h3>
            <p>Esta seção permite a configuração das áreas comuns do condomínio, como salas de reunião, salões de festas, academias, etc. Os administradores podem adicionar novas áreas, definir horários de funcionamento, capacidade máxima, e outras regras específicas para cada área. É fundamental para garantir que todas as áreas comuns estejam bem gerenciadas e disponíveis para os moradores conforme necessário.</p>
        </div>
        <div class="article" id="article6">
            <h3>Sair</h3>
            <p>O link para sair do sistema encerra a sessão do administrador e redireciona para a página de login. Isso garante que as informações sensíveis e as operações administrativas sejam protegidas, exigindo autenticação para acesso ao sistema.</p>
        </div>
    </div>
    <script>
        function searchArticles() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const articles = document.querySelectorAll('.article');
            articles.forEach(article => {
                const title = article.querySelector('h3').textContent.toLowerCase();
                const content = article.querySelector('p').textContent.toLowerCase();
                if (title.includes(query) || content.includes(query)) {
                    article.style.display = 'block';
                } else {
                    article.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
