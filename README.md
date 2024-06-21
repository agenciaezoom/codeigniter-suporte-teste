# SOULX

Requisitos mínimos:

-   PHP ^7.2
-   MYSQL ^5.4
-   Node ^16.5

Recomendando usar algum serviço de server local como Xampp, WampServer, Laragon, etc.

### Configuração

-   Crie um schema no banco de dados com o nome "soulx" e importe o dump localizado em ./temp/soulx.sql
-   Altere as configurações de banco no arquivo ./application/config/database.php e ./cms/config/database.php
-   Extraia o zip "node_modules" para uma pasta anterior a raiz do projeto
    -   node_modules: xampp/htdocs/node_modules/
    -   soulx: xampp/htdocs/soulx/
-   Execute o camando gulp na raiz do projeto (necessário para processar o less -> css e minificar o js)

### Estrutura

-   Arquitetura HMVC (separado por módulos)
-   Projeto conta com CMS e SITE dependentes entre si

### Acesso ao CMS

Usuário: ezoom
Senha: ezoom@123

## TAREFA

A ideia da tarefa é realizar alguns ajustes e melhorias no projeto, simulando um chamado de suporte. Por isso, os ajustes abaixo precisam ser realizados para que o chamado seja atendido:

-   Envio do formulário de newsletter não está funcionando; uma mensagem de erro é mostrada e o botão de enviar fica estranho.
-   Formulário de contato não está funcionando. Registro não aparece no CMS e não estou recebendo notificação de envio por email.
-   Página de contato está mostrando um erro estranho ao acessá-la.
-   Quando a página de contato estava acessível, não era mostrado o campo de telefone no formulário.
-   Gostaria que ao abrir um pergunta no FAQ, o fundo permanecesse branco e tivesse alguma animação suave na abertura e fechamento.
-   Está aparecendo um erro quando tento buscar registros no módulo de produtos no CMS.
-   Existem alguns campos de youtube e link no módulo de banners do CMS que gostaria de remover porque não usaremos.
-   Estou sentindo falta de uma animação nas seções ao carregar a página inicial.
-   O link para whatsapp está incorreto em todas as páginas e gostaria que abrisse sempre em uma nova guia.
-   Algumas páginas estão desconfiguradas quando tento acessá-las pelo celular.

### Extra

Além dos ajustes acima listados pelo hipotético cliente, melhorias na usabilidade e/ou ajustes visuais e funcionais de forma geral são bem-vindos para uma melhor experiência do usuário e qualidade na entrega da demanda.
