# E-commerce para Gráfica (Laravel + Docker)

Base inicial de um e-commerce para gráfica com foco em arquitetura de produção:

- Documentação consolidada do projeto: [`docs/DOCUMENTACAO_DO_PROJETO.md`](docs/DOCUMENTACAO_DO_PROJETO.md)
- Docker Compose (Nginx, PHP-FPM, MySQL, Redis, Mailpit)
- Laravel (última versão instalada via Composer no setup)
- Catálogo com categorias, produtos e variantes
- Carrinho em sessão
- Checkout com criação de pedido
- Cobrança integrada (Mercado Pago / fallback manual)
- Frete por CEP (Melhor Envio / fallback local) + retirada no balcão
- Upload de arte final por item + checklist técnico + revisão/admin
- Atendimento online, chat do pedido, blog, portfólio e contatos

## Stack usada

- PHP `8.5` (`php:8.5-fpm-bookworm`)
- MySQL `8.4` (`mysql:8.4`)
- Laravel skeleton `v12.11.2`
- Laravel framework `v12.53.0`

## Subir o projeto

```bash
docker compose up -d --build
docker compose exec -T app php artisan migrate --seed --force
```

Acesse:

- Loja: `http://localhost:8080`
- Login: `http://localhost:8080/entrar`
- Painel admin: `http://localhost:8080/painel`
- Mailpit: `http://localhost:8025`
- MySQL: `localhost:3307`
- Redis: `localhost:6380`

## Hot reload (frontend)

Para recarregamento automático de tela durante desenvolvimento, suba também o serviço Node/Vite:

```bash
docker compose up -d node
```

Depois disso, ao editar arquivos Blade, CSS ou JS, o navegador recarrega automaticamente.

## Serviços Docker

- `nginx`: proxy web na porta `8080`
- `app`: PHP-FPM + Composer
- `node`: Vite dev server (HMR) na porta `5173`
- `mysql`: banco principal
- `redis`: cache/session/queue
- `mailpit`: captura de e-mails em dev

## Credenciais de demo

Usuários seedados:

- Admin: `admin@graficaimpacta.local` / `password`
- Cliente: `cliente@graficaimpacta.local` / `password`

Banco MySQL:

- Database: `grafica_ecommerce`
- User: `laravel`
- Password: `secret`
- Root password: `root`

## Estrutura implementada

- Catálogo:
  - `categories`
  - `products`
  - `product_variants`
  - `product_images`
- Cliente e endereços:
  - `users` (com campos extras)
  - `addresses`
- Carrinho:
  - fluxo em sessão (`CartService`)
  - tabelas `carts` e `cart_items` preparadas para persistência futura
- Pedidos:
  - `orders`
  - `order_items`
  - `order_status_histories`
- Pagamentos:
  - `payments` (Mercado Pago / manual)
- Produção gráfica:
  - `artwork_files` (upload, checklist e revisão de arte)

## Fluxo disponível

1. Home -> catálogo
2. Produto -> selecionar variação
3. Adicionar ao carrinho
4. Checkout (contato, endereço, frete/retirada, pagamento)
5. Criação de pedido com número único e cobrança
6. Upload de arte final por item (área do cliente)
7. Revisão de arte + workflow no painel admin

## Configurações importantes (cobrança e frete)

No arquivo `app/.env`:

- `STOREFRONT_PAYMENT_DRIVER=mercadopago` para ativar cobrança real via Mercado Pago (PIX/cartão/boleto)
- `MERCADOPAGO_ACCESS_TOKEN=` token da conta (teste ou produção)
- `MERCADOPAGO_WEBHOOK_TOKEN=` token simples para validar o webhook da rota `/webhooks/mercado-pago`
- `STOREFRONT_SHIPPING_DRIVER=melhor_envio` para usar cotação real por CEP
- `MELHOR_ENVIO_TOKEN=` token da API Melhor Envio
- `STOREFRONT_PICKUP_*` para configurar retirada no balcão

Se não configurar tokens, o sistema usa fallback local:

- Cobrança manual (pedido criado com pagamento pendente)
- Frete local por CEP (simulação para desenvolvimento)

## Próximos módulos recomendados (alto padrão)

1. Cartão no próprio checkout (tokenização + antifraude)
2. Aprovação de prova digital (layout final com aceite)
3. Etiquetas de envio e rastreio no painel
4. Regras comerciais (cupom, tabela B2B, desconto por volume)
5. Painel financeiro (baixas, conciliação, 2ª via)

## Feature adicionada nesta rodada

Foi adicionada uma melhoria operacional pequena e útil para a gráfica: ao revisar uma arte no painel admin, o sistema publica automaticamente um aviso no chat do pedido com o status da revisão e as observações da equipe.

Também foi adicionada a mesma lógica ao bloco **Atualizar status do pedido**: quando o admin muda o workflow, o chat recebe uma mensagem com status do pedido, pagamento, produção e observações da equipe. Isso conecta pré-impressão, produção e cliente dentro do fluxo que já existia.

Arquivos principais:

- `app/app/Http/Controllers/Admin/ArtworkFileReviewController.php`
- `app/app/Http/Controllers/Admin/OrderController.php`
- `app/tests/Feature/PrintShopOperationalFlowTest.php`
- `docs/DOCUMENTACAO_DO_PROJETO.md`

## Validação executada nesta rodada

- `docker compose exec -T app php -l app/Http/Controllers/Admin/ArtworkFileReviewController.php`
- `docker compose exec -T app php -l tests/Feature/PrintShopOperationalFlowTest.php`
- `docker compose exec -T app php artisan test --filter=PrintShopOperationalFlowTest`
- `docker compose exec -T app php artisan test`
