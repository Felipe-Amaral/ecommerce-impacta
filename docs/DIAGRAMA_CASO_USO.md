# Diagrama de Caso de Uso

Este diagrama mostra os principais atores do sistema e o que cada um pode fazer no e-commerce da grafica.

```mermaid
flowchart LR
    Visitante(["Visitante"])
    Cliente(["Cliente autenticado"])
    Admin(["Admin / Equipe da grafica"])
    MercadoPago(["Mercado Pago"])
    MelhorEnvio(["Melhor Envio"])
    Reverb(["Reverb"])

    subgraph LojaPublica["Loja publica"]
        UC01(["Navegar pela home"])
        UC02(["Consultar catalogo"])
        UC03(["Buscar e filtrar produtos"])
        UC04(["Ver detalhes do produto"])
        UC05(["Adicionar item ao carrinho"])
        UC06(["Calcular frete"])
        UC07(["Finalizar checkout"])
        UC08(["Enviar formulario de contato"])
        UC09(["Ler blog"])
        UC10(["Ver portfolio"])
    end

    subgraph ContaCliente["Area do cliente"]
        UC11(["Criar conta"])
        UC12(["Entrar no sistema"])
        UC13(["Acessar dashboard"])
        UC14(["Listar pedidos"])
        UC15(["Ver detalhe do pedido"])
        UC16(["Enviar arte final"])
        UC17(["Acompanhar status do pedido"])
        UC18(["Conversar no chat do pedido"])
    end

    subgraph Atendimento["Atendimento online"]
        UC19(["Abrir chat no site"])
        UC20(["Enviar mensagem ao atendimento"])
        UC21(["Deixar mensagem offline"])
        UC22(["Monitorar visitantes ativos"])
        UC23(["Responder chat online"])
        UC24(["Encerrar atendimento"])
    end

    subgraph PainelAdmin["Painel administrativo"]
        UC25(["Visualizar dashboard operacional"])
        UC26(["Gerenciar pedidos"])
        UC27(["Atualizar workflow do pedido"])
        UC28(["Revisar arte final"])
        UC29(["Enviar aviso automatico no chat"])
        UC30(["Gerenciar produtos"])
        UC31(["Gerenciar categorias"])
        UC32(["Gerenciar banners"])
        UC33(["Gerenciar blog"])
        UC34(["Gerenciar portfolio"])
        UC35(["Gerenciar contatos"])
    end

    subgraph Integracoes["Integracoes externas"]
        UC36(["Inicializar pagamento"])
        UC37(["Receber webhook de pagamento"])
        UC38(["Consultar cotacao de frete"])
        UC39(["Transmitir mensagens em tempo real"])
    end

    Visitante --> UC01
    Visitante --> UC02
    Visitante --> UC03
    Visitante --> UC04
    Visitante --> UC05
    Visitante --> UC06
    Visitante --> UC07
    Visitante --> UC08
    Visitante --> UC09
    Visitante --> UC10
    Visitante --> UC11
    Visitante --> UC12
    Visitante --> UC19
    Visitante --> UC20
    Visitante --> UC21

    Cliente --> UC12
    Cliente --> UC13
    Cliente --> UC14
    Cliente --> UC15
    Cliente --> UC16
    Cliente --> UC17
    Cliente --> UC18
    Cliente --> UC07

    Admin --> UC25
    Admin --> UC26
    Admin --> UC27
    Admin --> UC28
    Admin --> UC29
    Admin --> UC30
    Admin --> UC31
    Admin --> UC32
    Admin --> UC33
    Admin --> UC34
    Admin --> UC35
    Admin --> UC22
    Admin --> UC23
    Admin --> UC24

    UC07 --> UC36
    UC36 --> MercadoPago
    MercadoPago --> UC37

    UC06 --> UC38
    UC38 --> MelhorEnvio

    UC18 --> UC39
    UC20 --> UC39
    UC23 --> UC39
    UC39 --> Reverb

    UC27 --> UC29
    UC28 --> UC29
```

## Atores

- `Visitante`: pessoa que acessa o site sem necessariamente estar logada.
- `Cliente autenticado`: usuario cadastrado que compra e acompanha pedidos.
- `Admin / Equipe da grafica`: usuario interno que gerencia catalogo, pedidos, artes, atendimento e conteudo.
- `Mercado Pago`: gateway usado para pagamento quando configurado.
- `Melhor Envio`: servico usado para cotacao de frete quando configurado.
- `Reverb`: servico de mensagens em tempo real usado no chat.

## Casos de uso principais

- O visitante navega pelo catalogo, adiciona produtos ao carrinho, calcula frete e finaliza o checkout.
- O cliente acompanha pedidos, envia arte final e conversa com a equipe pelo chat do pedido.
- O admin revisa artes, atualiza o workflow do pedido e gerencia o catalogo.
- Quando o admin revisa uma arte ou altera o workflow, o sistema envia um aviso automatico no chat do pedido.
- O atendimento online permite acompanhar visitantes ativos e responder mensagens em tempo real.

## Fluxo de compra resumido

```text
Visitante/Cliente
-> consulta catalogo
-> adiciona produto ao carrinho
-> calcula frete
-> finaliza checkout
-> pedido e criado
-> cliente envia arte
-> admin revisa arte
-> admin atualiza workflow
-> cliente recebe avisos no chat do pedido
```
