# Aplicacao Laravel - E-commerce para Grafica

Esta pasta contem a aplicacao Laravel do e-commerce da grafica Uriah Criativa.

Documentacao completa do projeto:

- [`../README.md`](../README.md)
- [`../docs/DOCUMENTACAO_DO_PROJETO.md`](../docs/DOCUMENTACAO_DO_PROJETO.md)

## Principais modulos

- Loja publica com home, catalogo, carrinho e checkout.
- Frete com Melhor Envio ou cotacao local.
- Pagamento manual ou Mercado Pago.
- Area do cliente com pedidos, upload de arte e chat do pedido.
- Painel admin com dashboard, pedidos, workflow, revisao de arte, catalogo, banners, blog, portfolio, contatos e atendimento online.
- Chat flutuante com rastreio de visitantes e mensagens offline.

## Comandos uteis

Na raiz do repositorio:

```bash
docker compose up -d --build
docker compose exec -T app php artisan migrate --seed --force
docker compose exec -T app php artisan test
```

Para hot reload:

```bash
docker compose up -d node
```
