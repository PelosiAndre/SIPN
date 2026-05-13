<div align="center">

<img src="assets/img/logo.png" alt="SIPN Logo" width="300"/>

# SIPN

**Sistema Integrado de Profissionalização e Negócios**

[![Status](https://img.shields.io/badge/status-concluído-blue?style=for-the-badge)](#)

*Projeto acadêmico desenvolvido para simular uma plataforma de cursos online.*

</div>

---

## Sobre o Projeto

> **⚠️ Aviso:** Este sistema foi construído exclusivamente como um trabalho de faculdade. Todo o conteúdo presente na plataforma (cursos, vídeos, instituições parceiras e certificados) é **fictício** e utilizado apenas para fins de demonstração técnica.

O **SIPN** é um Sistema de Gestão de Aprendizagem (LMS) desenvolvido para centralizar e facilitar o acesso à educação profissionalizante gratuita. A plataforma integra alunos a instituições parceiras, permitindo o consumo de conteúdo em vídeo, controle automatizado de progresso e emissão de certificados com validação pública.

Este sistema foi desenvolvido inteiramente com tecnologias nativas (Vanilla JS e PHP), focando em performance, modelagem relacional eficiente e uma arquitetura limpa sem a dependência de frameworks externos pesados.

### Funcionalidades

- **Portal do Aluno** — dashboard personalizado com catálogo de cursos, progresso em porcentagem e histórico de matrículas.
- **Player Inteligente** — integração com a API do YouTube Iframe para marcação automática de aulas concluídas assim que o vídeo termina.
- **Geração de Certificados** — emissão de certificados em formato PDF perfeitamente formatados para impressão, diretamente pelo navegador.
- **Portal Administrativo** — painel do funcionário para gestão completa (CRUD) de cursos, entidades parceiras e contas de usuários.
- **Exclusão Segura** — mecanismo de proteção para ações destrutivas (deleção de cursos/usuários) exigindo um código de autorização gerado dinamicamente por sessão.
- **Verificador de Autenticidade** — página pública que permite a recrutadores e empresas validarem os certificados emitidos através do código de autenticação único.

---

## Tecnologias

| Camada | Tecnologia |
|--------|-----------|
| Front-end | HTML5, CSS3 (Variáveis CSS, Flexbox) |
| Interatividade | JavaScript ES6 (puro, sem frameworks) |
| Back-end | PHP 8.x (PDO para comunicação segura) |
| Banco de Dados | MySQL (Modelagem relacional) |
| Player de Vídeo | YouTube Iframe Player API |

---

## Como Usar

O projeto requer um servidor local com suporte a PHP e MySQL (como XAMPP, WAMP ou Laragon).

1. Clone o repositório na sua pasta pública do servidor (ex: `htdocs` ou `www`):
```bash
git clone https://github.com/PelosiAndre/SIPN.git
```

2. Configure o banco de dados:
- Crie um banco de dados chamado `sipn_db` no seu gerenciador (phpMyAdmin, DBeaver, etc).
- Importe o script SQL fornecido (que contém a estrutura das tabelas e dados iniciais para teste).

3. Configure a conexão:
- Verifique o arquivo `controller/conexao.php` e ajuste o usuário/senha do banco de dados conforme o seu ambiente local.

> **Nota:** Todos os usuários de teste (alunos e funcionários) criados no script SQL inicial possuem a senha padrão: `password`.

---

Projeto desenvolvido para a disciplina de **Linguagem de Programação IV** — FATEC Presidente Prudente, 4º termo (2026).

## 👥 Autoria

- André Rego Pelosi
