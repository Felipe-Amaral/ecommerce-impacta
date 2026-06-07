# Documentacao do Projeto

Data da analise: 2026-06-07

## Visao geral

Este projeto e um e-commerce para grafica online, com marca visual voltada para a **Uriah Criativa**. A aplicacao combina loja, catalogo, checkout, acompanhamento de pedidos, revisao de arte final, atendimento online, blog, portfolio e painel administrativo.

O objetivo do sistema e permitir que um cliente escolha materiais graficos, selecione variacoes, envie observacoes ou arquivo de arte, finalize o pedido, acompanhe pagamento/producao e converse com a equipe da grafica durante o processo.

## Stack

- Backend: Laravel 12.
- Linguagem: PHP com requisito Composer `^8.2`; imagem Docker usa PHP-FPM.
- Frontend: Blade, Vite e Tailwind CSS 4.
- Banco: MySQL 8.4 no Docker; testes usam SQLite em memoria.
- Cache/sessao/fila: Redis no ambiente local; `array`/SQLite nos testes.
- E-mail local: Mailpit.
- Tempo real: Laravel Reverb/Pusher para chat de pedido.
- Autenticacao social: Laravel Socialite com Google, Facebook e GitHub.
- Containers: Nginx, PHP-FPM, Node/Vite, MySQL, Redis, Mailpit e Reverb.

## Como rodar

Na raiz do projeto:

```bash
docker compose up -d --build
docker compose exec -T app php artisan migrate --seed --force
```

Acessos principais:

- Loja: `http://localhost:8080`
- Login: `http://localhost:8080/entrar`
- Painel admin: `http://localhost:8080/painel`
- Mailpit: `http://localhost:8025`
- MySQL externo: `localhost:3307`
- Redis externo: `localhost:6380`
- Reverb externo: `localhost:8081`
- Vite/HMR: `localhost:5173`

Para hot reload:

```bash
docker compose up -d node
```

Credenciais seedadas:

- Admin: `admin@graficaimpacta.local` / `password`
- Cliente: `cliente@graficaimpacta.local` / `password`

## Estrutura do repositorio

- `docker-compose.yml`: orquestra os servicos locais.
- `docker/`: Dockerfile PHP, Nginx e configuracoes PHP.
- `app/`: aplicacao Laravel.
- `app/app/Http/Controllers/`: controllers publicos, autenticacao, conta e admin.
- `app/app/Models/`: modelos Eloquent.
- `app/app/Services/`: regras de negocio de carrinho, pedido, pagamento e frete.
- `app/app/Enums/`: status de pedido, pagamento, producao, metodo de pagamento e arte.
- `app/database/migrations/`: schema do banco.
- `app/database/seeders/`: dados de demonstracao.
- `app/resources/views/`: views Blade da loja, conta, admin e componentes.
- `app/tests/`: testes feature/unit.
- `docs/`: documentacao de apoio do projeto.

## Modulos implementados

### Loja publica

Rotas publicas principais:

- `/`: home com banners, categorias e produtos em destaque.
- `/catalogo`: listagem de produtos com busca e filtro por categoria.
- `/catalogo/{slug}`: pagina de produto com variacoes, configuracao e upload opcional de arte.
- `/carrinho`: carrinho em sessao.
- `/checkout`: checkout com dados do cliente, endereco, frete e pagamento.
- `/checkout/frete/cotacoes`: endpoint JSON para cotacoes de frete.
- `/pedido/{order}/sucesso`: pagina assinada de sucesso do pedido.
- `/contato`: formulario institucional.
- `/blog`: conteudos com categorias, tags, SEO e posts relacionados.
- `/portfolio`: cases com categorias, metricas, galeria e SEO.
- `/servicos`, `/quem-somos`, `/orcamento`: paginas institucionais.

### Autenticacao e area do cliente

- Login e cadastro em `/entrar` e `/cadastro`.
- Login social em `/auth/social/{provider}`.
- Dashboard do cliente em `/minha-conta`.
- Lista e detalhe de pedidos em `/minha-conta/pedidos`.
- Upload de arte final por item do pedido.
- Chat do pedido para alinhar arte, prazo, cobranca e entrega.

### Painel administrativo

Rotas protegidas por usuario autenticado e checagem `is_admin`:

- `/painel`: dashboard operacional.
- `/painel/pedidos`: lista com filtros por status, pagamento, producao e arte pendente.
- `/painel/pedidos/{order}`: detalhe do pedido, itens, pagamentos, artes, historico e chat.
- Atualizacao manual de workflow do pedido.
- Revisao tecnica de arquivos de arte.
- Cadastros de categorias, produtos, variacoes e banners.
- Blog: posts, categorias e tags.
- Portfolio: projetos e categorias.
- Contatos recebidos pelo formulario/site/chat offline.
- Atendimento online: visitantes ativos, jornadas, sessoes e mensagens em tempo quase real.

### Atendimento online

O projeto possui dois tipos de chat:

- Chat do pedido: vinculado a um pedido, com canal privado `orders.{id}.chat`, polling e Reverb quando configurado.
- Chat flutuante do site: acompanha visitantes, paginas acessadas, sessoes abertas e mensagens offline.

O chat flutuante registra:

- token de visitante;
- usuario logado quando houver;
- URL atual, landing page, referencia e titulo da pagina;
- idioma, timezone, tamanho de tela e metadados do navegador;
- jornada recente por pagina;
- sessoes de chat e mensagens;
- contatos offline convertidos em `contact_messages`.

## Fluxos principais

### Compra

1. Cliente navega na home ou catalogo.
2. Abre um produto e seleciona uma variacao.
3. Pode informar configuracoes, observacoes e enviar arquivo de arte.
4. Item entra no carrinho em sessao via `CartService`.
5. Checkout coleta dados do cliente, cobranca, entrega, frete e pagamento.
6. `OrderPlacementService` cria pedido, itens, pagamento inicial, historico e mensagem inicial de chat.
7. `PaymentCheckoutService` tenta iniciar Mercado Pago quando configurado; senao usa fluxo manual.
8. Carrinho e limpo e cliente vai para gateway ou pagina de sucesso.

### Frete

1. `CheckoutShippingQuoteController` recebe CEP.
2. `ShippingQuoteService` monta contexto de envio com peso estimado por categoria/variacao.
3. Se o driver for `melhor_envio`, tenta cotacao na API.
4. Em falha ou sem token, cai para `LocalShippingQuoteProvider`.
5. Se retirada estiver ativa, adiciona `pickup_counter` como primeira opcao.
6. A opcao selecionada recalcula o total do carrinho.

### Pagamento

1. Pedido nasce com `pending_payment` e pagamento `pending`.
2. Driver `manual`: pedido permanece aguardando baixa manual.
3. Driver `mercadopago`: cria preferencia de checkout e redireciona o cliente.
4. Webhook `/webhooks/mercado-pago` valida token simples quando configurado.
5. O gateway sincroniza pagamento, atualiza `payments`, `orders`, `paid_at`, status e historico.

### Arte final e producao

1. Pedido criado com item em `pending_file`.
2. Cliente envia arte pelo detalhe do pedido.
3. Item passa para `file_sent`.
4. Admin revisa o arquivo.
5. Status possiveis: enviado, em revisao, aprovado, ajuste solicitado ou rejeitado.
6. Se todos os itens estiverem aprovados, o pedido vai para producao aprovada.
7. Admin atualiza workflow para impressao, acabamento, envio, entrega etc.

### Feature adicionada hoje: aviso automatico no chat ao revisar arte

Ao atualizar a revisao tecnica de uma arte no painel admin, o sistema cria automaticamente uma mensagem no chat do pedido com:

- item revisado;
- novo status da arte;
- observacoes da equipe quando informadas;
- metadados do arquivo, item e status.

Isso melhora o fluxo operacional porque o cliente nao precisa procurar o historico tecnico: a decisao da pre-impressao aparece diretamente no canal de conversa do pedido.

Arquivo principal: `app/app/Http/Controllers/Admin/ArtworkFileReviewController.php`.

### Evolucao: aviso automatico no chat ao atualizar workflow

Ao usar o bloco **Atualizar status do pedido** no painel admin, o sistema tambem cria uma mensagem no chat do pedido com:

- status comercial do pedido;
- status do pagamento;
- etapa de producao;
- observacoes da equipe quando informadas;
- metadados dos status anteriores e novos.

Esse aviso cobre mudancas como pagamento confirmado, pedido em producao, impressao, acabamento, envio e entrega.

Arquivo principal: `app/app/Http/Controllers/Admin/OrderController.php`.

Teste atualizado: `app/tests/Feature/PrintShopOperationalFlowTest.php`.

## Modelo de dados

### Catalogo

- `categories`: categorias comerciais do catalogo.
- `products`: produtos com SEO, tipo, lead time, preco base e especificacoes JSON.
- `product_variants`: variacoes com atributos, preco, promocao, prazo, peso e estoque opcional.
- `product_images`: imagens por produto e opcionalmente por variacao.
- `home_banners`: banners da home com tema, CTAs, agenda de exibicao e imagem.

### Cliente e autenticacao

- `users`: clientes e admins; campo `is_admin` define acesso ao painel.
- `addresses`: enderecos de usuario.
- `social_accounts`: vinculos com Google, Facebook ou GitHub.
- `sessions`: sessoes Laravel.

### Carrinho

- `CartService` usa sessao (`storefront_cart_v1`) como fonte ativa.
- `carts` e `cart_items` existem para persistencia futura, mas nao sao o fluxo principal atual.

### Pedidos

- `orders`: cabecalho do pedido, cliente, totais, enderecos, frete, status e metadados.
- `order_items`: itens congelados do pedido com produto, variacao, preco, configuracao e status de producao.
- `payments`: pagamentos por pedido, provider, metodo, transacao e payload do gateway.
- `artwork_files`: arquivos de arte por item, checklist, metadata, revisor e status.
- `order_status_histories`: trilha de alteracoes de status.
- `order_messages`: chat do pedido com leitura por cliente/admin.

### Conteudo

- `blog_categories`, `blog_tags`, `blog_posts`, `blog_post_tag`.
- `portfolio_categories`, `portfolio_projects`.

Ambos possuem suporte a rascunho/publicado, destaque, SEO, OG tags, canonical, noindex e contagem de views.

### Atendimento e contato

- `contact_messages`: contatos institucionais e mensagens offline do chat.
- `live_visitors`: visitante ativo no site.
- `live_visitor_page_views`: jornada de paginas.
- `live_chat_sessions`: sessoes de atendimento online.
- `live_chat_messages`: mensagens entre visitante e admin.

## Servicos importantes

- `CartService`: adiciona, atualiza, remove e resume itens do carrinho.
- `OrderPlacementService`: cria pedidos, itens, arquivos, pagamentos e historico em transacao.
- `OrderNumberGenerator`: gera numeros `GI-YYYYMMDD-XXXXXX`.
- `PaymentCheckoutService`: escolhe driver de pagamento.
- `MercadoPagoPaymentGateway`: preferencia de checkout, retorno e webhook.
- `ShippingQuoteService`: orquestra frete e retirada.
- `LocalShippingQuoteProvider`: simulacao local de Correios/transportadora.
- `MelhorEnvioShippingQuoteProvider`: cotacao real via Melhor Envio.
- `UiStatus`: centraliza labels, icones e tons visuais de status.

## Configuracoes de ambiente

Principais variaveis em `app/.env.example`:

- `APP_URL`: URL base usada em callbacks e assets.
- `DB_*`: conexao MySQL.
- `SESSION_DRIVER`: `database` no ambiente local.
- `BROADCAST_CONNECTION`: `reverb` para chat em tempo real.
- `REVERB_HOST`, `REVERB_PORT`, `REVERB_SCHEME`: host publico usado pelo navegador para conectar no websocket.
- `REVERB_BROADCAST_*`: host interno usado pelo container PHP para publicar eventos no container Reverb.
- `REVERB_CLIENT_*`: host explicitamente enviado ao widget do chat no navegador.
- `STOREFRONT_PAYMENT_DRIVER`: `manual` ou `mercadopago`.
- `STOREFRONT_PAYMENT_SUCCESS_REDIRECT`: `gateway` ou `success_page`.
- `MERCADOPAGO_ACCESS_TOKEN`, `MERCADOPAGO_WEBHOOK_TOKEN`.
- `STOREFRONT_SHIPPING_DRIVER`: `local` ou `melhor_envio`.
- `MELHOR_ENVIO_TOKEN`, `MELHOR_ENVIO_BASE_URL`, `MELHOR_ENVIO_SANDBOX`.
- `STOREFRONT_PICKUP_*`: retirada no balcao.
- `STOREFRONT_SOCIAL_PROVIDERS`: providers sociais habilitados.

## Testes existentes

Suite principal:

```bash
docker compose exec -T app php artisan test
```

Coberturas relevantes:

- Cadastro e login.
- Blog publico/admin.
- Fluxo operacional completo da grafica: carrinho, frete, checkout, upload de arte, revisao admin e workflow.
- Chat de pedido cliente/admin e bloqueio de acesso indevido.

## Pontos de atencao

- A interface usa a marca **Uriah Criativa**, mas alguns emails, credenciais e nomes tecnicos ainda usam `graficaimpacta.local`.
- Autorizacao admin e feita diretamente nos controllers com `abort_unless(auth()->user()?->is_admin, 403)`.
- Carrinho ativo e em sessao; tabelas de carrinho ainda nao sao usadas.
- Pagamento e frete possuem fallback local/manual, bom para demo e desenvolvimento.
- Uploads de arte usam disco `public`; em producao, revisar storage, backups e limites.
- `app/README.md` foi substituido por um resumo do projeto para evitar o README padrao do Laravel.

## Proximos incrementos recomendados

1. SLA de producao: alertas para pedidos parados em pre-impressao, impressao ou acabamento.
2. Cupom/desconto: regra comercial simples por codigo no checkout.
3. Reprovacao guiada de arte: motivos padronizados, checklist obrigatorio e instrucoes para novo envio.
4. Persistencia de carrinho por usuario: usar `carts` e `cart_items` para recuperar carrinhos abandonados.
5. Rastreio de entrega: codigo de rastreio, transportadora e historico de eventos.
6. Permissoes administrativas: policies/middleware para separar atendente, producao, marketing e admin geral.
